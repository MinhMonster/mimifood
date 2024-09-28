<?php

namespace App\Http\Controllers;
use App\Models\Products; // Import model

use Illuminate\Http\Request;

class ProductsController extends Controller
{
    /**
     * Hiển thị danh sách các sản phẩm.
     */
    public function index()
    {
        $products = Products::all();

        return response()->json($products);
    }

    /**
     * Hiển thị chi tiết sản phẩm cụ thể.
     */
    public function show(Products $product, Request $request)
    {
        $product = Products::where('link', $request->link)->first();
        if(!$product) {
            abort('404');
        };
        return $product;
    }
}
