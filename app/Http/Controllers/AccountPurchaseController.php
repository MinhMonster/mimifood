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
            'purchase_type' => 'nullable|in:normal,installments,deposit',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data'], 400);
        }

        $user = $request->user();
        $userId = $user->id;
        $balanceBefore = $user->cash;
        $accountType = $request->input('account_type');
        $accountCode = $request->input('account_code');
        $purchaseType = $request->input('purchase_type') ?? 'normal';

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
                $accountType,
                $purchaseType
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

                switch ($purchaseType) {
                    case 'installments':
                        if (!$account->is_installments) {
                            throw new \RuntimeException('Tài khoản không hỗ trợ mua trả góp', 400);
                        }
                        $amount = $account->installments_price ?? $account->selling_price;
                        $deadline = now()->addMonth(1);
                        $walletTransactionType = 'purchase_account_installment_first';
                        $status = 'installment_first';
                        break;
                    case 'deposit':
                        if (!$account->is_deposit) {
                            throw new \RuntimeException('Tài khoản không hỗ trợ đặt cọc', 400);
                        }
                        $amount = $account->deposit_price ?? $account->selling_price;
                        $deadline = now()->addDays(7);
                        $walletTransactionType = 'purchase_account_deposit';
                        $status = 'deposit';
                        break;
                    default:
                        $amount = $price;
                        $walletTransactionType = 'purchase_account';
                        $status = 'completed';
                        $completed_at = now();
                        break;
                }


                if ($balanceBefore < $amount) {
                    throw new \RuntimeException('Số dư không đủ', 402);
                }

                // update user
                $user->decrement('cash', $amount);

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
                    'type' => $purchaseType,
                    'first_paid_amount' => $amount,
                    'second_paid_amount' => $purchaseType === 'normal' ? 0 : ($price - $amount),
                    'deadline_at' => $deadline ?? null,
                    'status' => $status,
                    'completed_at' => $completed_at ?? null,
                ]);

                $transaction = config('transactions.types.' . $walletTransactionType);

                // create wallet transaction (lỗi là rollback tất)
                WalletTransaction::create([
                    'user_id'        => $userId,
                    'type'           => $walletTransactionType,
                    'reference_type' => AccountPurchase::class,
                    'reference_id'   => $accountPurchase->id,
                    'direction'      => $transaction['direction'],
                    'amount'         => $amount,
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
                'message' => 'Giao dịch thành công',
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

    public function cancel(AccountPurchase $accountPurchase, Request $request)
    {
        $user = $request->user();

        if ($accountPurchase->user_id !== $user->id) {
            return response()->json([
                'message' => 'Bạn không có quyền hủy đơn hàng này',
            ], 403);
        }

        if (!in_array($accountPurchase->status, ['installment_first', 'deposit'])) {
            return response()->json([
                'message' => 'Chỉ có thể hủy đơn hàng đang ở trạng thái trả góp lần 1 hoặc đặt cọc',
            ], 400);
        }

        try {
            DB::transaction(function () use ($accountPurchase, $user) {
                // refund tiền đã trả
                if ($accountPurchase->status === 'installment_first') {
                    $refundAmount = 0;
                    $accountPurchase->status = 'cancelled_refund_pending';
                } else {
                    $refundAmount = $accountPurchase->first_paid_amount * 0.2; // hoàn tiền 20% cho đơn đặt cọc
                    $user->increment('cash', $refundAmount);
                    $walletTransactionType = 'refund_account_deposit';
                    $transaction = config('transactions.types.' . $walletTransactionType);
                    WalletTransaction::create([
                        'user_id'        => $user->id,
                        'type'           => $walletTransactionType,
                        'reference_type' => AccountPurchase::class,
                        'reference_id'   => $accountPurchase->id,
                        'direction'      => $transaction['direction'],
                        'amount'         => $refundAmount,
                        'balance_before' => $user->cash - $refundAmount,
                        'balance_after'  => $user->cash,
                        'description'    => $transaction['content'] . " #{$accountPurchase->id}",
                    ]);
                    // update account purchase
                    $accountPurchase->status = 'cancelled';
                }
                $accountPurchase->cancelled_at = now();
                $accountPurchase->save();

                // update account (mở khóa bán lại)
                $accountModel = null;
                switch ($accountPurchase->account_type) {
                    case 'ninja':
                        $accountModel = Ninja::class;
                        break;
                    case 'avatar':
                        $accountModel = Avatar::class;
                        break;
                    case 'dragon_ball':
                        $accountModel = DragonBall::class;
                        break;
                }
                if ($accountModel) {
                    $account = $accountModel::find($accountPurchase->account_id);
                    if ($account) {
                        $account->is_sold = false;
                        $account->save();
                    }
                }
            });

            return response()->json([
                'message' => 'Hủy đơn hàng thành công',
            ]);
        } catch (\Throwable $e) {
            Log::error('Cancel account purchase failed', [
                'user_id' => $user->id,
                'purchase_id' => $accountPurchase->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình hủy đơn hàng, vui lòng thử lại',
            ], 500);
        }
    }

    public function payRemaining(AccountPurchase $accountPurchase, Request $request)
    {
        $user = $request->user();

        if ($accountPurchase->user_id !== $user->id) {
            return response()->json([
                'message' => 'Bạn không có quyền thanh toán cho đơn hàng này',
            ], 403);
        }

        if (!in_array($accountPurchase->status, ['installment_first', 'deposit'])) {
            return response()->json([
                'message' => 'Chỉ có thể thanh toán cho đơn hàng đang ở trạng thái trả góp lần 1 hoặc đặt cọc',
            ], 400);
        }

        try {
            DB::transaction(function () use ($accountPurchase, $user) {
                $remainingAmount = $accountPurchase->second_paid_amount;
                if ($user->cash < $remainingAmount) {
                    throw new \RuntimeException('Số dư không đủ để thanh toán phần còn lại', 402);
                }

                // trừ tiền phần còn lại
                $user->decrement('cash', $remainingAmount);

                // update account purchase
                $accountPurchase->status = 'completed';
                $accountPurchase->completed_at = now();
                $accountPurchase->save();

                // tạo transaction cho phần thanh toán còn lại
                $walletTransactionType = $accountPurchase->type === 'installments' ? 'purchase_account_installment_second' : 'purchase_account_deposit_completed';
                $transaction = config('transactions.types.' . $walletTransactionType);
                WalletTransaction::create([
                    'user_id'        => $user->id,
                    'type'           => $walletTransactionType,
                    'reference_type' => AccountPurchase::class,
                    'reference_id'   => $accountPurchase->id,
                    'direction'      => $transaction['direction'],
                    'amount'         => $remainingAmount,
                    'balance_before' => $user->cash + $remainingAmount,
                    'balance_after'  => $user->cash,
                    'description'    => $transaction['content'] . " #{$accountPurchase->id}",
                ]);
            });

            return response()->json([
                'message' => 'Thanh toán thành công',
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 400);
        } catch (\Throwable $e) {
            Log::error('Pay remaining for account purchase failed', [
                'user_id' => $user->id,
                'purchase_id' => $accountPurchase->id,
                'error'   => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Đã xảy ra lỗi trong quá trình thanh toán phần còn lại, vui lòng thử lại',
            ], 500);
        }
    }
}
