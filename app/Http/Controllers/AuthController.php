<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json([
                    'code'    => 'INVALID_CREDENTIALS',
                    'message' => 'Email hoặc mật khẩu không đúng'
                ], 401);
            }
        } catch (JWTException $e) {
            return response()->json([
                'code'    => 'TOKEN_CREATE_FAILED',
                'message' => 'Không thể đăng nhập, vui lòng thử lại sau'
            ], 500);
        }

        return response()->json([
            'token'   => $token,
            'message' => 'Đăng nhập thành công'
        ]);
    }

    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json([
            'message' => 'Đã đăng xuất thành công'
        ]);
    }
}
