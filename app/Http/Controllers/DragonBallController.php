<?php

namespace App\Http\Controllers;

use App\Models\DragonBall;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DragonBallController extends Controller
{
    /**
     * Danh sách nick Ngọc Rồng
     */
    public function index(Request $request)
    {
        $query = DragonBall::available()
            ->orderByDesc('code')
            ->search($request);

        return formatPaginate($query, $request);
    }

    /**
     * Chi tiết nick theo code
     */
    public function show($code)
    {
        $dragonBall = DragonBall::available()
            ->where('code', $code)
            ->first();

        if (!$dragonBall) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản không tồn tại hoặc đã bán!',
                'account_type' => 'dragon_ball',
            ], 404);
        }

        return fetchData($dragonBall);
    }
}
