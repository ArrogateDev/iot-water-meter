<?php

namespace App\Models\Manage;

use App\Traits\Model\BaseTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens, Notifiable, BaseTrait, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'updated_at',
        'deleted_at'
    ];

    public const NORMAL = 0;

    public const DISABLE = 1;

    /**
     * 设置用户密码
     *
     * @param $value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * 管理员角色
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasOneThrough
     */
    public function role()
    {
        return $this->hasOneThrough(Role::class, AdminRole::class, 'admin_id', 'id', 'id', 'role_id');
    }

    /**
     * 管理员角色记录
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function role_logs()
    {
        return $this->belongsToMany(Role::class, AdminRole::class, 'admin_id', 'role_id', 'id', 'id');
    }

    /**
     * 用户权限节点
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function role_authority()
    {
        return $this->belongsToMany(RoleAuthority::class, AdminRole::class, 'admin_id', 'role_id', 'id', 'role_id');
    }

    /**
     * 用户是否有权限
     *
     * @param $alias
     * @return bool
     */
    public function hasPermissions($alias)
    {
        $this->role->load([
            'permissions' => function ($query) use ($alias) {
                $query->where('alias', $alias);
            }
        ]);
        return $this->role->permissions->isNotEmpty();
    }
}
