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

Route::controller(ProductController::class)->group(function () {
    Route::get('/products', 'index')->name('products.all');
    Route::get('/product/search/{value}', 'show')->name('product.one');
    Route::post('/product/create', 'store')->name('product.store');
    Route::put('/product/update/{id}', 'update')->name('product.update');
    Route::delete('/product/delete/{id}', 'destroy')->name('product.delete');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'index')->name('users.all');
    Route::get('/user/search/{value}', 'show')->name('user.one');
    Route::post('/user/create', 'store')->name('user.store');
    Route::put('/user/update/{id}', 'update')->name('user.update');
    Route::delete('/user/delete/{id}', 'destroy')->name('user.delete');
});