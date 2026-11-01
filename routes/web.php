<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CashSessionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiningTableController;
use App\Http\Controllers\KitchenController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:admin,manager')->group(function () {
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::resource('users', UserController::class)->except(['show']);
    });

    Route::middleware('role:admin,manager,storekeeper')->group(function () {
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::resource('products', ProductController::class)->except(['show']);

        Route::get('/stock', [StockController::class, 'index'])->name('stock.index');
        Route::post('/stock/ingredients', [StockController::class, 'storeIngredient'])->name('stock.ingredients.store');
        Route::post('/stock/ingredients/{ingredient}/adjust', [StockController::class, 'adjust'])->name('stock.adjust');

        Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
        Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
        Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    });

    Route::middleware('role:admin,manager,waiter,cashier')->group(function () {
        Route::get('/tables', [DiningTableController::class, 'index'])->name('tables.index');
        Route::post('/tables', [DiningTableController::class, 'store'])->name('tables.store');
        Route::patch('/tables/{table}/status', [DiningTableController::class, 'updateStatus'])->name('tables.status');
        Route::delete('/tables/{table}', [DiningTableController::class, 'destroy'])->name('tables.destroy');

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/items', [OrderController::class, 'addItems'])->name('orders.items.add');
        Route::post('/orders/{order}/send-kitchen', [OrderController::class, 'sendToKitchen'])->name('orders.kitchen');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    });

    Route::middleware('role:admin,manager,cook')->group(function () {
        Route::get('/kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
        Route::patch('/kitchen/items/{item}', [KitchenController::class, 'updateItem'])->name('kitchen.items.update');
        Route::post('/kitchen/orders/{order}/ready', [KitchenController::class, 'markOrderReady'])->name('kitchen.ready');
    });

    Route::middleware('role:admin,manager,cashier')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/{order}/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::post('/pos/{order}/pay', [PosController::class, 'pay'])->name('pos.pay');
        Route::get('/pos/{order}/receipt', [PosController::class, 'receipt'])->name('pos.receipt');
        Route::get('/pos/{order}/receipt.pdf', [PosController::class, 'receiptPdf'])->name('pos.receipt.pdf');

        Route::get('/cash-sessions', [CashSessionController::class, 'index'])->name('cash-sessions.index');
        Route::post('/cash-sessions/open', [CashSessionController::class, 'open'])->name('cash-sessions.open');
        Route::get('/cash-sessions/{cashSession}', [CashSessionController::class, 'show'])->name('cash-sessions.show');
        Route::post('/cash-sessions/{cashSession}/close', [CashSessionController::class, 'close'])->name('cash-sessions.close');
    });
});
