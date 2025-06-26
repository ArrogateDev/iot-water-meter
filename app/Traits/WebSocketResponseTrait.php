<?php

namespace App\Traits;

use App\Constants\WebSocketResponseCode;
use Carbon\Carbon;
use GatewayWorker\Lib\Gateway;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

trait WebSocketResponseTrait
{

    /**
     * 成功响应
     *
     * @param string $type
     * @param array $content
     * @param string $message
     * @return string
     */
    public static function responseSuccess(string $type, array $content = [], string $message = 'ok'): string
    {
        return static::makeResponseData(
            [
                'type' => $type,
                'content' => (object)$content,
                'message' => $message,
                'server_time' => Carbon::now()->toDateTimeString(),
                'code' => WebSocketResponseCode::SUCCESS
            ]
        );
    }

    /**
     * 错误响应.
     *
     * @param string $type
     * @param string $message
     * @param int $code
     * @return string
     */
    public static function responseError(string $type, string $message, int $code): string
    {
        return static::makeResponseData(
            [
                'type' => $type,
                'content' => (object)[],
                'message' => $message,
                'code' => $code
            ]
        );
    }

    /**
     * 格式化返回数据
     *
     * @param array $data
     * @return string
     */
    public static function makeResponseData(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $client_id
     * @param string $notification
     * @return void
     */
    public static function sendToClient(string $client_id, string $notification)
    {
        env('WEB_SOCKET_LOG') && Log::channel('web_socket_log')->info('发送：' . $client_id, json_decode($notification, true));
        !empty($client_id) && Gateway::isOnline($client_id) > 0 && Gateway::sendToClient($client_id, $notification);
    }

    /**
     * @param string $group
     * @param string $notification
     * @return void
     */
    public static function sendToGroup(string $group, string $notification)
    {
        env('WEB_SOCKET_LOG') && Log::channel('web_socket_log')->info('发送：' . $group, json_decode($notification, true));
        !empty($group) && Gateway::sendToGroup($group, $notification);
    }

    /**
     * 绑定uid
     *
     * @param string $client_id
     * @param int $user_id
     * @return void
     */
    public static function onBindUid(string $client_id, int $user_id)
    {
        Gateway::bindUid($client_id, $user_id);
        Cache::put($client_id, $user_id);
    }

    /**
     * @param int $user_id
     * @return false|mixed
     */
    public static function getClientIdByUidOne(int $user_id)
    {
        $client_ids = Gateway::getClientIdByUid($user_id);
        array_filter($client_ids, function ($client_id) {
            return Gateway::isOnline($client_id) > 0;
        });
        return end($client_ids);
    }

    /**
     * @param $client_id
     * @param $key
     * @param $value
     * @return void
     */
    public static function onUpdateClientData($client_id, $key, $value)
    {
        Redis::sadd($key . ':' . $client_id, $value);
    }
}
