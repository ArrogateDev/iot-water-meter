<?php

namespace App\Services;

use App\Constants\ResponseCode;
use App\Constants\WebSocketResponseCode;
use App\Constants\WebSocketResponseType as ResponseType;
use App\Exceptions\WebSocket\AuthFailException;
use App\Exceptions\WebSocket\TokenExpiredException;
use App\Models\Manage\Admin;
use App\Models\WebSocket\AdminWebSocket;
use App\Models\WebSocket\AdminWebSocketGrouping;
use App\Models\WebSocket\WebSocketGrouping;
use App\Traits\WebSocketResponseTrait;
use Carbon\Carbon;
use GatewayWorker\Lib\Gateway;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Workerman\Timer;

class WebSocket
{
    use WebSocketResponseTrait;

    /**
     * 登录
     *
     * @param $client_id
     * @param array $params
     * @return bool
     * @throws AuthFailException
     * @throws TokenExpiredException
     */
    public static function onLogin($client_id, array $params): bool
    {
        $type = ResponseType::AUTH_SUCCESS;
        if (self::isOnline($client_id)) {
            self::sendToClient($client_id, self::responseSuccess($type, [], '已认证成功!'));
            return true;
        }

        if (!Arr::has($params, ['device', 'token'])) {//, 'session'
            throw new AuthFailException('参数错误!', 1000);
        }

        $device = $params['device'] ?? null;
        $token = $params['token'] ?? null;
        $session = $params['session'] ?? null;
        if (!in_array($device, ['android', 'ios']) || empty($token)) {
            throw new AuthFailException('参数错误!!', ResponseCode::PARAM_ERR);
        }

        try {
            $decrypted = Crypt::decryptString($token);
            $decrypted_params = json_decode($decrypted, true);

            if (!Arr::has($decrypted_params, ['id', 'expire_time'])) {
                throw new AuthFailException('参数错误!!!', 1000);
            }

            if (Cache::get(sprintf('websocket_token:%s', $decrypted_params['id'])) !== $token) {
                throw new TokenExpiredException('Token失效', WebSocketResponseCode::TOKEN_EXPIRED);
            }

            $now = Carbon::now();
            if (Carbon::parse($decrypted_params['expire_time'])->lt($now)) {
                throw new TokenExpiredException('Token已过期', WebSocketResponseCode::TOKEN_EXPIRED);
            }

            $id = $decrypted_params['id'] ?? null;
            $user = Admin::query()
                ->where('id', $id)
                ->notDelete()
                ->firstOr(function () {
                    throw new AuthFailException('用户不存在', ResponseCode::PARAM_ERR);
                });

            if ($user->state !== Admin::NORMAL) {
                throw new AuthFailException('用户已禁用', ResponseCode::PARAM_ERR);
            }

            $websocket = [
                'client_id' => $client_id,
                'is_online' => AdminWebSocket::ONLINE,
                'login_at' => Carbon::now()->toDateTimeString(),
                'ip' => $session['HTTP_X_REAL_IP'] ?? '0.0.0.0',
            ];

            $user->load(['websocket', 'websocket_grouping_info']);

            $websocket_grouping_info = $user->websocket_grouping_info;
            $device_grouping = $websocket_grouping_info->firstWhere('type', 'device');
            if ($device_grouping && $device_grouping->mark != $device) {
                AdminWebSocketGrouping::query()->where(['admin_id' => $user->id, 'grouping_id' => $device_grouping->id])->delete();
                $grouping_id = WebSocketGrouping::query()->where(['type' => 'device', 'mark' => $device])->value('id');
                $join_device_grouping = new AdminWebSocketGrouping();
                $join_device_grouping->admin_id = $user->id;
                $join_device_grouping->grouping_id = $grouping_id;
                $join_device_grouping->save();
                Gateway::joinGroup($client_id, $grouping_id);
            } elseif (!$device_grouping) {
                $grouping_id = WebSocketGrouping::query()->where(['type' => 'device', 'mark' => $device])->value('id');
                $join_device_grouping = new AdminWebSocketGrouping();
                $join_device_grouping->admin_id = $user->id;
                $join_device_grouping->grouping_id = $grouping_id;
                $join_device_grouping->save();
                Gateway::joinGroup($client_id, $grouping_id);
            }

            if ($user->websocket instanceof AdminWebSocket) {
                $websocket['last_login_at'] = $user->websocket->login_at;
                $user->websocket()->update($websocket);
            } else {
                $user->websocket()->create($websocket);
            }

            self::onBindUid($client_id, $user->id);
            Redis::sadd('websocket_auth', $client_id);
            Timer::del($session['auth_timer_id']);
            unset($session['auth_timer_id']);
            Gateway::setSession($client_id, $session);
            self::sendToClient($client_id, self::responseSuccess($type, [], '认证成功!'));
            return true;
        } catch (DecryptException $e) {
            Log::error($e);
            throw new AuthFailException('参数错误', ResponseCode::PARAM_ERR);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 心跳
     *
     * @param $client_id
     * @param array $params
     * @return bool
     */
    public static function onHeartbeat($client_id, array $params): bool
    {
        self::sendToClient($client_id, self::responseSuccess(ResponseType::PING_OK));
        return true;
    }

    /**
     * 是否在线
     *
     * @param string $client_id
     * @return bool
     */
    public static function isOnline(string $client_id)
    {
        return Redis::sismember('websocket_auth', $client_id) && Gateway::isOnline($client_id) > 0;
    }
}
