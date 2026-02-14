<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NinjaCoinTransaction;
use App\Models\CarrotPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\WalletTransaction;

class AdminNinjaCoinTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = NinjaCoinTransaction::query()->orderByDesc('id')->with('user');

        return formatPaginate(
            $query,
            $request,
            [],
        );
    }

    /**
     * Bảng giá nạp carrot
     * (API public – không cần auth)
     */
    public function prices(Request $request)
    {
        return formatPaginate(
            CarrotPrice::query(),
            $request
        );
    }

    public function updateStatus(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:ninja_coin_transactions,id',
            'status' => 'required|string',
        ]);

        $transaction = NinjaCoinTransaction::findOrFail($validated['id']);

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Giao dịch không tồn tại.',
            ], 404);
        }

        // if ($transaction->status !== 'pending') {
        //     return response()->json([
        //         'message' => 'Giao dịch đã được xử lý.',
        //     ], 400);
        // }

        DB::beginTransaction();
        try {
            if ($validated['status'] === "confirmed") {
                $transaction->status = 'success';
                $transaction->save();

                DB::commit();

                return response()->json([
                    'message' => 'Xác nhận thành công!',
                ]);
            } else {
                $transaction->status = 'failed';
                $transaction->save();

                $price = $transaction->amount;
                // update cash user
                $user = $transaction->user;
                $userId = $user->id;
                $balanceBefore = $user->cash;
                $user->cash += $price;
                $user->save();

                $walletConfig = config("transactions.types.refund_ninja_coin");

                WalletTransaction::create([
                    'user_id'        => $userId,
                    'type'           => 'refund_ninja_coin',
                    'reference_type' => NinjaCoinTransaction::class,
                    'reference_id'   => $transaction->id,
                    'direction'      => $walletConfig['direction'],
                    'amount'         => $price,
                    'balance_before' => $balanceBefore,
                    'balance_after'  => $user->cash,
                    'description'    => $walletConfig['content'] . " #{$transaction->id}",
                ]);

                DB::commit();

                return response()->json([
                    'message' => 'Đã huỷ và hoàn tiền!',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Đã có lỗi xảy ra.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
