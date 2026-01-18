<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\UserCashRequest;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->search($request);

        return formatPaginate($query, $request);
    }

    public function updateCash(UserCashRequest $request, User $user)
    {
        try {
            DB::beginTransaction();
            $balanceBefore = $user->cash;
            $direction = $request->direction;
            $amount = $request->amount;
            if ($direction === 'in') {
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
                'description'    => $transaction['content'],
            ]);

            DB::commit();
            return response()->json([
                'message' => $transaction['content'] . " thành công!",
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => $direction . ' failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function updateStatus(Request $request, User $user)
    {
        try {
            DB::beginTransaction();

            $statusBefore = $user->status;
            $statusAfter  = $request->status === "locked" ? "locked" : "active";
            $message = $statusAfter === "locked" ? "Khoá thành công" : "Mở khoá thành công";

            if ($statusBefore === $statusAfter) {
                return response()->json([
                    'message' => $message,
                ]);
            }

            $user->update([
                'status' => $statusAfter,
            ]);

            DB::commit();

            return response()->json([
                'message' => $message,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => "Có lỗi xảy ra",
            ], 500);
        }
    }
}
