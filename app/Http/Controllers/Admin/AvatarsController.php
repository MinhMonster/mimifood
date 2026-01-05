<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Avatars; // Import model
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class AvatarsController extends Controller
{
    public function index(Request $request)
    {
        $query = Avatars::query()->search($request)->orderByDesc('code');

        return formatPaginate($query, $request);
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
                Rule::unique('avatars')->ignore($oldId)->whereNull('deleted_at'),
            ],
            // 'password' => 'nullable|string',
            'description' => 'required|string',
            'images' => 'required|array',
            'is_full_image' => 'required|boolean',
            'selling_price' => 'required|integer',
            'purchase_price' => 'required|integer',
            'discount_percent' => 'nullable|integer',
            'land' => 'required|integer',
            'pets' => 'required|integer',
            'fish' => 'required|integer',
            'sex' => 'required|integer',
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
            $validated['code'] = (Avatars::max('code') ?? 0) + 1;
        }

        if (!$oldId) {
            $avatar = new Avatars();
            if (!empty($validated['id'])) {
                $avatar->id = $validated['id'];
            }
            $avatar->fill($validated);
            $avatar->save();
            return fetchData($avatar);
        }

        $oldAvatar = Avatars::withTrashed()->find($oldId);
        if (!$oldAvatar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Avatar not found',
            ], 404);
        }

        // if (!empty($validated['id']) && $validated['id'] != $oldId) {
        //     DB::transaction(function () use ($oldAvatar, $validated) {
        //         $newAvatar = new Avatars();
        //         $newAvatar->id = $validated['id'];
        //         $newAvatar->fill($validated);
        //         $newAvatar->save();

        //         $oldAvatar->delete();
        //     });

        //     $avatar = Avatars::find($validated['id']);
        //     return fetchData($avatar);
        // }

        $oldAvatar->fill($validated)->save();
        return fetchData($oldAvatar);
    }

    public function show(Request $request)
    {
        $avatar = Avatars::withTrashed()->find($request->id);

        if (!$avatar) {
            return response()->json([
                'status' => 'error',
                'message' => 'Avatar not found',
            ], 404);
        }

        return fetchData($avatar);
    }


    /**
     * Soft delete a avatar.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request)
    {
        $id = $request->id;
        $avatar = Avatars::withTrashed()->find($id);

        if (! $avatar) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Avatar not found',
            ], 404);
        }

        if (! $avatar->trashed()) {
            $avatar->delete();
        }

        return fetchData($avatar);
    }


    /**
     * Restore (un-delete) a avatar.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore(Request $request)
    {
        $id = $request->id;
        $avatar = Avatars::withTrashed()->find($id);

        if (! $avatar) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Avatar not found',
            ], 404);
        }

        if ($avatar->trashed()) {
            $avatar->restore();
        }

        return fetchData($avatar);
    }
}
