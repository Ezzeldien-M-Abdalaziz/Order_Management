<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Customer\CustomerController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->group(function (){
    Route::POST('/login', [AdminController::class, 'login']);
    Route::middleware(['set_sanctum_guard', 'auth:sanctum'])->group(function () {

        Route::GET('/admins/', action: [AdminController::class, 'index']);
    });


});

Route::prefix('/')->group(function () {
    Route::middleware(['set_sanctum_guard', 'auth:sanctum'])->group(function () {

        Route::GET('/customers/', action: [CustomerController::class, 'index']);
    });
    Route::post('/login', [CustomerController::class, 'login']);
});


