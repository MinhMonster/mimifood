<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Ninjas; // Import model
use App\Http\Controllers\Controller;
use App\Support\SumConfig;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class NinjasController extends Controller
{
    public function index(Request $request)
    {
        $query = Ninjas::query()->search($request)->orderByDesc('code');

        return formatPaginate(
            $query,
            $request,
            [],
            SumConfig::account_game()
        );
    }

    public function modify(Request $request)
    {
        $oldId = $request->id;
        $input = $request->input('input');

        $validator = Validator::make($input, [
            'code' => [
                'nullable',
                'integer',
                'min:1',
                Rule::unique('ninjas', 'code')->ignore($oldId)->whereNull('deleted_at'),
            ],
            'username' => [
                'required',
                'string',
                Rule::unique('ninjas')->ignore($oldId)->whereNull('deleted_at'),
            ],
            // 'password' => 'nullable|string',
            'character_name' => 'required|string',
            'description' => 'required|string',
            'images' => 'required|array',
            'is_full_image' => 'required|boolean',
            'selling_price' => 'required|integer',
            'purchase_price' => 'required|integer',
            'discount_percent' => 'nullable|integer|max:50',
            'class' => 'required|integer',
            'level' => 'required|integer|max:160',
            'server' => 'required|integer',
            'weapon' => 'required|integer|max:16',
            'type' => ['required', Rule::in(['1', '2', '3'])],
            'tl_1' => 'nullable|integer|max:9',
            'tl_2' => 'nullable|integer|max:9',
            'tl_3' => 'nullable|integer|max:9',
            'tl_4' => 'nullable|integer|max:9',
            'tl_5' => 'nullable|integer|max:9',
            'tl_6' => 'nullable|integer|max:9',
            'tl_7' => 'nullable|integer|max:9',
            'tl_8' => 'nullable|integer|max:9',
            'tl_9' => 'nullable|integer|max:9',
            'tl_10' => 'nullable|integer|max:9',
            'tl_11' => 'nullable|integer|max:9',
            'tl_12' => 'nullable|integer|max:9',
            'item_1' => 'nullable|string',
            'item_2' => 'nullable|string',
            'item_3' => 'nullable|string',
            'item_4' => 'nullable|string',
            'item_5' => 'nullable|string',
            'item_6' => 'nullable|string',
            'item_7' => 'nullable|string',
            'item_8' => 'nullable|string',
            'item_9' => 'nullable|string',
            'item_10' => 'nullable|string',
            'item_11' => 'nullable|string',
            'item_12' => 'nullable|string',
            'item_13' => 'nullable|string',
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
            $validated['code'] = (Ninjas::max('code') ?? 0) + 1;
        }
        if (!$oldId) {
            $ninja = new Ninjas();
            if (!empty($validated['id'])) {
                $ninja->id = $validated['id'];
            }
            $ninja->fill($validated);
            $ninja->save();
            return fetchData($ninja);
        }

        $oldNinja = Ninjas::withTrashed()->find($oldId);
        if (!$oldNinja) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ninja not found',
            ], 404);
        }

        // if (!empty($validated['id']) && $validated['id'] != $oldId) {
        //     DB::transaction(function () use ($oldNinja, $validated) {
        //         $newNinja = new Ninjas();
        //         $newNinja->id = $validated['id'];
        //         $newNinja->fill($validated);
        //         $newNinja->save();

        //         $oldNinja->delete();
        //     });

        //     $ninja = Ninjas::find($validated['id']);
        //     return fetchData($ninja);
        // }

        $oldNinja->fill($validated)->save();
        return fetchData($oldNinja);
    }

    public function show(Request $request)
    {
        $ninja = Ninjas::withTrashed()->find($request->id);

        if (!$ninja) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ninja not found',
            ], 404);
        }

        return fetchData($ninja);
    }


    /**
     * Soft delete a ninja.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $ninja = Ninjas::withTrashed()->find($id);

        if (! $ninja) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ninja not found',
            ], 404);
        }

        if (! $ninja->trashed()) {
            $ninja->delete();
        }

        return fetchData($ninja);
    }


    /**
     * Restore (un-delete) a ninja.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request)
    {
        $id = $request->id;
        $ninja = Ninjas::withTrashed()->find($id);

        if (! $ninja) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Ninja not found',
            ], 404);
        }

        if ($ninja->trashed()) {
            $ninja->restore();
        }

        return fetchData($ninja);
    }
}
