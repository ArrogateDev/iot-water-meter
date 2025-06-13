<?php

namespace App\Models;

use App\Traits\Model\BaseTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Base extends Model
{
    use BaseTrait, SoftDeletes;

    protected $guarded = [];

    protected $hidden = [
        'updated_at',
        'deleted_at'
    ];
}
