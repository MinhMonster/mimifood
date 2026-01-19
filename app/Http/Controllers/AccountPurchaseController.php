<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\Ninja;
use App\Models\Avatar;
use App\Models\DragonBall;
use App\Models\AccountPurchase;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminPurchaseNotification;

class AccountPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = AccountPurchase::query()->where('user_id', $user->id);
        return formatPaginate($query, $request, ['account']);
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $history = AccountPurchase::query()
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
            'account_type' => 'required|in:ninja,avatar,dragon_ball',
            'account_code' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        $user = $request->user();
        $userId = $user->id;
        $balanceBefore = $user->cash;
        $accountType = $request->input('account_type');
        $accountCode = $request->input('account_code');

        switch ($accountType) {
            case 'ninja':
                $accountModel = Ninja::class;
                break;
            case 'avatar':
                $accountModel = Avatar::class;
                break;
            case 'dragon_ball':
                $accountModel = DragonBall::class;
                break;
            default:
                return response()->json(['message' => 'Loại game không tồn tại!'], 400);
        }

        try {
            DB::beginTransaction();


            $account = $accountModel::where('code', $accountCode)
                ->whereNull('deleted_at')
                ->lockForUpdate()
                ->first();
            if (!$account) {
                DB::rollBack();
                return response()->json(['message' => 'Không tìm thấy tài khoản'], 404);
            }

            if ($account->is_sold) {
                DB::rollBack();
                return response()->json(['message' => 'Tài khoản đã bán'], 409);
            }

            $price = $account->selling_price ?? $account->price;

            if ($balanceBefore < $price) {
                DB::rollBack();
                return response()->json(['message' => 'Số dư không đủ'], 409);
            }
            // update user
            $user->decrement('cash', $price);

            // update account
            $account->is_sold = true;
            $account->save();

            // create history buy account
            $accountPurchase = AccountPurchase::create([
                'account_type' => $accountType,
                'account_code' => $account->code,
                'account_id' => $account->id,
                'user_id' => $userId,
                'selling_price' => $price,
                'purchase_price' => $account->purchase_price,
            ]);

            $transaction = config("transactions.types.purchase_account");

            WalletTransaction::create([
                'user_id'        => $userId,
                'type'           => 'purchase_account',
                'reference_type' => AccountPurchase::class,
                'reference_id'   => $accountPurchase->id,
                'direction'      => $transaction['direction'],
                'amount'         => $price,
                'balance_before' => $balanceBefore,
                'balance_after'  => $user->cash,
                'description'    => $transaction['content'] . " #{$accountPurchase->id}",
            ]);

            DB::commit();

            try {
                Mail::to(config('mail.admin_email'))
                    ->queue(new AdminPurchaseNotification($user, $account, $accountPurchase));
            } catch (\Throwable $e) {
                Log::error('Send admin purchase mail failed', [
                    'user_id' => $user->id,
                    'account_code' => $accountPurchase->account_code,
                    'error' => $e->getMessage(),
                ]);
            }

            return response()->json([
                'message' => 'Mua tài khoản thành công',
                'data' => $accountPurchase,
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
