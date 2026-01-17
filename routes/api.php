<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\NinjaController;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\DragonBallController;
use App\Http\Controllers\AccountPurchaseController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TopUpTransactionsController;
use App\Http\Controllers\WalletTransactionController;
use App\Http\Controllers\NinjaCoinTransactionsController;
use App\Http\Controllers\CarrotTransactionsController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ProductsController as AdminProductsController;
use App\Http\Controllers\Admin\NinjaController as AdminNinjaController;
use App\Http\Controllers\Admin\AvatarController as AdminAvatarController;
use App\Http\Controllers\Admin\DragonBallController as AdminDragonBallController;
use App\Http\Controllers\Admin\DiscountsController as AdminDiscountsController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\TopUpTransactionsController as AdminTopUpTransactionsController;
use App\Http\Controllers\Admin\CarrotTransactionController as AdminCarrotTransactionController;
use App\Http\Controllers\Admin\WalletTransactionController as AdminWalletTransactionController;
use App\Http\Controllers\Admin\AccountPurchaseController as AdminAccountPurchaseController;
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

Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
Route::get('/products/{link}', [ProductsController::class, 'show'])->name('products.show');

// Ninja
Route::get('/ninjas', [NinjaController::class, 'index'])->name('ninjas.index');
Route::get('/ninjas/{code}', [NinjaController::class, 'show'])->name('ninjas.show');
Route::get('/ninja-coin/prices', [NinjaCoinTransactionsController::class, 'prices'])
    ->name('ninja-coin.prices');
Route::get('/carrot/prices', [CarrotTransactionsController::class, 'prices'])
    ->name('carrot.prices');
// Avatar
Route::get('/avatars', [AvatarController::class, 'index'])->name('avatars.index');
Route::get('/avatars/{code}', [AvatarController::class, 'show'])->name('avatars.show');
// Dragon Ball
Route::get('/dragon-balls', [DragonBallController::class, 'index']);
Route::get('/dragon-balls/{code}', [DragonBallController::class, 'show']);

// settings
Route::get('/notification', [SettingsController::class, 'notification'])->name('settings.notification');
// user
Route::group(['middleware' => 'is_user'], function () {
    Route::get('/user', [UserController::class, 'user'])->name('user');
    Route::post('/account-purchases', [AccountPurchaseController::class, 'purchase']);
    Route::get('/account-purchases', [AccountPurchaseController::class, 'index']);
    Route::get('/account-purchases/{id}', [AccountPurchaseController::class, 'show']);
    Route::prefix('top-up')->name('top-up.')->group(function () {
        Route::post('/bank', [TopUpTransactionsController::class, 'store'])->name('bank.store');
        Route::get('/bank/histories', [TopUpTransactionsController::class, 'index'])->name('bank.histories');
    });
    Route::prefix('account')->name('top-up.')->group(function () {
        Route::get('/transactions', [WalletTransactionController::class, 'index'])->name('account.transactions');
    });
    Route::prefix('/ninja-coin')->name('ninja-coin.')->group(function () {
        Route::post('/buy', [NinjaCoinTransactionsController::class, 'store'])
            ->name('store');
        Route::get('/histories', [NinjaCoinTransactionsController::class, 'index'])
            ->name('histories');
    });
    Route::prefix('/carrot')->name('carrot.')->group(function () {
        Route::post('/topup', [CarrotTransactionsController::class, 'store'])
            ->name('store');
        Route::get('/histories', [CarrotTransactionsController::class, 'index'])
            ->name('histories');
    });
});

// admin
Route::post('/admin/login', [AdminAuthController::class, 'login']);
// Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::group(['prefix' => 'admin', 'middleware' => 'is_admin'], function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::post('/users/{user}/top-up', [AdminUserController::class, 'topUp'])->name('admin.users.topUp');
    Route::get('/products', [AdminProductsController::class, 'index'])->name('admin.products.index');
    Route::post('/products', [AdminProductsController::class, 'modify'])->name('admin.products.modify');
    Route::get('/products/{id}', [AdminProductsController::class, 'show'])->name('admin.products.show');

    Route::get('/folders', [FolderController::class, 'index'])->name('admin.folders.index');
    Route::post('/folders/create', [FolderController::class, 'create'])->name('admin.folders.create');
    Route::post('/folders/update', [FolderController::class, 'update'])->name('admin.folders.update');
    Route::post('/files', [FileController::class, 'index'])->name('admin.files.index');
    Route::post('/files/uploads', [FileController::class, 'uploads'])->name('admin.files.uploads');
    Route::delete('/files/{id}/delete', [FileController::class, 'delete'])->name('admin.files.delete');
    Route::group(['prefix' => 'game'], function () {
        // Admin Ninja
        Route::get('/ninjas', [AdminNinjaController::class, 'index'])->name('admin.ninjas.index');
        Route::post('/ninjas/modify', [AdminNinjaController::class, 'modify'])->name('admin.ninjas.modify');
        Route::post('/ninjas/destroy', [AdminNinjaController::class, 'destroy'])->name('admin.ninjas.destroy');
        Route::post('/ninjas/restore', [AdminNinjaController::class, 'restore'])->name('admin.ninjas.restore');
        Route::get('/ninjas/{id}', [AdminNinjaController::class, 'show'])->name('admin.ninjas.show');
        // Admin Avatar
        Route::get('/avatars', [AdminAvatarController::class, 'index'])->name('admin.avatars.index');
        Route::post('/avatars/modify', [AdminAvatarController::class, 'modify'])->name('admin.avatars.modify');
        Route::post('/avatars/destroy', [AdminAvatarController::class, 'destroy'])->name('admin.avatars.destroy');
        Route::post('/avatars/restore', [AdminAvatarController::class, 'restore'])->name('admin.avatars.restore');
        Route::get('/avatars/{id}', [AdminAvatarController::class, 'show'])->name('admin.avatars.show');
        // Admin Dragon ball
        Route::get('/dragon-balls', [AdminDragonBallController::class, 'index'])->name('admin.dragon-balls.index');
        Route::post('/dragon-balls/modify', [AdminDragonBallController::class, 'modify'])->name('admin.dragon-balls.modify');
        Route::post('/dragon-balls/destroy', [AdminDragonBallController::class, 'destroy'])->name('admin.dragon-balls.destroy');
        Route::post('/dragon-balls/restore', [AdminDragonBallController::class, 'restore'])->name('admin.dragon-balls.restore');
        Route::get('/dragon-balls/{id}', [AdminDragonBallController::class, 'show'])->name('admin.dragon-balls.show');
    });
    // Admin Discount
    Route::get('/discounts', [AdminDiscountsController::class, 'index'])->name('admin.discounts.index');
    Route::post('/discounts/modify', [AdminDiscountsController::class, 'modify'])->name('admin.discounts.modify');
    Route::post('/discounts/destroy', [AdminDiscountsController::class, 'destroy'])->name('admin.discounts.destroy');
    Route::post('/discounts/restore', [AdminDiscountsController::class, 'restore'])->name('admin.discounts.restore');
    Route::get('/discounts/{id}', [AdminDiscountsController::class, 'show'])->name('admin.discounts.show');
    // Admin Settings
    Route::get('/settings', [AdminSettingsController::class, 'show'])->name('admin.settings.show');
    Route::post('/settings/modify', [AdminSettingsController::class, 'modify'])->name('admin.settings.modify');
    Route::get('/top-up-transactions', [AdminTopUpTransactionsController::class, 'index'])->name('admin.topUpTransactions.index');
    Route::post('/top-up-transactions/update', [AdminTopUpTransactionsController::class, 'update'])->name('admin.topUpTransactions.update');
    Route::prefix('account-purchases')->name('admin.accountPurchases.')->group(function () {
        Route::get('/', [AdminAccountPurchaseController::class, 'index'])->name('index');
        Route::post('/{id}/update', [AdminAccountPurchaseController::class, 'update'])->name('update');
        Route::post('/{id}/update-account', [AdminAccountPurchaseController::class, 'updateAccount'])->name('updateAccount');
    });

    Route::prefix('wallet-transactions')
        ->name('admin.wallet-transactions.')
        ->controller(AdminWalletTransactionController::class)
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
    Route::prefix('/carrots')->name('carrots.')->group(function () {
        Route::get('/', [AdminCarrotTransactionController::class, 'index'])
            ->name('index');
        Route::post('/update-status', [AdminCarrotTransactionController::class, 'updateStatus'])
            ->name('update-status');
    });
});
