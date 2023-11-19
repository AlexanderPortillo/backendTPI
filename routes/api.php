<?php

use App\Http\Controllers\ProductController;
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