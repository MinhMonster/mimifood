<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\TopUpTransactions; // Import model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

        $transaction = TopUpTransactions::findOrFail($validated['id']);

        if ($transaction->status !== 'pending') {
            return response()->json([
                'message' => 'Transaction has been processed.',
            ], 400);
        }

        DB::beginTransaction();
        try {
            if ($validated['confirm']) {
                $transaction->status = 'success';
                $transaction->save();
                // update cash user
                $user = $transaction->user;
                $user->cash += $transaction->amount;
                $user->save();
            } else {
                $transaction->status = 'failed';
                $transaction->save();
            }

            DB::commit();

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
