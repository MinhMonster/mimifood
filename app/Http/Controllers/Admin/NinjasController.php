<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Ninjas; // Import model
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class NinjasController extends Controller
{
    public function index(Request $request)
    {
        $query = Ninjas::query()->search($request);

        return formatPaginate($query, $request);
    }

    public function modify(Request $request)
    {
        $oldId = $request->id;
        $input = $request->input('input');

        $validator = Validator::make($input, [
            'id' => 'nullable|integer',
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
            'discount_percent' => 'nullable|integer',
            'class' => 'required|integer',
            'level' => 'required|integer',
            'server' => 'required|integer',
            'weapon' => 'required|integer',
            'type' => ['required', Rule::in(['1', '2', '3'])],
            'tl1' => 'nullable|integer',
            'tl2' => 'nullable|integer',
            'tl3' => 'nullable|integer',
            'tl4' => 'nullable|integer',
            'tl5' => 'nullable|integer',
            'tl6' => 'nullable|integer',
            'tl7' => 'nullable|integer',
            'tl8' => 'nullable|integer',
            'tl9' => 'nullable|integer',
            'tl10' => 'nullable|integer',
            'yoroi' => 'nullable|integer',
            'eye' => 'nullable|integer',
            'book' => 'nullable|integer',
            'cake' => 'nullable|integer',
            'yen' => 'nullable|string',
            'clone' => 'nullable|string',
            'disguise' => 'nullable|string',
            'mounts' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

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

        if (!empty($validated['id']) && $validated['id'] != $oldId) {
            DB::transaction(function () use ($oldNinja, $validated) {
                $newNinja = new Ninjas();
                $newNinja->id = $validated['id'];
                $newNinja->fill($validated);
                $newNinja->save();

                $oldNinja->delete();
            });

            $ninja = Ninjas::find($validated['id']);
            return fetchData($ninja);
        }

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
