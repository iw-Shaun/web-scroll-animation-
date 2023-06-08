<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use App\LineUtils\LINEBotExt;
use App\Common\AppError;

/**
 * Line bot api 集合
 * 
 */

class LineBotApi
{

    private $bot;

    public function __construct()
    {   
        $httpClient = new CurlHTTPClient(config('services.line.channelAccessToken'));
        $this->bot = new LINEBotExt($httpClient, ['channelSecret' => config('services.line.channelSecret')]);
    }

    public function getGroupSummary($groupId)
    {
        $res = $this->bot->getGroupSummary($groupId);
        if ($res->isSucceeded()) {
            $resArray = $res->getJSONDecodedBody();
            return $resArray;
        }

        return [
            'groupId' => '',
            'groupName' => '',
            'pictureUrl' => ''
        ];
    }

    /**
     * @param string $replyToken string
     * @param mixed $msg string|object
     */
    public function replyMessage($replyToken, $msg) {
        // Log::debug("replyMessage");
        $res = null;
        if (gettype($msg) == 'string') {
            $res = $this->bot->replyText($replyToken, $msg);
        } else {
            $res = $this->bot->replyMessage($replyToken, $msg);
        }

        return $this->resHandler($res, true);
    }

    /**
     * @param string $lineId
     * @param mixed $msg
     */
    public function pushMessage($lineId, $msg) {
        Log::debug("pushMessage");
        $res = null;
        if (gettype($msg) == 'string') {
            $msg = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($msg);
            $res = $this->bot->pushMessage($lineId, $msg);
        } else {
            $res = $this->bot->pushMessage($lineId, $msg);
        }

        return $this->resHandler($res, false, $lineId);
    }

    private function resHandler($res, $isReply, $lineId=null)
    {
        $userInfo = '';
        if ($lineId) {
            $userInfo = " line_id=[{$lineId}]";
        }

        // Check response.
        if ($res->isSucceeded()) {
            Log::info(($isReply ? 'Reply' : 'Push') . ' successfully.' . $userInfo);
            return true;
        } else {
            $httpStatusCode = $res->getHTTPStatus();
            $errorMessage = $res->getRawBody();
            Log::error(($isReply ? 'Reply' : 'Push') . " error:  $errorMessage ($httpStatusCode)." . $userInfo);

            $appErrorCode = AppError::LINEBOT_UNHANDLED_EXCEPTION;
            switch ($httpStatusCode) {
                case 401:
                    $appErrorCode = AppError::INVALID_LINEBOT_TOKEN;
                    break;
                case 400:
                    $appErrorCode = AppError::INVALID_LINEBOT_TOKEN;
                    break;
                case 429:
                    $appErrorCode = AppError::INVALID_LINEBOT_TOKEN;
                    break;
            }
            return [
                'code' => $appErrorCode,
                'message' => "$errorMessage ($httpStatusCode)."
            ];
        }
    }
}
