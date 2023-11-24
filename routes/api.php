<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::apiResource("/products", ProductController::class);

Route::middleware('x_api_key')->group(function () {
    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products.all');
        Route::get('/product/search/{value}', 'show')->name('product.one');
        Route::post('/product/create', 'store')->name('product.store');
        Route::put('/product/update/{id}', 'update')->name('product.update');
        Route::delete('/product/delete/{id}', 'destroy')->name('product.delete');
        Route::get('/products/price/{inicio}/{final}', 'showRangePrice')->name('product.rangePrice');
    });

    Route::controller(UserController::class)->group(function () {
        Route::get('/users', 'index')->name('users.all');
        Route::post('/user/create', 'store')->name('user.store');
        Route::get('/user/search/{value}', 'show')->name('user.one');
        Route::put('/user/update/{id}', 'update')->name('user.update');
        Route::delete('/user/delete/{id}', 'destroy')->name('user.delete');
    });
});

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index')->name('products.all');
    Route::get('/product/category/{value}', 'showCategory')->name('product.nameProduct');
    Route::get('/product/nameProduct/{value}', 'showNameProduct')->name('product.nameProduct');
    Route::get('/product/price/{inicio}/{final}', 'showRangePrice')->name('product.rangePrice');
});

Route::controller(UserController::class)->group(function () {
    Route::post('/user/singIng', 'store')->name('user.signIn');
    Route::put('/user/updateUser/{id}', 'updateUser')->name('user.updateUser');
});