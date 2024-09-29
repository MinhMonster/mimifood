<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Products; // Import model
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Hiển thị danh sách các sản phẩm.
     */
    public function index(Request $request)
    {
        $products = Products::withTrashed();

        return formatPaginate($products, $request);
    }

    /**
     * Lưu trữ sản phẩm mới vào cơ sở dữ liệu.
     */
    public function modify(Request $request)
    {
        // 1. Xác thực dữ liệu
        $input = $request->input('input');
        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'link' => [
                'required',
                'string',
                'unique:products,link,' . ($input['id'] ?? '')
            ],
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'images' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

        if($request->id) {
            $product = Products::withTrashed()->find($request->id);
        } else {
            $product = new Products();
        }

        $product->name = $input['name'];
        $product->link = $input['link'];
        $product->price = $input['price'];
        $product->description = $input['description'];
        $product->quantity = $input['quantity'] ?? 0;  // Mặc định là 0 nếu không có giá trị
        $product->images = $input['images'] ?? [];

        if($request->id) {
            $product->update();
        } else {
            $product->save();  // Lưu vào database
        }

        return $product;
    }

    /**
     * Hiển thị chi tiết sản phẩm cụ thể.
     */
    public function show(Products $product, Request $request)
    {
        $product = Products::withTrashed()->find($request->id);
        return fetchData($product);
    }


    /**
     * Xóa sản phẩm cụ thể khỏi cơ sở dữ liệu.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')
                         ->with('success', 'Product deleted successfully.');
    }
}
