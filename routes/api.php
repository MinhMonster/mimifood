<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\Admin\ProductsController as AdminProductsController;
use App\Http\Controllers\Admin\FolderController;
use App\Http\Controllers\Admin\FileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/{link}', [ProductsController::class, 'show'])->name('products.show');

// user
Route::group(['middleware' => 'is_user'], function() {
    // ...
});

// admin
Route::group(['prefix' => 'admin'], function () {
    Route::get('/products', [AdminProductsController::class, 'index'])->name('admin.products.index');
    Route::post('/products', [AdminProductsController::class, 'modify'])->name('admin.products.modify');
    Route::get('/products/{id}', [AdminProductsController::class, 'show'])->name('admin.products.show');

    Route::get('/folders', [FolderController::class, 'index'])->name('admin.folders.index');
    Route::post('/folders/create', [FolderController::class, 'create'])->name('admin.folders.create');
    Route::post('/folders/update', [FolderController::class, 'update'])->name('admin.folders.update');
    Route::post('/files', [FileController::class, 'index'])->name('admin.files.index');
    Route::post('/files/uploads', [FileController::class, 'uploads'])->name('admin.files.uploads');
    Route::delete('/files/{id}/delete', [FileController::class, 'delete'])->name('admin.files.delete');
});
