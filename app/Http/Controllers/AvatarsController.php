<?php

namespace App\Http\Controllers;

use App\Models\Avatars;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class AvatarsController extends Controller
{
    public function index(Request $request)
    {
        $query = Avatars::query()->search($request);

        return formatPaginate($query, $request);
    }

    public function show(Request $request)
    {
        $avatar = Avatars::find($request->id);

        if (!$avatar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Avatar not found',
            ], 404);
        }

        return fetchData($avatar);
    }
}
