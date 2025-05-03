<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\OrderController;
use App\Http\Controllers\Api\Admin\ProductController;
use App\Http\Controllers\Api\Customer\CustomerController;
use App\Http\Controllers\Api\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Api\Customer\ProductController as CustomerProductController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;



Route::prefix('admin')->group(function (){
    Route::POST('/login', [AdminController::class, 'login']);
    Route::middleware(['set_sanctum_guard', 'auth:admin'])->group(function () {

        //products
        Route::get('/products', [ProductController::class, 'getProducts']);
        Route::get('/products/{product}', [ProductController::class, 'getProduct']);
        Route::post('/products/create', [ProductController::class, 'createProduct']);
        Route::put('/products/update', [ProductController::class, 'updateProduct']);
        Route::delete('/products/delete/{product}', [ProductController::class, 'deleteProduct']);

        //orders
        Route::get('/orders', [OrderController::class, 'getOrders']);
        Route::get('/orders/view/{order}', [OrderController::class, 'getOrder']);
        Route::get('/orders/shippedorders', [OrderController::class, 'shippedOrders']);
        Route::delete('/orders/delete/{order}', [OrderController::class, 'deleteOrder']);
        Route::put('/orders/change-status', [OrderController::class, 'changeStatus']);
        Route::get('/orders/stats', [OrderController::class, 'getStats']);
    });
});


Route::post('/register', [CustomerController::class, 'register']);

Route::prefix('/')->group(function () {
    Route::middleware(['set_sanctum_guard', 'auth:customer'])->group(function () {

         //products
         Route::get('/products', [CustomerProductController::class, 'getProducts']);
         Route::get('/products/{product}', [ProductController::class, 'getProduct']);

         //orders
         Route::get('/orders', [CustomerOrderController::class, 'getOrders']);
         Route::get('/orders/view/{order}', [CustomerOrderController::class, 'getOrder']);
         Route::post('/orders/create', [CustomerOrderController::class, 'createOrder']);
         Route::delete('/orders/{order}', [CustomerOrderController::class, 'deleteOrder']);

    });
    Route::post('/login', [CustomerController::class, 'login']);
});


