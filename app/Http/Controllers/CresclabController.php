<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ConnectException;
use App\Http\Controllers\Controller;

const HTTP_TIMEOUT = 15;

class CresclabController extends Controller
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';

    private static function sendRequest($method, $api, $data=[])
    {
        $url = 'https://api.cresclab.com';

        if (config('app.env') == 'production') {
            $accessToken = config('services.cresclab.prod.token');
        } else {
            $accessToken = config('services.cresclab.dev.token');
        }

        $headers = [
            'Authorization' => "Bearer {$accessToken}",
        ];

        $client = new Client([
            'base_uri' => $url,
            'timeout'  => HTTP_TIMEOUT,
        ]);
        try {
            if ($method == self::METHOD_GET) {
                $response = $client->request($method, $api, [
                    'headers' => $headers,
                ]);
            } else {
                $response = $client->request($method, $api, [
                    'headers' => $headers,
                    'json' => $data,
                ]);
            }
            $code = $response->getStatusCode();
            $reason = $response->getReasonPhrase();
            $body = $response->getBody();
            $json = json_decode($body, true);
            Log::error("Request [{$method} {$api}] HTTP {$code} {$reason}");
            return $json;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $res = $e->getResponse();
                $code = $res->getStatusCode();
                $body = $res->getBody();
                Log::error("Request [{$method} {$api}] fail, http_code=[{$code}], reason: {$body}");
            }
        } catch (ConnectException $e) {
            Log::error("Request [{$method} {$api}] fail, http connection error.");
        }
    }

    public static function tagUser($lineId, $tagId)
    {
        $data = [
            'tag_id' => $tagId,
            'line_uid' => $lineId,
        ];
        $res = self::sendRequest(self::METHOD_POST, '/openapi/v1/taglinemember/', $data);
        if (isset($res['line_uid'])) {
            Log::debug("Tag [{$tagId}] to user[{$lineId}] success.");
            return true;
        }
        return false;
    }

    public static function untagUser($lineId, $tagId)
    {
        $data = [
            'tag_id' => $tagId,
            'line_uid' => $lineId,
        ];
        $res = self::sendRequest(self::METHOD_DELETE, '/openapi/v1/taglinemember/', $data);
        return true;
    }

    public static function tagUserByName($lineId, $tagName)
    {
        $tagId = null;
        $tag = CresclabController::findTag($tagName);
        if (!$tag) {
            $tag = CresclabController::createTag($tagName);
            if ($tag) {
                $tagId = $tag['id'];
            }
        } else {
            $tagId = $tag['id'];
        }
        if ($tagId) {
            return CresclabController::tagUser($lineId, $tagId);
        }
        return false;
    }

    public static function untagUserByName($lineId, $tagName)
    {
        $tag = CresclabController::findTag($tagName);
        if (!$tag) {
            $tag = CresclabController::createTag($tagName);
            if ($tag) {
                $tagId = $tag['id'];
            }
        } else {
            $tagId = $tag['id'];
        }
        if ($tagId) {
            return CresclabController::untagUser($lineId, $tagId);
        }
        return false;
    }

    public static function createTag($name)
    {
        $data = [
            'name' => $name,
        ];
        $res = self::sendRequest(self::METHOD_POST, '/openapi/v1/tag/', $data);
        return $res;
    }

    public static function findTag($name)
    {
        $tags = self::listTags($name);
        $index = array_search($name, array_column($tags, 'name')); // find the name fully matched.
        if ($index === false) {
            return null;
        }
        return $tags[$index];
    }

    public static function listTags($keyword=null)
    {
        $start = null;
        $tags = [];
        do {
            $params = [];
            if ($keyword) {
                $params['name'] = $keyword;
            }
            if ($start) {
                $params['start'] = $start;
            }
            $query = http_build_query($params);
            $res = self::sendRequest(self::METHOD_GET, "/openapi/v1/tag/?{$query}");
            $start = null; // Should clean the value.
            if ($res) {
                $tags = array_merge($tags, $res['results']);
                $start = $res['next'] ? urldecode($res['next']) : null;
            }
        } while ($start);
        return $tags;
    }
}
