<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\User;
use App\Models\Admin\WalletTransaction;
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
            $balanceBefore = $user->cash;
            $type = $request->type;
            $amount = $request->amount;
            if ($type === 'increase') {
                $user->increment('cash', $amount);
            } else {
                $user->decrement('cash', $amount);
            }

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => $type,
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->cash,
                'reference'      => uniqid('TXN-'),
                'description'    => $validated['description'] ?? null,
            ]);

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
