<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NinjaCoinTransaction;
use App\Models\NinjaCoinPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AdminPurchaseNinjaCoinNotification;

class NinjaCoinTransactionsController extends Controller
{
    /**
     * Danh sách giao dịch bán xu của user
     */
    public function index(Request $request)
    {
        $query = NinjaCoinTransaction::query()
            ->search($request)
            ->where('user_id', $request->user()->id);

        return formatPaginate($query, $request);
    }

    /**
     * Bảng giá xu ninja
     */
    public function prices(Request $request)
    {
        return formatPaginate(
            NinjaCoinPrice::query(),
            $request
        );
    }

    /**
     * Tạo giao dịch bán xu (pending)
     */
    public function purchase(Request $request)
    {
        $validated = $request->validate([
            'character_name' => 'required|string|max:14',
            'server'         => 'required|integer',
            'amount'         => 'required|integer|min:10000|max:10000000',
        ]);

        // Lấy bảng giá theo server
        $priceRow = NinjaCoinPrice::where('server', $validated['server'])->first();

        if (!$priceRow) {
            return response()->json([
                'message' => 'Bảng giá server chưa được cấu hình'
            ], 422);
        }

        $amount = $validated['amount'];

        // Lấy giá theo khoảng (floor tier)
        if ($amount >= 1000000) {
            $price = $priceRow->amount_1000000;
        } elseif ($amount >= 500000) {
            $price = $priceRow->amount_500000;
        } elseif ($amount >= 200000) {
            $price = $priceRow->amount_200000;
        } elseif ($amount >= 50000) {
            $price = $priceRow->amount_50000;
        } else {
            $price = $priceRow->amount_10000;
        }

        if (!$price || $price <= 0) {
            return response()->json([
                'message' => 'Mức giá này hiện không khả dụng'
            ], 422);
        }

        $user = $request->user();
        $userId = $user->id;

        try {
            DB::beginTransaction();

            // 🔒 Lock user
            $user->lockForUpdate();

            $balanceBefore = $user->cash;

            // ❌ Không đủ tiền
            if ($balanceBefore < $price) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Số dư không đủ để mua xu ninja'
                ], 402);
            }

            // 💸 Trừ tiền user
            $user->cash -= $amount;
            $user->save();

            // 🧾 Tạo giao dịch mua xu (PENDING)
            $transaction = NinjaCoinTransaction::create([
                'user_id'        => $userId,
                'character_name' => $validated['character_name'],
                'server'         => $validated['server'],
                'coin'           => $price * $amount,
                'amount'         => $amount,
                'price'          => $price,
                'status'         => 'pending',
            ]);
            $transactionId = $transaction->id;

            // 📒 Ghi lịch sử ví
            $walletConfig = config('transactions.types.purchase_ninja_coin');

            WalletTransaction::create([
                'user_id'        => $userId,
                'type'           => 'purchase_ninja_coin',
                'reference_type' => NinjaCoinTransaction::class,
                'reference_id'   => $transactionId,
                'direction'      => $walletConfig['direction'], // out
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->cash,
                'description'    => $walletConfig['content'] . " #{$transactionId}",
            ]);

            DB::commit();

            // 📧 Gửi mail admin (không rollback nếu fail)
            try {
                Mail::to(config('mail.admin_email'))
                    ->queue(new AdminPurchaseNinjaCoinNotification(
                        $user,
                        $transaction
                    ));
            } catch (\Throwable $e) {
                Log::error('Send admin purchase ninja coin mail failed', [
                    'user_id' => $userId,
                    'transaction_id' => $transactionId,
                    'error' => $e->getMessage(),
                ]);
            }

            return fetchData($transaction->refresh());

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('Purchase ninja coin failed', [
                'user_id' => $user->id ?? null,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Thất bại',
            ], 500);
        }
    }
}
