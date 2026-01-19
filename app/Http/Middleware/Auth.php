<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class Auth
{
    public function handle(Request $request, Closure $next)
    {
        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return response()->json([
                'code'    => 'TOKEN_EXPIRED',
                'message' => 'Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại'
            ], 401);
        } catch (TokenInvalidException $e) {
            return response()->json([
                'code'    => 'TOKEN_INVALID',
                'message' => 'Phiên đăng nhập không hợp lệ, vui lòng đăng nhập lại'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'code'    => 'UNAUTHENTICATED',
                'message' => 'Bạn chưa đăng nhập'
            ], 401);
        }

        return $next($request);
    }
}
