<?php

namespace App\Models\WebSocket;

use App\Models\Base;
use App\Models\Manage\Admin;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminWebSocket extends Base
{
    use HasFactory;

    /**
     * 状态-离线
     */
    public const OFFLINE = 0;

    /**
     * 状态-在线
     */
    public const ONLINE = 1;

    /**
     * 状态映射
     */
    const onlineStatusMapping = [
        self::OFFLINE,
        self::ONLINE
    ];

    /**
     * 用户信息
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }
}
