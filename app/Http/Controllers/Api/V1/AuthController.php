<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;

class AuthController extends BaseController
{

    /**
     * 用户注册
     * @param  Request $request 请求
     * @return json             注册结果
     */
    public function register(Request $request)
    {
        $credentials = $request->only('email', 'password', 'password_confirmation');

        // 数据验证
        $validator = app('validator')->make($credentials, User::$registerRules);

        // 不符合数据验证规则
        if($validator->fails()) {
            return response()->json(
                array(
                    'errcode' => 400001,
                    'errmsg' => $validator->errors()
                ), 200);
        }

        // 用户注册数据存入数据库
        $user = new User();

        $user->email = $request->email;
        $user->password = app('hash')->make($request->password);
        $user->is_active = false;

        $data = $user->save();

        // 返回注册结果
        if($data){
            return response()->json([
                    'errcode' => 0,
                    'errmsg' => '注册成功，请等待账号激活',
                ], 200);
        } else{
            return response()->json([
                    'errcode' => 40002,
                    'errmsg' => '注册失败'
                ], 400);
        }
    }

    /**
     * 认证操作
     * @param  Request $request 请求数据
     * @return json             认证结果，若成功，返回 token
     */
    public function authenticate(Request $request)
    {

        $credentials = $request->only('email', 'password');

        // 验证格式
        $validator = app('validator')->make($credentials, User::$authRules);

        // 不符合数据验证规则
        if($validator->fails()){
            return response()->json(
                [
                    'errcode' => 40003,
                    'errmsg' => $validator->errors()
                ], 200);
        }

        // 验证用户是否存在
        $user = User::where('email', '=', $credentials['email'])->first();
        if(! $user){
            return response()->json([
                'errcode' => 40004,
                'errmsg' => '用户不存在'
            ], 404);
        }

        // 验证用户是否激活
        if(! $user->is_active){
            return response()->json([
                'errcode' => 40005,
                'errmsg' => '用户未激活'
            ], 200);
        }

        // 验证账号密码，生成 token
        try{
            if(! $token = JWTAuth::attempt($credentials)){
                return response()->json([
                    'errcode' => 40006,
                    'errmsg' => '密码错误或账号出错'
                ]);
            }
        } catch( JWTException $e ){
            return response()->json(
                [
                    'errcode' => 40007,
                    'errmsg' => '创建Token失败'
                ]);
        }

        // 返回认证信息
        return response()->json(
            [
                'errcode' => 0,
                'errmsg' => '认证成功',
                'data' => [
                    'token' => $token,
                    'user' => $user
                ]
            ], 200);
    }
}