<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NinjaCoinTransaction;
use App\Models\NinjaCoinPrice;
use Illuminate\Http\Request;

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
    public function store(Request $request)
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

        $transaction = NinjaCoinTransaction::create([
            'user_id'        => $request->user()->id,
            'character_name' => $validated['character_name'],
            'server'         => $validated['server'],
            'coin'           => $price * $amount,
            'amount'         => $amount,
            'price'          => $price, // 🔒 lock giá tại thời điểm tạo
        ]);

        return fetchData($transaction);
    }
}
