<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use App\Http\Controllers\Session;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LineController;
use App\Models\User;
use App\Models\UserEvent;

class UserController extends Controller
{
    const HTTP_TIMEOUT = 30;

    public function auth(Request $request)
    {
        // Debug
        Log::info($_SERVER['REQUEST_URI']);
        Log::info('===auth===');

        // Set redirect url for bot_prompt=aggressive.
        $channelId = config('services.line.loginChannelId');
        $liffUrl = config('services.line.liffUrl');
        //$liffUrl .= '?oa=1';
        if ($request->has('image_id')) {
            $imageId = $request->input('image_id');
            $liffUrl .= "&image_id={$imageId}";
             Log::info('===auth image_id '.$imageId); //debug
        }
        $liffUrlEncode = urlencode($liffUrl);
        $url = "https://access.line.me/oauth2/v2.1/authorize?response_type=code&client_id={$channelId}&redirect_uri={$liffUrlEncode}&bot_prompt=aggressive&scope=profile&state=0";

        return view('auth', [
            'redirectUrl' => $url,
        ]);
    }

    public function login(Request $request)
    {
        $lineId = $request->post('line_id');
        $accessToken = $request->post('access_token');
        $friendship = $request->post('friendship');

        if (empty($lineId) || empty($accessToken)) {
            return response()->json(['error' => [
                'message' => 'missing parameter'
            ]], 400);
        }

        if (config('services.line.useMockUser') == false) {
            // Check the token is valid, and match to the line_id. (This can prevent forge $line_id)
            $lineController = new LineController();
            $profile = $lineController->getProfile($accessToken);
            if (!$profile) {
                return response()->json(['error' => [
                    'code' => 'Invalid token',
                ]], 400);
            } else if ($profile['userId'] != $lineId) {
                return response()->json(['error' => [
                    'code' => 'Invalid token pair',
                ]], 400);
            }
            $name = $profile['displayName'];
            $avatar_url = $profile['pictureUrl'];
        } else {
            $name = 'Test123';
            $avatar_url = 'https://gravatar.com/avatar/071bbb589ceaf46b0d0d0c2f856c697b?s=400&d=robohash&r=x';
        }

        $user = User::where('line_id', $lineId)->first();
        if (!$user) {
            $user = new User();
            $user->line_id = $lineId;
            $user->friendship = $friendship;
        } else if (!$user->friendship) {
            $user->is_new_friend = $user->friendship != $friendship;
        }

        // Update basic data.
        $user->name = $name;
        $user->avatar_url = $avatar_url;
        $user->loggedin_at = date('Y-m-d H:i:s');
        $user->save();

        // Set current user.
        Auth::login($user);

        //self::resetUserRichMenu($user);

        return response()->json(['data' => 'success']);
    }

    public function trackingEvent(Request $request)
    {
        $authUser = Auth::user();

        $ev = new UserEvent();
        $ev->user_id = $authUser->id;
        $ev->category = $request->input('category');
        $ev->action = $request->input('action');
        $ev->save();

        return response()->json(['data' => $ev]);
    }

    public function show(Request $request)
    {
        $authUser = Auth::user();
        $user = $authUser->toArray();
        // $fields = ['id', 'gender', 'birthday', 'county', 'district', 'products', 'infos', 'use_online_store', 'submitted_at'];
        // $user = array_intersect_key($authUser->toArray(), array_flip($fields));
        // if ($user['products']) {
        //     $user['products'] = json_decode($user['products']);
        // }
        // if ($user['infos']) {
        //     $user['infos'] = json_decode($user['infos']);
        // }
        return response()->json(['data' => $user]);
    }

    public function update(Request $request)
    {
        $authUser = Auth::user();

        $validator = Validator::make($request->all(), [
            'gender' => [Rule::in(['male', 'female', 'unisex'])],
            'birthday' => 'date|after:1931-1-1|before:2020-12-31',
            'county' => ['min:2', 'max:10', 'regex:/^(?!=).*/', 'regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u'], // Only allow Chinese and alphanum
            'district' => ['nullable', 'min:0', 'max:10', 'regex:/^(?!=).*/', 'regex:/^[\x{4e00}-\x{9fa5}a-zA-Z0-9]+$/u'],
            'products' => 'array',
            'products.*' => ['integer', 'between:1,8'],
            'infos' => 'array',
            'infos.*' => ['integer', 'between:1,5'],
            'use_online_store' => [Rule::in(['yes', 'no'])],
        ]);
        if ($validator->fails()) {
            // $validator->errors()->all()
            return response()->json(['error' => 'Invalid format'], 400);
        }

        $fillAllFields = true;
        $fields = ['gender', 'birthday', 'county', 'district', 'products', 'infos', 'use_online_store'];
        foreach ($fields as $field) {
            if ($request->has($field)) {
                if (in_array($field, ['products', 'infos'])) {
                    $arr = $request->post($field);
                    $arr = array_unique($arr);
                    $value = json_encode(array_values($arr));
                } else {
                    $value = $request->post($field);
                }
                $authUser->{$field} = $value;
            } else {
                // If one of field not fill, set flag to false.
                $fillAllFields = false;
            }
        }

        if ($fillAllFields) {
            $authUser->submitted_at = date('Y-m-d H:i:s');
        }

        $authUser->save();

        if ($authUser->submitted_at) {
            // Set rich menu.
            $r = new RichMenuController();
            $r->linkRichMenuByName($authUser->line_id, RichMenuTemplate::MENU_MEMBER);
        }

        return response()->json(['data' => 'Success']);
    }

    private function getLineOAFriendship($accessToken)
    {
        $headers = [
            'Authorization' => "Bearer {$accessToken}",
        ];

        $client = new Client([
            'base_uri' => 'https://api.line.me',
            'timeout'  => self::HTTP_TIMEOUT,
        ]);
        try {
            $response = $client->request('GET', '/friendship/v1/status', [
                'headers' => $headers,
            ]);
            $code = $response->getStatusCode();
            $body = $response->getBody();
            $json = json_decode($body, true);
            return $json['friendFlag'];
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $res = $e->getResponse();
                $code = $res->getStatusCode();
                $body = $res->getBody();
                Log::error("Request [/friendship/v1/status] fail, http_code=[{$code}], reason: {$body}");
            }
            return null;
        } catch (ConnectException $e) {
            Log::error("Request [/friendship/v1/status] fail, http connection error.");
            return null;
        }
    }
}
