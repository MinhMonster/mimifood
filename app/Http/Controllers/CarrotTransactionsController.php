<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CarrotTransaction;
use App\Models\CarrotPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\WalletTransaction;

class CarrotTransactionsController extends Controller
{
    /**
     * Danh sách giao dịch nạp carrot của user
     */
    public function index(Request $request)
    {
        $query = CarrotTransaction::query()
            ->where('user_id', $request->user()->id)
            ->orderByDesc('id');

        return formatPaginate($query, $request);
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

    /**
     * Tạo giao dịch nạp carrot (pending)
     */
    /**
     * Tạo giao dịch nạp carrot (pending) + trừ tiền ngay
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'game_type' => 'required|string|max:20',
            'username'  => 'required|string|max:50',
            'server'    => 'required|integer',
            'amount'    => 'required|integer|min:50000',
        ]);

        /** @var User $user */
        $user = $request->user();

        try {
            DB::beginTransaction();

            // 🔒 Lock user để tránh double spend
            $user->lockForUpdate();

            $balanceBefore = $user->cash;

            // 🔒 Lock giá theo mệnh giá
            $priceRow = CarrotPrice::lockForUpdate()
                ->where('amount', $validated['amount'])
                ->first();

            if (!$priceRow) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Mệnh giá này hiện không khả dụng'
                ], 422);
            }

            $price = $priceRow->price;

            // ❌ Không đủ tiền
            if ($balanceBefore < $price) {
                DB::rollBack();
                return response()->json([
                    'message' => 'Số dư không đủ để nạp carrot'
                ], 402);
            }

            // 💸 Trừ tiền user
            $user->cash -= $price;
            $user->save();

            // 🧾 Tạo giao dịch nạp (PENDING)
            $transaction = CarrotTransaction::create([
                'user_id'   => $user->id,
                'game_type' => $validated['game_type'],
                'username'  => $validated['username'],
                'server'    => $validated['server'],
                'amount'    => $priceRow->amount,
                'price'     => $price,
                'status'    => 'pending',
            ]);

            // 📒 Ghi lịch sử ví
            $walletConfig = config('transactions.types.carrot_topup');

            WalletTransaction::create([
                'user_id'        => $user->id,
                'type'           => 'carrot_topup',
                'direction'      => $walletConfig['type'], // out
                'amount'         => $price,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->cash,
                'description'    => $walletConfig['content'],
                'meta'           => $transaction,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Thành công',
                'data'    => $transaction,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Thất bại',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
