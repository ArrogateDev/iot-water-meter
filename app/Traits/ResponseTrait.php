<?php

namespace App\Traits;

use App\Constants\ResponseCode;
use Illuminate\Support\Facades\Redis;

trait ResponseTrait
{
    /**
     * 成功响应
     *
     * @param object|array $data
     * @param string $message
     * @param $type 1|2
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseSuccess($data = null, $message = 'ok')
    {
        $headers = Redis::hgetall('api_data_caches');
        return response()->json(
            [
                'message' => $message,
                'code' => ResponseCode::SUCCESS,
                'data' => $data
            ],
            200,
            $headers
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }

    /**
     * 错误响应.
     *
     * @param string $message
     * @param int $code
     * @param mixed $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function responseError($message, $code, $errors = null)
    {
        return response()->json(
            [
                'message' => $message,
                'code' => $code,
                'errors' => $errors
            ]
        )->setEncodingOptions(JSON_UNESCAPED_UNICODE);
    }
}
