<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(LoginController::class)->group(function () {
    Route::post('/login', 'Login');
});

Route::controller(UserController::class)->group(function () {
    Route::post('/register', 'Register');
});

Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'ListProduct');
        Route::get('/products/{id}', 'DetailProduct');
    });

Route::middleware(['auth:sanctum', 'role:' . RoleEnum::ADMIN->value])->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/admin/logout', 'logout');
    });

    Route::prefix('/admin')->controller(UserController::class)->group(function () {
        Route::get('/users', 'ListUser');
        Route::post('/users', 'CreateUser');
        Route::get('/users/{id}', 'DetailUser');
        Route::patch('/users/{id}', 'UpdateUserData');
        Route::patch('/users/{id}/password', 'UpdateUserPassword');
        Route::delete('/users/{id}', 'DeleteUser');
    });

    Route::prefix('/admin')->controller(ProductController::class)->group(function () {
        Route::get('/products', 'ListProduct');
        Route::post('/products', 'CreateProduct');
        Route::get('/products/{id}', 'DetailProduct');
        Route::patch('/products/{id}', 'UpdateProductData');
        Route::patch('/products/{id}/image', 'UpdateProductImage');
        Route::delete('/products/{id}', 'DeleteProduct');
    });
});
