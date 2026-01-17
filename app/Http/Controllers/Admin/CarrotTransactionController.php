<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarrotTransaction;
use App\Models\CarrotPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletTransaction;
use App\Support\SumConfig;

class CarrotTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = CarrotTransaction::query()->orderByDesc('id')->with('user');

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
            'id' => 'required|exists:carrot_transactions,id',
            'status' => 'required|string',
        ]);

        $carrotTransaction = CarrotTransaction::findOrFail($validated['id']);

        if (!$carrotTransaction) {
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
                $carrotTransaction->status = 'success';
                $carrotTransaction->save();

                DB::commit();

                return response()->json([
                    'message' => 'Xác nhận thành công!',
                ]);
            } else {
                $carrotTransaction->status = 'failed';
                $carrotTransaction->save();

                $price = $carrotTransaction->price;
                // update cash user
                $user = $carrotTransaction->user;
                $userId = $user->id;
                $balanceBefore = $user->cash;
                $user->cash += $price;
                $user->save();

                $transaction = config("transactions.types.refund");

                WalletTransaction::create([
                    'user_id'        => $userId,
                    'type'           => 'refund',
                    'reference_type' => CarrotTransaction::class,
                    'reference_id'   => $carrotTransaction->id,
                    'direction'      => $transaction['type'],
                    'amount'         => $price,
                    'balance_before' => $balanceBefore,
                    'balance_after'  => $user->cash,
                    'description'    => $transaction['content'] . " Carrot #{$carrotTransaction->id}",
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
