<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use App\Support\SumConfig;

abstract class AdminGameAccountController extends Controller
{
    /**
     * Trả về Model class (VD: Ninja::class)
     *
     * @return class-string<Model>
     */
    abstract protected function model(): string;

    /**
     * Rules validate – controller con tự định nghĩa
     */
    abstract protected function rules(?int $id = null): array;

    /**
     * Message not found
     */
    protected function notFoundMessage(): string
    {
        return 'Account not found';
    }

    /**
     * Instance model
     */
    protected function modelInstance(): Model
    {
        return app($this->model());
    }

    /**
     * Query chung cho admin index
     */
    protected function baseQuery(): Builder
    {
        return $this->modelInstance()
            ->newQuery()
            ->filter()
            ->orderByDesc('code');
    }

    /**
     * Danh sách admin
     */
    public function index(Request $request)
    {
        return formatPaginate(
            $this->baseQuery(),
            $request,
            [],
            SumConfig::account_game()
        );
    }

    /**
     * Tạo / cập nhật
     */
    public function modify(Request $request)
    {
        $id = $request->id;
        $input = $request->input('input');

        $validator = Validator::make($input, $this->rules($id));

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // auto code
        if (empty($validated['code'])) {
            $validated['code'] = (
                ($this->model())::withTrashed()->max('code') ?? 0
            ) + 1;
        }

        // create
        if (!$id) {
            $model = $this->modelInstance();
            $model->fill($validated)->save();
            return fetchData($model);
        }

        // update
        $model = ($this->model())::withTrashed()->find($id);
        if (!$model) {
            return $this->notFoundResponse();
        }

        $model->fill($validated)->save();
        return fetchData($model);
    }

    /**
     * Chi tiết (admin thấy cả soft delete)
     */
    public function show(Request $request)
    {
        $id = $request->id;

        $account = ($this->model())::withTrashed()->find($id);

        if (! $account) {
            return $this->notFoundResponse();
        }

        return fetchData($account);
    }

    /**
     * Soft delete (admin)
     */
    public function destroy(Request $request)
    {
        $id = $request->id;

        $account = ($this->model())::withTrashed()->find($id);

        if (! $account) {
            return $this->notFoundResponse();
        }

        if ($account->trashed()) {
            return fetchData($account);
        }

        $account->delete();

        return fetchData($account);
    }

    /**
     * Restore soft deleted account
     */
    public function restore(Request $request)
    {
        $id = $request->id;

        $account = ($this->model())::withTrashed()->find($id);

        if (! $account) {
            return $this->notFoundResponse();
        }

        if (! $account->trashed()) {
            return fetchData($account);
        }

        $account->restore();

        return fetchData($account);
    }

    protected function notFoundResponse()
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->notFoundMessage(),
        ], 404);
    }
}
