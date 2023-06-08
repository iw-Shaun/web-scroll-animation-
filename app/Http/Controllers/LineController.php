<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Line\RichMenuController;
use App\Http\Controllers\LineCustomMessageBuilder;
use App\Http\Controllers\Line\TextEventController;
use App\Http\Controllers\Line\PostbackController;
use App\Models\RichMenu;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Event\PostbackEvent;
use LINE\LINEBot\Exception\InvalidSignatureException;
use LINE\LINEBot\Exception\InvalidEventRequestException;

class LineController extends Controller
{
    private $bot;

    const HTTP_TIMEOUT = 30;

    const API_GET_PROFILE = '/v2/profile';

    public function __construct()
    {
        $httpClient = new CurlHTTPClient(config('services.line.channelAccessToken'));
        $this->bot = new LINEBot($httpClient, ['channelSecret' => config('services.line.channelSecret')]);
    }

    public function pushTextMessage($lineId, $text_message) {
        $msg =  new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text_message);
        $res = $this->bot->pushMessage($lineId, $msg);

        if ($res->isSucceeded()) {
            Log::info("pushMessage() success: line_id=[{$lineId}]");
            return true;
        } else {
            $httpStatusCode = $res->getHTTPStatus();
            $errorMessage = $res->getRawBody();
            Log::error("pushMessage() error:  msg=[{$errorMessage}] httpStatus=[({$httpStatusCode})] line_id=[{$lineId}]");
            echo $errorMessage;
            return false;
        }
    }

    public function pushMessage($lineId, $json_messages) {
        $msg = new LineCustomMessageBuilder($json_messages);
        $res = $this->bot->pushMessage($lineId, $msg);

        if ($res->isSucceeded()) {
            Log::info("pushMessage() success: line_id=[{$lineId}]");
            return true;
        } else {
            $httpStatusCode = $res->getHTTPStatus();
            $errorMessage = $res->getRawBody();
            Log::error("pushMessage() error:  msg=[{$errorMessage}] httpStatus=[({$httpStatusCode})] line_id=[{$lineId}]");
            return false;
        }
    }

    public function getProfile($accessToken)
    {
        $headers = [
            'Authorization' => "Bearer {$accessToken}",
        ];

        $client = new Client([
            'base_uri' => 'https://api.line.me',
            'timeout'  => self::HTTP_TIMEOUT,
        ]);
        try {
            $response = $client->request('GET', self::API_GET_PROFILE, [
                'headers' => $headers,
            ]);
            $code = $response->getStatusCode();
            $body = $response->getBody();
            $data = json_decode($body, true);
            return $data;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $res = $e->getResponse();
                $code = $res->getStatusCode();
                $body = $res->getBody();
                Log::error("Request [".self::API_GET_PROFILE."] fail, http_code=[{$code}], reason: {$body}");
            }
            return null;
        } catch (ConnectException $e) {
            Log::error("Request [".self::API_GET_PROFILE."] fail, http connection error.");
            return null;
        }
    }

    public function webhookVerify(Request $request)
    {
        return response()->json(['data' => 'Success']);
    }

    public function webhookNotify(Request $request)
    {
        $body = $request->getContent();
        $signature = $request->header('X-Line-Signature');

        // Check signature and get events.
        Log::debug($body);
        try {
            $events = $this->bot->parseEventRequest($body, $signature);
        } catch (InvalidSignatureException $e) {
            return response()->json(['error' => ['message' => 'Invalid signature']], 400);
        } catch (InvalidEventRequestException $e) {
            return response()->json(['error' => ['message' => 'Invalid event']], 400);
        }

        // Process events.
        foreach ($events as $event) {
            if ($event instanceof TextMessage) {
                new TextEventController($this->bot, $event);
            } else if ($event instanceof PostbackEvent) {
                new PostbackController($this->bot, $event);
            } else {
                Log::debug('Unhandled event type: ' . get_class($event));
            }
        }

        // Respose with http 200 to let the LINE server knows we've received the message.
        return response('');
    }

    public function richMenuCommands(Request $request) {
        $r = new RichMenuController();
        $action = $request->input('action');
        switch ($action) {
            case 'get_richmenus':
                $data = $r->getRichMenuList();
                break;
            case 'get_default_richmenu':
                $data = $r->getDefaultRichMenu();
                break;
            case 'set_default_richmenu':
                $data = null;
                $menuId = $request->input('menu_id');
                if ($menuId) {
                    $data = $r->setDefaultRichMenu($menuId);
                }
                break;
            case 'cancel_default_richmenu':
                $data = $r->cancelDefaultRichMenu();
                break;
            case 'create_all_richmenus':
                $forceCreateNew = $request->input('force') ? true : false;
                $data = $r->createOrUpdateAllRichMenus($forceCreateNew);
                break;
            case 'delete_all_richmenus':
                $data = $r->deleteAllRichMenus();
                break;
            case 'delete_richmenu':
                $data = null;
                $menuId = $request->input('menu_id');
                if ($menuId) {
                    $data = $r->deleteRichMenuById($menuId);
                }
                break;
            case 'relink_all_users_richmenu':
                // Scan all users, and relink for those users' rich menu does not exist.
                $menus = RichMenu::all()->toArray();
                $menuIds = array_map(function($m) { return $m['uuid']; }, $menus);
                $userIds = User::select(['id'])->whereNotIn('rich_menu_id', $menuIds)->get()->pluck('id')->toArray();
                $userCount = count($userIds);
                $processCount = 0;
                $startTs = time();
                foreach ($userIds as $userId) {
                    $user = User::find($userId);
                    if ($user) {
                        UserController::resetUserRichMenu($user);
                    }
                    $processCount++;

                    // Prevent gateway timeout.
                    $now = time();
                    if ($now - $startTs > 50) {
                        break;
                    }
                }
                $data = "Process: {$processCount}, total: {$userCount}";
                break;
            case 'bulk_relink_all_users_richmenu':
                $data = [];
                $r = new RichMenuController();
                $menus = RichMenu::all()->toArray();
                foreach ($menus as $menu) {
                    // Find those users with invalid rich menu uuid.
                    $lineIds = User::select(['line_id'])->where([
                        ['rich_menu_name', $menu['name']],
                        ['rich_menu_id', '!=', $menu['uuid']],
                    ])->get()->pluck('line_id')->toArray();

                    $chunks = array_chunk($lineIds, 500); // At most link 500 users' rich menu.
                    foreach ($chunks as $chunk) {
                        $userCount = count($chunk);
                        $result = $r->bulkLinkRichMenuByIdAndName($chunk, $menu['uuid'], $menu['name']);
                        $data[] = "bulk link [{$menu['name']}] to [{$userCount}] users, result={$result}";
                    }
                }
                break;
            default:
                return response()->json(['error' => ['message' => 'Undefined action']], 400);
                break;
        }
        return response()->json(['data' => $data]);
    }
}