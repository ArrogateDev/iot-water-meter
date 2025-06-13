<?php

namespace App\Http\Controllers;

use App\Constants\ResponseCode;
use App\Exceptions\ApiException;
use App\Jobs\AutoDeleteExpiresFileJob;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{

    /**
     * 上传文件
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiException
     */
    public function upload(Request $request)
    {
        $type = $request->input('type', 'default');
        $file = $request->file('file');
        if (!in_array($type, ['default', 'editor'])) {
            throw new ApiException('类型错误', ResponseCode::PARAM_ERR);
        }

        if ($file->isValid() === false) {
            throw new ApiException('文件错误', ResponseCode::PARAM_ERR);
        }

        try {

            $date = Carbon::now()->format('Ymd');
            $extension = $file->getClientOriginalExtension();
            $name = $file->getClientOriginalName();
            $path = 'files/' . $type . '/' . $date;
            $filename = md5($name) . '.' . $extension;

            $result['type'] = $type;
            $result['filename'] = $file->storeAs($path, $filename, 'public');

//            AutoDeleteExpiresFileJob::dispatch($path . '/' . $filename, $type)->delay(now()->addMinutes(30));

            return $this->responseSuccess($result);
        } catch (\Exception $e) {
            Log::error($e);
            throw new ApiException(__('fail'), ResponseCode::SERVER_ERR);
        }
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     * @throws \Exception
     */
    public function download(Request $request)
    {
        try {
            $file = $request->query('file');
            if (!Storage::exists($file)) {
                throw new \Exception(__('文件不存在'));
            }
            return Storage::download($file);
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
