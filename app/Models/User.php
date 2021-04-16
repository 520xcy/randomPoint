<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable;

    public $incrementing  = false;
    protected $table      = 'users';
    protected $primaryKey = 'openid';
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
        'name', 'openid', 'last_session'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $appends = []; //返回数列中添加

    // protected static function boot()
    // {
    //     parent::boot();

    //     /**
    //      * Attach to the 'creating' Model Event to provide a UUID
    //      * for the `id` field (provided by $model->getKeyName())
    //      */
    //     static::creating(function ($model) {
    //         $model->{$model->getKeyName()} = (string)$model->generateNewId();
    //         // $model->uuid = (string) $model->generateNewId();
    //     });
    // }

    // protected function generateNewId()
    // {
    //     return (string) Str::orderedUuid();;
    // }
}
