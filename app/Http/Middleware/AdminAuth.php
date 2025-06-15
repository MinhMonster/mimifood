<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class AdminAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            // Parse token trước khi lấy user
            $jwtUser = JWTAuth::parseToken()->authenticate();
            $user = Auth::guard('admin-api')->user($jwtUser);

            // Nếu không có user hoặc user không phải Admin
            if (!$user || !($user instanceof \App\Models\Admin)) {
                return response()->json([
                    'message' => $user
                ], 401);
            }

        } catch (TokenInvalidException $e) {
            return response()->json(['message' => 'Token is invalid'], 401);
        } catch (TokenExpiredException $e) {
            return response()->json(['message' => 'Token is expired'], 401);
        } catch (JWTException $e) {
            return response()->json(['message' => 'Authorization token not found'], 401);
        }

        return $next($request);
    }
}
