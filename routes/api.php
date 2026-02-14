<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\TopicController;
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
use App\Http\Controllers\Admin\TopicController as AdminTopicController;
use App\Http\Controllers\Admin\AdminNinjaController;
use App\Http\Controllers\Admin\AdminAvatarController;
use App\Http\Controllers\Admin\AdminDragonBallController;
use App\Http\Controllers\Admin\DiscountController as AdminDiscountController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\TopUpTransactionsController as AdminTopUpTransactionsController;
use App\Http\Controllers\Admin\CarrotTransactionController as AdminCarrotTransactionController;
use App\Http\Controllers\Admin\AdminNinjaCoinTransactionController;
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
Route::get('/ninja-coins/prices', [NinjaCoinTransactionsController::class, 'prices'])
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
// topics
Route::get('/topics', [TopicController::class, 'index']);
Route::get('/topics/{slug}', [TopicController::class, 'show']);


// user
Route::group(['middleware' => 'is_user'], function () {
    Route::get('/user', [UserController::class, 'user'])->name('user');
    Route::post('/account-purchases', [AccountPurchaseController::class, 'purchase']);
    Route::get('/account-purchases', [AccountPurchaseController::class, 'index']);
    Route::get('/account-purchases/{id}', [AccountPurchaseController::class, 'show']);
    Route::prefix('top-up')->name('top-up.')->group(function () {
        Route::prefix('bank')->name('bank.')->group(function () {
            Route::get('/', [TopUpTransactionsController::class, 'index'])->name('index');
            Route::post('/', [TopUpTransactionsController::class, 'store'])->name('store');
        });
    });
    Route::prefix('account')->name('top-up.')->group(function () {
        Route::get('/transactions', [WalletTransactionController::class, 'index'])->name('account.transactions');
    });
    Route::prefix('/ninja-coins')->name('ninja-coins.')->group(function () {
        Route::get('/', [NinjaCoinTransactionsController::class, 'index'])->name('index');
        Route::post('/purchase', [NinjaCoinTransactionsController::class, 'purchase'])->name('purchase');
    });
    Route::prefix('/carrots')->name('carrots.')->group(function () {
        Route::get('/', [CarrotTransactionsController::class, 'index'])->name('index');
        Route::post('/topup', [CarrotTransactionsController::class, 'store'])->name('topup');
        Route::get('/prices', [CarrotTransactionsController::class, 'prices'])->name('prices');
    });
});

// admin
Route::post('/admin/login', [AdminAuthController::class, 'login']);
// Route::post('/admin/register', [AdminAuthController::class, 'register']);
Route::group(['prefix' => 'admin', 'middleware' => 'is_admin'], function () {
    Route::prefix('users')->name('admin.users.')->group(function () {
        Route::get('/', [AdminUserController::class, 'index'])->name('index');
        Route::post('{user}/update-cash', [AdminUserController::class, 'updateCash'])->name('cash');
        Route::post('{user}/update-status', [AdminUserController::class, 'updateStatus'])->name('update-status');
    });
    Route::get('/products', [AdminProductsController::class, 'index'])->name('admin.products.index');
    Route::post('/products', [AdminProductsController::class, 'modify'])->name('admin.products.modify');
    Route::get('/products/{id}', [AdminProductsController::class, 'show'])->name('admin.products.show');

    // Admin topics
    Route::prefix('topics')->group(function () {
        Route::get('/', [AdminTopicController::class, 'index']);
        Route::post('modify', [AdminTopicController::class, 'modify']);
        Route::get('{id}', [AdminTopicController::class, 'show']);
        Route::post('destroy', [AdminTopicController::class, 'destroy']);
        Route::post('restore', [AdminTopicController::class, 'restore']);
    });

    Route::get('/folders', [FolderController::class, 'index'])->name('admin.folders.index');
    Route::post('/folders/create', [FolderController::class, 'create'])->name('admin.folders.create');
    Route::post('/folders/update', [FolderController::class, 'update'])->name('admin.folders.update');
    Route::post('/files', [FileController::class, 'index'])->name('admin.files.index');
    Route::post('/files/uploads', [FileController::class, 'uploads'])->name('admin.files.uploads');
    Route::delete('/files/{id}/delete', [FileController::class, 'delete'])->name('admin.files.delete');
    Route::group(['prefix' => 'game'], function () {
        Route::adminGameCrud('ninjas', AdminNinjaController::class);
        Route::adminGameCrud('avatars', AdminAvatarController::class);
        Route::adminGameCrud('dragon-balls', AdminDragonBallController::class);
    });
    // Admin Discount
    Route::prefix('discounts')->name('admin.discounts.')->controller(AdminDiscountController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('{discount}', 'show')->name('show');
        Route::post('/modify', 'modify')->name('modify');
        Route::delete('{discount}', 'destroy')->name('destroy');
        Route::get('{discount}/active', 'setActive')->name('active');
    });
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
    Route::prefix('/ninja-coins')->name('ninja-coins.')->group(function () {
        Route::get('/', [AdminNinjaCoinTransactionController::class, 'index'])
            ->name('index');
        Route::post('/update-status', [AdminNinjaCoinTransactionController::class, 'updateStatus'])
            ->name('update-status');
    });
});
