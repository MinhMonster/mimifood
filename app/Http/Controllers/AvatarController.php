<?php

namespace App\Http\Controllers;

use App\Models\Avatar;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AvatarController extends Controller
{
    public function index(Request $request)
    {
        $query = Avatar::where('is_sold', false)->orderByDesc('code')->search($request);

        return formatPaginate($query, $request);
    }

    public function show($code)
    {
        $avatar = Avatar::where('code', $code)->where('is_sold', false)->first();

        if (!$avatar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản không tồn tại hoặc đã bán!',
                'account_type' => 'avatar',
            ], 404);
        }

        return fetchData($avatar);
    }
}
