<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PcLogin extends Model
{

    public $incrementing  = false;
    protected $table      = 'pc_logins';
    protected $primaryKey = 'random_key';
    protected $keyType = 'string'; //uuid做用户表id的坑
    protected $dates      = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'is_active', 'random_key', 'openid'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];
}
