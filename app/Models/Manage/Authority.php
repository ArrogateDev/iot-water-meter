<?php

namespace App\Models\Manage;

use App\Models\Base;

class Authority extends Base
{

    protected $hidden = ['created_at', 'updated_at', 'deleted_at', 'pivot'];

    //  权限类型
    public const MENU_TYPE = 0;    //  菜单

    public const GPS_TYPE = 1;     //  导航

    public const BUTTON_TYPE = 2;  //  按钮

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'pid', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function authority_roles()
    {
        return $this->hasMany(RoleAuthority::class, 'authority_id', 'id');
    }
}
