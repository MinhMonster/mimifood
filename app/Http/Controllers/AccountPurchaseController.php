<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use \App\Models\User;
use App\Models\Ninjas;
use App\Models\Avatars;
use App\Models\AccountPurchaseHistory;
use App\Models\WalletTransaction;

class AccountPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = AccountPurchaseHistory::query()->where('user_id', $user->id);
        return formatPaginate($query, $request, ['account']);
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $history = AccountPurchaseHistory::query()
            ->where('user_id', $user->id)
            ->where('id', $request->id)->first();

        if (!$history) {
            return response()->json([
                'status' => 'error',
                'message' => 'History not found',
            ], 404);
        }

        return fetchData($history);
    }

    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_type' => 'required|in:ninja,avatar,ngocrong',
            'account_code' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        /** @var User $user */
        $user = Auth::user();
        $userId = $user->id;
        $balanceBefore = $user->cash;
        $accountType = $request->input('account_type');
        $accountCode = $request->input('account_code');

        switch ($accountType) {
            case 'ninja':
                $accountModel = Ninjas::class;
                break;
            case 'avatar':
                $accountModel = Avatars::class;
                break;
            default:
                return response()->json(['message' => 'Loại game không tồn tại!'], 400);
        }

        $account = $accountModel::lockForUpdate()->where('code', $accountCode)->first();
        $price = $account->price;

        if (!$account) {
            return response()->json(['message' => 'Không tìm thấy tài khoản này!'], 404);
        }

        if ($account->is_sold ?? false) {
            return response()->json(['message' => 'Tài khoản này đã bán!'], 409);
        }

        if ($balanceBefore < $price) {
            return response()->json(['message' => 'Số dư không đủ để mua tài khoản!'], 402);
        }

        try {
            DB::beginTransaction();
            // update user
            $user->cash -= $price;
            $user->save();

            // update account
            $account->is_sold = true;
            $account->save();

            // create history buy account
            $history = AccountPurchaseHistory::create([
                'account_type' => $accountType,
                'account_code' => $account->code,
                'user_id' => $userId,
                'selling_price' => $price,
                'purchase_price' => $account->purchase_price,
            ]);

            $transaction = config("transactions.types.purchase");

            WalletTransaction::create([
                'user_id'        => $userId,
                'type'           => 'purchase',
                'direction'      => $transaction['type'],
                'amount'         => $price,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->cash,
                'description'    => $transaction['content'],
                'meta'           => $history
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Mua tài khoản thành công',
                'data' => $history,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình mua, vui lòng thử lại',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
