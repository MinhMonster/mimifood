<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AccountPurchase;
use App\Models\WalletTransaction;
use App\Support\SumConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Enums\AccountPurchaseStatus;
use App\Enums\AccountPurchaseType;

class AccountPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountPurchase::query()->filter()->with('user')->with('account');

        return formatPaginate(
            $query,
            $request,
            [],
            SumConfig::for('account_purchase')
        );
    }

    public function update(Request $request, int $id)
    {
        $history = AccountPurchase::withTrashed()->find($id);

        if (! $history) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Giao dịch này không tồn tại',
            ], 404);
        }

        $validated = $request->validate([
            'selling_price' => 'required|integer',
            'purchase_price' => 'required|integer',
            'images' => 'nullable|array',
            'note' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $history->fill($validated)->save();
            DB::commit();

            return response()->json([
                'message' => 'Cập nhật thành công',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'message' => 'Đã có lỗi xảy ra.',
            ], 500);
        }
    }

    public function updateAccount(Request $request, int $id)
    {
        $validated = $request->validate([
            'username'      => 'required|string',
            'password'      => 'required|string|regex:/^[a-z0-9]+$/',
            'transfer_pin'  => 'nullable|regex:/^[1-9]{4}$/',
        ]);

        $history = AccountPurchase::withTrashed()->find($request->history_id);

        if (! $history) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Giao dịch này không tồn tại',
            ], 404);
        }

        $account = $history->account;

        if (!$account) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Tài khoản không tồn tại!',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $account->fill($validated)->save();
            DB::commit();

            return response()->json([
                'message' => 'Cập nhật tài khoản thành công',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'message' => 'Đã có lỗi xảy ra.',
            ], 500);
        }
    }

    public function cancelAndRefund(AccountPurchase $accountPurchase)
    {
        if ($accountPurchase->type === AccountPurchaseType::NORMAL) {
            return response()->json([
                'message' => 'Giao dịch này không thể hủy và hoàn tiền.',
            ], 409);
        }
        if ($accountPurchase->status === AccountPurchaseStatus::CANCELLED_REFUNDED) {
            return response()->json([
                'message' => 'Giao dịch đã được hủy và hoàn tiền trước đó.',
            ], 409);
        }

        if ($accountPurchase->status === AccountPurchaseStatus::COMPLETED) {
            return response()->json([
                'message' => 'Giao dịch đã hoàn thành trước đó.',
            ], 409);
        }

        DB::beginTransaction();
        try {
            $accountPurchase->status = AccountPurchaseStatus::CANCELLED_REFUNDED;
            $accountPurchase->save();

            // Hoàn tiền cho người mua
            $price = $accountPurchase->selling_price;
            if ($accountPurchase->type === AccountPurchaseType::DEPOSIT) {
                $amount = $price * 0.2; // Hoàn lại 20% số tiền đã thanh toán
            } elseif ($accountPurchase->type === AccountPurchaseType::INSTALLMENTS) {
                $amount = $price * 0.5; // Hoàn lại 50% số tiền đã thanh toán
            }

            $user = $accountPurchase->user;
            $user->increment('cash', $amount);

            // tạo transaction cho phần thanh toán còn lại
            $walletTransactionType = $accountPurchase->type === AccountPurchaseType::INSTALLMENTS ? 'refund_account_installments' : 'refund_account_deposit';
            $transaction = config('transactions.types.' . $walletTransactionType);
            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => $walletTransactionType,
                'reference_type' => AccountPurchase::class,
                'reference_id'   => $accountPurchase->id,
                'direction'      => $transaction['direction'],
                'amount'         => $amount,
                'balance_before' => $user->cash + $amount,
                'balance_after'  => $user->cash,
                'description'    => $transaction['content'] . " #{$accountPurchase->id}",
            ]);
            DB::commit();

            return response()->json([
                'message' => 'Giao dịch đã được hủy và hoàn tiền cho người mua.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'message' => 'Đã có lỗi xảy ra.',
            ], 500);
        }
    }

    public function updateStatus(AccountPurchase $accountPurchase, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:completed,cancelled',
        ]);

        DB::beginTransaction();
        try {
            $accountPurchase->status = $validated['status'];
            $accountPurchase->save();

            DB::commit();

            return response()->json([
                'message' => 'Trạng thái giao dịch đã được cập nhật.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return response()->json([
                'message' => 'Đã có lỗi xảy ra.',
            ], 500);
        }
    }
}
