<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin\Discount;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DiscountController extends Controller
{
    public function index(Request $request)
    {
        $query = Discount::query();
        return formatPaginate($query, $request);
    }

    public function modify(Request $request)
    {
        $id = $request->id;
        $input = $request->input('input');

        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
            'price_tiers' => 'nullable|array',
            // 'price_tiers.*.min' => 'required_with:price_tiers|numeric|min:0',
            // 'price_tiers.*.max' => 'required_with:price_tiers|numeric|gte:price_tiers.*.min',
        ]);

        $validated = $validator->validated();
        if ($id) {
            $discount = Discount::findOrFail($id);
            $discount->update($validated);
        } else {
            $discount = Discount::create($validated);
        }

        return fetchData($discount);
    }

    public function show(Discount $discount)
    {

        if (!$discount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Discount không tồn tại',
            ], 404);
        }

        return fetchData($discount);
    }


    public function setActive(Discount $discount)
    {
        DB::transaction(function () use ($discount) {

            $newStatus = ! $discount->is_active;

            if ($newStatus === true) {
                Discount::where('type', $discount->type)
                    ->where('id', '!=', $discount->id)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            $discount->update([
                'is_active' => $newStatus,
            ]);
        });

        return response()->json([
            'message' => 'Cập nhật thành công!',
        ]);
    }

    public function destroy($id)
    {
        $discount = Discount::findOrFail($id);
        $discount->delete();

        return response()->json(['message' => 'Discount deleted successfully.']);
    }
}
