<?php

namespace App\Models\Manage;

use App\Models\Base;

class Role extends Base
{

    protected $hidden = ['updated_at', 'pivot', 'laravel_through_key'];

    /**
     * 角色和权限的模型关联关系
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Authority::class, RoleAuthority::class, 'role_id', 'authority_id', 'id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function permission_ids()
    {
        return $this->hasMany(RoleAuthority::class, 'role_id', 'id');
    }
}
