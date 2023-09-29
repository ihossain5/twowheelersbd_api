<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
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

Route::get('/clear-cache/',function(){
    $configCache = Artisan::call('config:cache');
    $clearCache  = Artisan::call('cache:clear');
    $clearRoute  = Artisan::call('route:clear');
    $clearView   = Artisan::call('view:clear');
    // return what you want
    return "Finished";
});


Route::controller(AuthController::class)
->prefix('auth')
->middleware('api')
->group(function () {
    Route::post('/login', 'login')->name('user.login');
    Route::post('/logout', 'logout')->name('user.logout');
    // Route::post('/refresh', 'refresh');
    Route::post('/me', 'getProfile')->middleware('auth.jwt')->name('user.profile');


});

Route::controller(HomeController::class)
->group(function () {
    Route::get('/sliders', 'sliders');
    Route::get('/hot-deals', 'hotDeals');
    Route::get('/categories', 'categories')->name('categories');
    Route::get('/brands', 'brands');
    Route::get('/shops', 'shops');
    Route::get('/motorbike-models', 'motorbikeModels');
  
});

Route::controller(ShopController::class)
->group(function () {
    Route::get('/shop-hot-deals', 'shopHotdeals');
    Route::get('/shop/{id}/videos', 'shopVideos');
    Route::get('/shop/{id}/reviews', 'shopReviews');
    Route::get('/single-shop/{id}/details', 'singleShop')->name('shop.details');
});

Route::controller(ApiController::class)
->group(function () {
    Route::get('/brand-categories', 'brandCategories');
    Route::get('/brand-wise-models', 'brandWiseModels');
    Route::get('/brand/{id}/brand-category-wise-models', 'brandCategoryWiseModels')->name('brand.category.models');
    Route::get('/models', 'models')->name('all.models');
});

Route::controller(BlogController::class)
->group(function () {
    Route::get('/blogs', 'blogs');
    Route::get('/blogs/{id}/details', 'details')->name('blog.details');
});


Route::controller(ProductController::class)
->group(function () {
    Route::get('/category/{id}/products', 'productsByCategory');
    Route::get('/sub-category/{id}/products', 'productsBySubCategory')->name('subcategory.products');
    Route::get('/shop/{id}/products', 'productsByShop')->name('shop.products');
    Route::get('/model/{id}/products', 'productsByModel')->name('model.products');
    Route::get('/hot-deals/{id}/products', 'productsByHotDeal');
    Route::get('/shop/{id}/top-selling/products', 'shopWiseTopProducts');
    Route::get('/shop/{id}/new-arraival/products', 'shopWiseNewArraivalProducts');

    Route::get('/products', 'products')->name('products');
    Route::get('/products/{id}/details', 'getProductById')->name('product.details');

    Route::get('/accessories', 'accessories')->name('all.accessories');
    Route::get('/model/{id}/motorbikes', 'motorbikes')->name('model.motorbikes');
    Route::get('/motorbikes/{id}/details', 'motorbikeDetails')->name('motorbikes.details');
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact with site owner'], 404);
});
