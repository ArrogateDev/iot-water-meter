<?php

namespace App\Events;

use App\Constants\WebSocketResponseCode;
use App\Constants\WebSocketResponseType as ResponseType;
use App\Exceptions\WebSocket\AuthFailException;
use App\Exceptions\WebSocket\TokenExpiredException;
use App\Exceptions\WebSocket\WebSocketException;
use App\Models\WebSocket\AdminWebSocket;
use App\Services\WebSocket;
use App\Traits\WebSocketResponseTrait;
use GatewayWorker\Lib\Gateway;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Workerman\Timer;


class WorkermanEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels, WebSocketResponseTrait;

    protected static $types = [
        'login', 'heartbeat'
    ];

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param string $client_id 连接id
     */
    public static function onConnect(string $client_id)
    {
        // 连接到来后，定时30秒关闭这个链接，需要30秒内发认证并删除定时器阻止关闭连接的执行
        // $auth_timer_id = Timer::add(30, function ($client_id) {
        //     $notification = self::responseError(ResponseType::AUTH_FAIL, '认证失败，关闭连接', WebSocketResponseCode::AUTH_FAIL);
        //     WebSocketResponseTrait::sendToClient($client_id, $notification);
        //     Gateway::closeClient($client_id);
        //     Session::remove('HTTP_X_REAL_IP');
        // }, [$client_id], false);
        // Gateway::updateSession($client_id, ['auth_timer_id' => $auth_timer_id]);
        // $message = self::responseError('auth', sprintf("Hello,%s,请进行认证！", $client_id), WebSocketResponseCode::AUTH);
        // WebSocketResponseTrait::sendToClient($client_id, $message);
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     *
     * @param string $client_id
     * @param $http_buffer
     */
    public static function onWebSocketConnect(string $client_id, $http_buffer)
    {
        // $http_x_real_ip = $http_buffer['server']['HTTP_X_REAL_IP'] ?? '0.0.0.0';
        // $key = sprintf('websocket_ip:%s', $http_x_real_ip);
        // if (Cache::has($key)) {
        //     $num = Cache::increment($key);
        // } else {
        //     $num = 0;
        //     Cache::put($key, $num, now()->endOfDay());
        // }

        // if ($num > 10000) {
        //     Gateway::closeClient($client_id);
        // }

        // Gateway::updateSession($client_id, ['HTTP_X_REAL_IP' => $http_x_real_ip]);
        $notification['type'] = 'auth_success';
        self::sendToClient($client_id, json_encode($notification));
    }

    /**
     * 当客户端发来消息时触发
     * @param string $client_id 连接id
     * @param mixed $message 具体消息
     */
    public static function onMessage(string $client_id, $message)
    {
        print_r($message);
        // $temp = $message;
         $message = json_decode($message, true);
        // env('WEB_SOCKET_LOG') && Log::channel('web_socket_log')->info('接收：' . $client_id, $message);

        // $has_closed = false;
        // try {

        //     if ($message == $temp || count($message) != 2) {
        //         throw new WebSocketException('非法操作', WebSocketResponseCode::ILLEGAL_OPERATION, ResponseType::ILLEGAL_OPERATION);
        //     }

        //     if (!Arr::has($message, ['type', 'params'])) {
        //         throw new WebSocketException('参数错误', WebSocketResponseCode::ILLEGAL_OPERATION, ResponseType::ILLEGAL_OPERATION);
        //     }

        $type = $message['type'];
        if (!in_array($type, self::$types)) {
            throw new WebSocketException('参数错误!', WebSocketResponseCode::ILLEGAL_OPERATION, ResponseType::ILLEGAL_OPERATION);
        }

        //     if (!(($lock = Cache::lock("submit_{$type}_lock:$client_id", 60))->get())) {
        //         throw new WebSocketException('操作频繁，请稍后再试', WebSocketResponseCode::ILLEGAL_OPERATION, ResponseType::ILLEGAL_OPERATION);
        //     }

        //     // 请求结束后关闭锁
        //     def($_Context, function () use (&$lock) {
        //         $lock->release();
        //     });

        //     if ($type !== 'login' && (Gateway::isOnline($client_id) <= 0 || !WebSocket::isOnline($client_id))) {
        //         throw new WebSocketException('请先认证!', WebSocketResponseCode::AUTH, ResponseType::TOKEN_EXPIRED);
        //     }

        $params = $message['params'];

        $type = Str::studly($type);
        //     $type === 'Login' && $params['session'] = Gateway::getSession($client_id);
        $method = sprintf('on%s', $type);
        WebSocket::{$method}($client_id, $params);
        // } catch (AuthFailException $e) {
        //     $has_closed = true;
        //     $notification = self::responseError(ResponseType::AUTH_FAIL, $e->getMessage(), $e->getCode());
        // } catch (TokenExpiredException $e) {
        //     $notification = self::responseError(ResponseType::TOKEN_EXPIRED, $e->getMessage(), $e->getCode());
        // } catch (WebSocketException $e) {
        //     $notification = self::responseError($e->getType(), $e->getMessage(), $e->getCode());
        // } catch (\PDOException $e) {
        //     Log::error($e);
        //     $notification = self::responseError(ResponseType::SERVER_CONGESTION, __('服务器拥堵'), WebSocketResponseCode::FAIL);
        // } catch (\Exception $e) {
        //     Log::error($e);
        //     $notification = self::responseError(ResponseType::SERVER_ERR, __('服务器异常'), WebSocketResponseCode::FAIL);
        // } finally {
        //     if (isset($e, $notification)) {
        //         WebSocketResponseTrait::sendToClient($client_id, $notification);
        //     }
        //     if ($has_closed) {
        //         Gateway::closeClient($client_id);
        //     }
        // }
    }

    /**
     * 当用户断开连接时触发
     * @param string $client_id 连接id
     * @throws WebSocketException
     */
    public static function onClose(string $client_id)
    {
        // $user_id = Cache::pull($client_id);
        // if (!WebSocket::isOnline($client_id)) {
        //     Redis::srem('websocket_auth', $client_id);
        //     $user_id && AdminWebSocket::query()
        //         ->where('admin_id', $user_id)
        //         ->where('is_online', AdminWebSocket::ONLINE)
        //         ->update(['is_online' => AdminWebSocket::OFFLINE]);
        // }

        // env('WEB_SOCKET_LOG') && Log::channel('web_socket_log')->info('断开：' . $client_id . ':' . $user_id);
    }
}
