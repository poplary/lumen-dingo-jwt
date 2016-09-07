<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\BaseController;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends BaseController
{
    /**
     * 获取用户信息
     * @param  Request $request 请求数据
     * @param  integer $id      用户id
     * @return json             用户数据
     */
    function profile(Request $request, $id=null)
    {
        if(is_null($id))
            $id = $this->getAuthenticatedUser()->id;

        $user = User::find($id);

        if(! $user) {
            return response()->json([
                    'errcode' => 40101,
                    'errmsg' => '用户不存在'
                ], 404);
        }

        return response()->json([
                'errcode' => 0,
                'errmsg' => '获取成功',
                'data' => [
                    'user' => $user
                ]
            ], 200);
    }
}