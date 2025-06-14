<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Discounts;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DiscountsController extends Controller
{
    /**
     * Danh sách tất cả giảm giá
     */
    public function index(Request $request)
    {
        $query = Discounts::query();
        return formatPaginate($query, $request);

    }

    /**
     * Tạo hoặc cập nhật một giảm giá
     */
    public function modify(Request $request, $id = null)
    {
        $isUpdate = $id !== null;
        $input = $request->input('input');

        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100', // loại game
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
            'price_tiers' => 'nullable|array',
            // 'price_tiers.*.min' => 'required_with:price_tiers|numeric|min:0',
            // 'price_tiers.*.max' => 'required_with:price_tiers|numeric|gte:price_tiers.*.min',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        if ($isUpdate) {
            $discount = Discounts::findOrFail($id);
            $discount->update($validated);
            $message = 'Discount updated successfully.';
        } else {
            $discount = Discounts::create($validated);
            $message = 'Discount created successfully.';
        }

        return response()->json([
            'message' => $message,
            'data' => $discount,
        ], $isUpdate ? 200 : 201);
    }

    /**
     * Xem chi tiết một giảm giá
     */
    public function show(Request $request)
    {
        $ninja = Discounts::withTrashed()->find($request->id);

        if (!$ninja) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ninja not found',
            ], 404);
        }

        return fetchData($ninja);
    }


    /**
     * Xoá một giảm giá
     */
    public function destroy($id)
    {
        $discount = Discounts::findOrFail($id);
        $discount->delete();

        return response()->json(['message' => 'Discount deleted successfully.']);
    }
}

