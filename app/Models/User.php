<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Auth\Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, JWTSubject
{

    // 软删除和用户验证attempt
    use SoftDeletes, Authenticatable;

    // 密码保护
    protected $hidden = ['password'];

    // 注册用户的验证规则
    public static $registerRules = [
        'email' => ['email', 'unique:users,email'],
        'password' => ['required', 'alpha_dash', 'confirmed'],
        'password_confirmation' => ['required'],
    ];

    // 登录时的验证规则
    public static $authRules = [
        'email' => ['required', 'email'],
        'password' => ['required', 'alpha_dash'],
    ];

    // jwt 需要实现的方法
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    // jwt 需要实现的方法
    public function getJWTCustomClaims()
    {
        return [];
    }
}