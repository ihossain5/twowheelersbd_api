<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserMessageController;
use App\Http\Controllers\UserOrderController;
use App\Http\Controllers\WishlishtController;
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


Route::get('/clear-cache/',function(){
    $configCache = Artisan::call('config:cache');
    $clearCache  = Artisan::call('cache:clear');
    $clearRoute  = Artisan::call('route:clear');
    $clearView   = Artisan::call('view:clear');
    $clearConfig   = Artisan::call('config:clear');
    $clearOptimize   = Artisan::call('optimize:clear');
    // return what you want
    return "Finished";
});


Route::controller(AuthController::class)
->prefix('auth')
->middleware('api')
->group(function () {
    Route::post('/register', 'register')->name('user.register');
    Route::post('/login', 'login')->name('user.login');
    Route::post('/register-verify-otp', 'registerOtpVerify')->name('user.otp.verify');
    Route::post('/forget-password', 'forgetPassword')->name('user.forget.password');
    Route::post('/forget-password-verify-otp', 'forgetPasswordOtpVerify')->name('user.forget.password.verify.otp');
    Route::post('/recover-password', 'recoverPassword')->name('user.recover.password');


    Route::post('/logout', 'logout')->name('user.logout');
    // Route::post('/refresh', 'refresh');
    Route::get('/get-user-info', 'getProfile')->middleware('auth.jwt')->name('user.profile');
    Route::post('/update-user-info', 'updateProfie')->middleware('auth.jwt')->name('user.profile.update');
    Route::get('/get-bike-info', 'getBikeInfo')->middleware('auth.jwt')->name('user.bike.info');
    Route::post('/store-bike-info', 'storeBikeInfo')->middleware('auth.jwt')->name('user.bike.info.store');

    Route::post('/user-delivery-address/{id}/update', 'updateDeliveryAddress')->middleware('auth.jwt')->name('user.delivery.address.update');


});

Route::controller(HomeController::class)
->group(function () {
    Route::get('/sliders', 'sliders');
    Route::get('/hot-deals', 'hotDeals');
    Route::get('/categories', 'categories')->name('categories');
    Route::get('/all-categories', 'allCategories')->name('all.categories');
    Route::get('/brands', 'brands')->name('brands');
    Route::get('/all-brands', 'allBrands')->name('all.brands');
    Route::get('/shops', 'shops');
    Route::get('/motorbike-models', 'motorbikeModels');
  
});

Route::controller(ShopController::class)
->group(function () {
    Route::get('/shop-hot-deals', 'shopHotdeals');
    Route::get('/shop/{id}/videos', 'shopVideos');
    Route::get('/shop/{id}/reviews', 'shopReviews');
    Route::get('/single-shop/{id}/details', 'singleShop')->name('shop.details');
    Route::post('/shop/{id}/add-review', 'storeRating')->middleware('jwt.auth');
});

Route::controller(ApiController::class)
->group(function () {
    Route::get('/brand-categories', 'brandCategories');
    Route::get('/brand-wise-models', 'brandWiseModels');
    Route::get('/brand/{id}/brand-category-wise-models', 'brandCategoryWiseModels')->name('brand.category.models');
    Route::get('/models', 'models')->name('all.models');
    Route::get('/models/{model}/details', 'modelDetails')->name('models.details');
    Route::get('/catelogues/{catelogue}/details', 'catelogueDetails')->name('catelogue.details');
    Route::post('resend-otp','otpResend')->name('otp.resend');
});

Route::controller(BlogController::class)
->group(function () {
    Route::get('/blogs', 'blogs');
    Route::get('/blogs/{id}/details', 'details')->name('blog.details');
    Route::post('/blogs/{id}/add-comment', 'addComment')->middleware('auth.jwt');
});

Route::controller(WishlishtController::class)
->middleware('auth.jwt')
->group(function () {
    Route::get('/all-wishlists', 'wishlists');
    Route::post('/add-to-wishlist', 'wishlistAdd')->name('wishlist.add');
    Route::post('/remove-to-wishlist', 'wishlistRemove')->name('wishlist.remove');

});

Route::controller(CouponController::class)
->middleware('auth.jwt')
->group(function () {
    Route::post('/apply-coupon', 'applyCoupon')->name('coupon.apply');
    Route::post('/remove-coupon', 'removeCoupon')->name('remove.apply');

});

Route::controller(UserMessageController::class)
->middleware('auth.jwt')
->group(function () {
    Route::get('/all-messages', 'allMessages');
    Route::get('/get-message/{id}', 'getMessageById')->name('user.send.message');
    Route::post('/send-message', 'sendMessage');
});

Route::controller(UserOrderController::class)
->middleware('auth.jwt')
->group(function () {
    Route::post('/create-order', 'orderCreate');
    Route::get('/all-orders', 'orders');
    Route::get('/order-track/{order_id}', 'orderTrack')->name('order.track');
    Route::get('/order-details/{order_id}', 'orderDetails')->name('order.details');
    Route::post('/cancel-order/{order_id}', 'orderCancel')->name('order.cancel');
    Route::post('/refund-request-order/{order_id}', 'refundRequestOrder')->name('order.refund.request');
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

    Route::post('/product/{id}/add-review', 'storeRating')->middleware('jwt.auth');

    Route::get('/filter-products', 'filterProducts');
    Route::get('/search-products', 'searchProducts');
});

Route::get('colors',function(){
    return response()->json(config('app.colors'));
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact with site owner'], 404);
});
