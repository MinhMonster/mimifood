<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\User;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\TopUpRequest;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->search($request);

        return formatPaginate($query, $request);
    }

    public function topUp(TopUpRequest $request, User $user)
    {
        try {
            DB::beginTransaction();

            if ($request->type === 'increase') {
                $user->increment('cash', $request->amount);
            } else {
                $user->decrement('cash', $request->amount);
            }

            DB::commit();
            return fetchData(
                [
                    'cash' => $user->cash,
                    'type' => $request->type
                ],
                ucfirst($request->type) . ' successful'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => ucfirst($request->type) . ' failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
