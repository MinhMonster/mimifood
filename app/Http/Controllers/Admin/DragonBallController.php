<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\DragonBall; // Import model
use App\Http\Controllers\Controller;
use App\Support\SumConfig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class DragonBallController extends Controller
{
    public function index(Request $request)
    {
        $query = DragonBall::query()->search($request)->orderByDesc('code');

        return formatPaginate(
            $query,
            $request,
            [],
            SumConfig::account_game()
        );
    }

    public function modify(Request $request)
    {
        $id = $request->id;
        $input = $request->input('input');

        $validator = Validator::make($input, [
            'code' => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('dragon_balls', 'code')->ignore($id)->whereNull('deleted_at'),
            ],
            'username' => [
                'required',
                'string',
                Rule::unique('dragon_balls')->ignore($id)->whereNull('deleted_at'),
            ],
            'description' => 'nullable|string',
            'images' => 'required|array',
            'selling_price' => 'required|integer',
            'purchase_price' => 'required|integer',
            'discount_percent' => 'nullable|integer',
            'planet' => 'required|integer',
            'server' => 'required|integer',
            'type' => ['required', Rule::in([1, 2, 3])],
            'strength' => 'required|string',
            'disciple' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        if (empty($validated['code'])) {
            $validated['code'] = (DragonBall::max('code') ?? 0) + 1;
        }

        if (!$id) {
            $account = new DragonBall();
            if (!empty($validated['id'])) {
                $account->id = $validated['id'];
            }
            $account->fill($validated);
            $account->save();
            return fetchData($account);
        }

        $account = DragonBall::withTrashed()->find($id);
        if (!$account) {
            return response()->json([
                'status' => 'error',
                'message' => 'Account not found',
            ], 404);
        }

        $account->fill($validated)->save();
        return fetchData($account);
    }

    public function show(Request $request)
    {
        $account = DragonBall::withTrashed()->find($request->id);

        if (!$account) {
            return response()->json([
                'status' => 'error',
                'message' => 'Account not found',
            ], 404);
        }

        return fetchData($account);
    }


    /**
     * Soft delete a account.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $account = DragonBall::withTrashed()->find($id);

        if (!$account) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Account not found',
            ], 404);
        }

        if (! $account->trashed()) {
            $account->delete();
        }

        return fetchData($account);
    }


    /**
     * Restore (un-delete) a account.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request)
    {
        $id = $request->id;
        $account = DragonBall::withTrashed()->find($id);

        if (! $account) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Account not found',
            ], 404);
        }

        if ($account->trashed()) {
            $account->restore();
        }

        return fetchData($account);
    }
}
