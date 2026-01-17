<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\User;
use App\Models\WalletTransaction;
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
            $direction = $request->type;
            $amount = $request->amount;
            if ($direction === 'increase') {
                $user->increment('cash', $amount);
                $type = 'admin_adjust_increase';
            } else {
                $user->decrement('cash', $amount);
                $type = 'admin_adjust_decrease';
            }
            $transaction = config("transactions.types.$type");

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => $type,
                'direction'      => $transaction['direction'],
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->cash,
                'reference'      => uniqid('TXN-'),
                'description'    => $transaction['content'],
            ]);

            DB::commit();
            return fetchData(
                [
                    'cash' => $user->cash,
                    'type' => $direction
                ],
                $direction . ' successful'
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $direction . ' failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
