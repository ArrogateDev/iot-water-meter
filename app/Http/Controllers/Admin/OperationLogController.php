<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Manage\AdminLog;
use Illuminate\Http\Request;

class OperationLogController extends Controller
{
    /**
     * 列表
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $name = $request->query('name');

        $admin = $request->user('admin');

        $list = AdminLog::query()
            ->when($admin->id != 1, function ($query) {
                $query->where('id', '>', 1);
            })
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->orderByDesc('id')
            ->paginate(limit_page());

        $list->makeHidden(['admin_id', 'updated_at']);

        return $this->responseSuccess($list);
    }
}
