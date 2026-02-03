<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TopUpTransactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\WalletTransaction;

class TopUpTransactionsController extends Controller
{
    public function index(Request $request)
    {
        $query = TopUpTransactions::query()->search($request)->with('user');

        return formatPaginate($query, $request);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:top_up_transactions,id',
            'confirm' => 'required|boolean',
        ]);

        $transaction = TopUpTransactions::with('user')->findOrFail($validated['id']);

        if ($transaction->status !== 'pending') {
            return response()->json([
                'message' => 'Transaction has been processed.',
            ], 400);
        }

        try {
            DB::transaction(function () use ($validated, $transaction) {

                if ($validated['confirm']) {

                    $user = $transaction->user;
                    $balanceBefore = $user->cash;

                    // 1. update topup status
                    $transaction->status = 'success';
                    $transaction->save();

                    // 2. update user cash
                    $user->increment('cash', $transaction->amount);

                    $walletConfig = config('transactions.types.top_up');

                    // 3. create wallet transaction (IN)
                    WalletTransaction::create([
                        'user_id'        => $user->id,
                        'type'           => 'top_up',
                        'reference_type' => TopUpTransactions::class,
                        'reference_id'   => $transaction->id,
                        'direction'      => $walletConfig['direction'], // in
                        'amount'         => $transaction->amount,
                        'balance_before' => $balanceBefore,
                        'balance_after'  => $balanceBefore + $transaction->amount,
                        'description'    => $walletConfig['content'] . " #{$transaction->id}",
                    ]);
                } else {
                    // reject topup
                    $transaction->status = 'failed';
                    $transaction->save();
                }
            });

            return response()->json([
                'message' => 'Success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Đã có lỗi xảy ra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
