<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\AccountPurchase;
use App\Support\SumConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = AccountPurchase::query()->search($request)->with('user');

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
}
