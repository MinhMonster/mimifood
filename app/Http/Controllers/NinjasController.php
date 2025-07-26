<?php

namespace App\Http\Controllers;

use App\Models\Ninjas;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class NinjasController extends Controller
{
    public function index(Request $request)
    {
        $query = Ninjas::query()->search($request);
        return formatPaginate($query, $request);
    }

    public function show($id)
    {
        $ninja = Ninjas::find($id);
        if (!$ninja) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ninja not found',
            ], 404);
        }

        return fetchData($ninja);
    }
}
