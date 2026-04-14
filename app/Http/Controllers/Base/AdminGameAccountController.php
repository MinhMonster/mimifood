<?php

namespace App\Http\Controllers\Base;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Support\SumConfig;

abstract class AdminGameAccountController extends BaseCrudController
{
    /**
     * Message not found (có thể override)
     */
    protected function notFoundMessage(): string
    {
        return 'Account not found';
    }

    protected function notFoundResponse()
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->notFoundMessage(),
        ], 404);
    }

    /**
     * Query riêng cho game account
     */
    protected function query(): Builder
    {
        return parent::query()
            ->filter()
            ->orderByDesc('code');
    }

    /**
     * Override index để thêm SumConfig
     */
    public function index(Request $request)
    {
        return formatPaginate(
            $this->query(),
            $request,
            [],
            SumConfig::account_game()
        );
    }

    /**
     * Hook trước khi save
     */
    protected function beforeSave(array &$data, ?Model $model = null): void
    {
        // auto generate code nếu chưa có
        if (empty($data['code'])) {
            $data['code'] = (
                ($this->model())::withTrashed()->max('code') ?? 0
            ) + 1;
        }
    }

    /**
     * Toggle deposit
     */
    public function toggleDeposit(Request $request)
    {
        $account = $this->findById($request->id);

        if (!$account) {
            return $this->notFoundResponse();
        }

        $account->is_deposit = !$account->is_deposit;
        $account->save();

        return response()->json([
            'status' => 'success',
            'message' => $account->is_deposit
                ? 'Deposit is On'
                : 'Deposit is Off',
        ]);
    }

    /**
     * Toggle installments
     */
    public function toggleInstallments(Request $request)
    {
        $account = $this->findById($request->id);

        if (!$account) {
            return $this->notFoundResponse();
        }

        $account->is_installments = !$account->is_installments;
        $account->save();

        return response()->json([
            'status' => 'success',
            'message' => $account->is_installments
                ? 'Installments is On'
                : 'Installments is Off',
        ]);
    }
}
