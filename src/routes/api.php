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
        Route::get('/product/list', 'ListProduct');
        Route::get('/product/{id}/detail', 'DetailProduct');
    });

Route::middleware(['auth:sanctum', 'role:' . RoleEnum::ADMIN->value])->group(function () {
    Route::controller(LoginController::class)->group(function () {
        Route::get('/admin/logout', 'logout');
    });

    Route::prefix('/admin')->controller(UserController::class)->group(function () {
        Route::get('/user/list', 'ListUser');
        Route::post('/user/add', 'CreateUser');
        Route::get('/user/{id}/detail', 'DetailUser');
        Route::patch('/user/{id}/edit/data', 'UpdateUserData');
        Route::patch('/user/{id}/edit/password', 'UpdateUserPassword');
        Route::delete('/user/{id}/delete', 'DeleteUser');
    });

    Route::prefix('/admin')->controller(ProductController::class)->group(function () {
        Route::get('/product/list', 'ListProduct');
        Route::post('/product/add', 'CreateProduct');
        Route::get('/product/{id}/detail', 'DetailProduct');
        Route::patch('/product/{id}/edit/data', 'UpdateProductData');
        Route::patch('/product/{id}/edit/image', 'UpdateProductImage');
        Route::delete('/product/{id}/delete', 'DeleteProduct');
    });
});
