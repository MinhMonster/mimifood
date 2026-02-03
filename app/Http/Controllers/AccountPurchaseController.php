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
            $result = DB::transaction(function () use (
                $accountModel,
                $accountCode,
                $user,
                $userId,
                $balanceBefore,
                $accountType
            ) {
                // lock account
                $account = $accountModel::where('code', $accountCode)
                    ->whereNull('deleted_at')
                    ->lockForUpdate()
                    ->first();
                if (!$account) {
                    throw new \RuntimeException('Không tìm thấy tài khoản', 404);
                }

                if ($account->is_sold) {
                    throw new \RuntimeException('Tài khoản đã bán', 409);
                }

                $price = $account->price ?? $account->selling_price;

                if ($balanceBefore < $price) {
                    throw new \RuntimeException('Số dư không đủ', 402);
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

                $transaction = config('transactions.types.purchase_account');

                // create wallet transaction (lỗi là rollback tất)
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

                return [
                    'account'          => $account,
                    'accountPurchase'  => $accountPurchase,
                ];
            });

            // gửi mail SAU transaction (không ảnh hưởng rollback)
            try {
                Mail::to(config('mail.admin_email'))
                    ->queue(new AdminPurchaseNotification(
                        $user,
                        $result['account'],
                        $result['accountPurchase']
                    ));
            } catch (\Throwable $e) {
                Log::error('Send admin purchase mail failed', [
                    'user_id'      => $user->id,
                    'account_code' => $result['accountPurchase']->account_code,
                    'error'        => $e->getMessage(),
                ]);
            }

            return response()->json([
                'message' => 'Mua tài khoản thành công',
                'data'    => $result['accountPurchase'],
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        } catch (\Throwable $e) {
            Log::error('Purchase account failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình mua, vui lòng thử lại',
            ], 500);
        }
    }
}
