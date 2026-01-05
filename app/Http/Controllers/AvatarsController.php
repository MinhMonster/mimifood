<?php

namespace App\Http\Controllers;

use App\Models\Avatars;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AvatarsController extends Controller
{
    public function index(Request $request)
    {
        $query = Avatars::where('is_sold', false)->orderByDesc('code')->search($request);

        return formatPaginate($query, $request);
    }

    public function show($code)
    {
        $avatar = Avatars::where('code', $code)->where('is_sold', false)->first();

        if (!$avatar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Avatar not found',
            ], 404);
        }

        return fetchData($avatar);
    }
}
