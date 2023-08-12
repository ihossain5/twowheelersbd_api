<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(HomeController::class)
->group(function () {
    Route::get('/sliders', 'sliders');
    Route::get('/hot-deals', 'hotDeals');
    Route::get('/categories', 'categories')->name('categories');
    Route::get('/brands', 'brands');
    Route::get('/shops', 'shops');
    Route::get('/blogs', 'blogs');
});

Route::controller(ApiController::class)
->group(function () {
    Route::get('/products', 'products')->name('products');
    Route::get('/products/{id}/details', 'getProductById');
    Route::get('/brand-categories', 'brandCategories');
    Route::get('/category/{id}/products', 'productsByCategory');
    Route::get('/sub-category/{id}/products', 'productsBySubCategory')->name('subcategory.products');
    Route::get('/shop/{id}/products', 'productsByShop')->name('shop.products');
    Route::get('/model/{id}/products', 'productsByModel')->name('model.products');
    Route::get('/models', 'models')->name('all.models');
    Route::get('/accessories', 'accessories')->name('all.accessories');
    Route::get('/motorbikes', 'motorbikes')->name('all.motorbikes');
    Route::get('/motorbikes/{id}/details', 'motorbikeDetails')->name('motorbikes.details');
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact with site owner'], 404);
});
