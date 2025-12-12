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
        Route::get('/product', 'ListProduct');
        Route::get('/product/{id}', 'DetailProduct');
    });

Route::middleware(['auth:sanctum', 'role:' . RoleEnum::ADMIN->value])->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/admin/logout', 'logout');
    });

    Route::prefix('/admin')->controller(UserController::class)->group(function () {
        Route::get('/user', 'ListUser');
        Route::post('/user', 'CreateUser');
        Route::get('/user/{id}', 'DetailUser');
        Route::patch('/user/{id}/data', 'UpdateUserData');
        Route::patch('/user/{id}/password', 'UpdateUserPassword');
        Route::delete('/user/{id}/delete', 'DeleteUser');
    });

    Route::prefix('/admin')->controller(ProductController::class)->group(function () {
        Route::get('/product', 'ListProduct');
        Route::post('/product', 'CreateProduct');
        Route::get('/product/{id}', 'DetailProduct');
        Route::patch('/product/{id}/data', 'UpdateProductData');
        Route::patch('/product/{id}/image', 'UpdateProductImage');
        Route::delete('/product/{id}', 'DeleteProduct');
    });
});
