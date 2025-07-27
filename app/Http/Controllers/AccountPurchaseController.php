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

class AccountPurchaseController extends Controller
{
    public function purchase(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account_type' => 'required|in:ninja,avatar,ngocrong',
            'account_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        /** @var User $user */
        $user = Auth::user();
        $accountType = $request->input('account_type');
        $accountId = $request->input('account_id');

        switch ($accountType) {
            case 'ninja':
                $accountModel = Ninjas::class;
                break;
            case 'avatar':
                $accountModel = Avatars::class;
                break;
            default:
                return response()->json(['message' => 'Invalid game type'], 400);
        }

        $account = $accountModel::lockForUpdate()->find($accountId);

        if (!$account) {
            return response()->json(['message' => 'Account not found'], 404);
        }

        if ($account->is_sold ?? false) {
            return response()->json(['message' => 'Account already sold'], 409);
        }

        if ($user->cash < $account->selling_price) {
            return response()->json(['message' => 'Số dư không đủ để mua tài khoản'], 402);
        }

        try {
            DB::beginTransaction();
            // update user
            $user->cash -= $account->selling_price;
            $user->save();

            // update account
            $account->is_sold = true;
            $account->save();

            // create history buy account
            $history = AccountPurchaseHistory::create([
                'account_type' => $accountType,
                'account_id' => $account->id,
                'user_id' => $user->id,
                'price' => $account->selling_price,
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
