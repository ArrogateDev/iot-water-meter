<?php

namespace App\Models\Manage;

use App\Models\Base;

class AdminLog extends Base
{

    protected $casts = ['content' => 'array'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    /**
     * 记录
     *
     * @param $request
     * @param $type
     * @param $title
     * @return void
     */
    public static function record($request, $type = 1, $title = '')
    {
        $title = empty($title) ? '未知' : $title;
        self::create([
            'title' => $title,
            'type' => $type,
            'content' => json_decode($request['content'], true),
            'method' => $request['method'],
            'url' => $request['url'],
            'admin_id' => $request['admin_id'],
            'username' => $request['username'],
            'useragent' => $request['useragent'],
            'ip' => $request['ip']
        ]);
    }
}
