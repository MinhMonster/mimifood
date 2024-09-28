<?php

namespace App\Http\Controllers\Admin;
use App\Models\Admin\Products; // Import model
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Hiển thị danh sách các sản phẩm.
     */
    public function index()
    {
        $products = Products::withTrashed()->get();

        return response()->json($products);
    }

    /**
     * Lưu trữ sản phẩm mới vào cơ sở dữ liệu.
     */
    public function create(Request $request)
    {
        // 1. Xác thực dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'link' => 'required|string',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
            'quantity' => 'nullable|integer',
        ]);

        // 2. Lưu dữ liệu vào cơ sở dữ liệu
        $product = new Products();
        $product->name = $request->input('name');
        $product->link = $request->input('link');
        $product->price = $request->input('price');
        $product->description = $request->input('description');
        $product->quantity = $request->input('quantity', 0);  // Mặc định là 0 nếu không có giá trị
        $product->save();  // Lưu vào database
        return $product;
    }

    /**
     * Hiển thị chi tiết sản phẩm cụ thể.
     */
    public function show(Products $product, Request $request)
    {
        $product = Products::withTrashed()->find($request->id);
        return response()->json($product);
    }

    /**
     * Hiển thị form chỉnh sửa sản phẩm cụ thể.
     */
    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    /**
     * Cập nhật sản phẩm cụ thể trong cơ sở dữ liệu.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
        ]);

        $product->update($request->all());

        return redirect()->route('products.index')
                         ->with('success', 'Product updated successfully.');
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
