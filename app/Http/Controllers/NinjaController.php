<?php

namespace App\Http\Controllers;

use App\Models\Ninja;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class NinjaController extends Controller
{
    public function index(Request $request)
    {
        $query = Ninja::where('is_sold', false)->orderByDesc('code')->search($request);
        return formatPaginate($query, $request);
    }

    public function show($code)
    {
        $ninja = Ninja::where('code', $code)->where('is_sold', false)->first();
        if (!$ninja) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tài khoản không tồn tại hoặc đã bán!',
                'account_type' => 'ninja',
            ], 404);
        }

        return fetchData($ninja);
    }
}
