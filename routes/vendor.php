<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\Vendor\MotorbikeController;
use App\Http\Controllers\Vendor\ProductController;
use App\Http\Controllers\VendorAuthController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::controller(VendorController::class)
    ->middleware('auth.jwt', 'shop')
    ->group(function () {
        Route::post('/shop/create-or-update', 'shopCreate');
        Route::get('/shop/details', 'shopDetails')->name('vendor.shop.details');
        Route::get('/shop/videos', 'shopVideos')->name('vendor.shop.videos');
        Route::post('/shop/video/create', 'shopVideoCreate')->name('vendor.shop.video.create');
        Route::get('/shop/video/edit/{id}', 'shopVideoEdit')->name('vendor.shop.video.edit');
        Route::post('/shop/video/update/{id}', 'shopVideoUpdate')->name('vendor.shop.video.update');
        Route::delete('/shop/video/delete/{id}', 'shopVideoDelete')->name('vendor.shop.video.delete');
        Route::get('/shop/deals', 'shopDeals')->name('vendor.shop.deals');
        Route::get('/shop-deals/{id}/products', 'dealsProducts')->name('vendor.shop.deals.products');
        Route::post('/shop-deals/create', 'createDeal');
        Route::get('/shop-deals/edit/{id}', 'editDeal')->name('vendor.edit.deal');
        Route::post('/shop-deals/update/{id}', 'updateDeal');
        Route::delete('/shop-deals/delete/{id}', 'deleteDeal');
        Route::get('/shop/coupons', 'shopCoupons')->name('vendor.shop.coupon');
        Route::post('/shop/coupons/create', 'createCoupon')->name('vendor.shop.create');
        Route::get('/shop/coupons/edit/{id}', 'editCoupon')->name('vendor.shop.edit');
        Route::post('/shop/coupons/update/{id}', 'updateCoupon')->name('vendor.shop.update');
        Route::delete('/shop/coupons/delete/{id}', 'deleteCoupon');
        Route::get('/shop/reviews', 'shopReviews')->name('vendor.shop.review');
        Route::post('/shop/reviews/approve/{id}', 'approveRating')->name('vendor.shop.review.approve');
    });
Route::controller(OrderController::class)
    ->middleware('auth.jwt', 'shop')
    ->group(function () {
        Route::get('/all-orders', 'allOrders')->name('vendor.all.order');
        Route::get('/total-orders', 'totalOrders')->name('vendor.total.order');
        Route::get('/pending-orders', 'pendingOrders')->name('vendor.pending.order');
        Route::get('/orders/details/{id}', 'orderDetails')->name('vendor.details.order');
    });

Route::controller(VendorAuthController::class)
    ->prefix('auth')
    ->middleware('api')
    ->group(function () {
        Route::post('/login', 'login')->name('vendor.login');
        Route::post('/logout', 'logout')->name('vendor.logout');
        Route::post('/register', 'register')->name('vendor.register');
        Route::post('/verify-otp', 'verifyOtp')->name('vendor.otp.verify');
        Route::post('/forget-password', 'forgetPassword')->name('vendor.forget.password');
        Route::post('/recover-password', 'recoverPassword')->name('vendor.recover.password');

        // Route::post('/refresh', 'refresh');
        Route::get('/profile', 'getProfile')->middleware('auth.jwt')->name('vendor.profile');
        Route::post('/profile/update', 'updateProfile')->middleware('auth.jwt')->name('vendor.profile.update');
        Route::post('/password-change', 'passwordChange')->middleware('auth.jwt')->name('vendor.password.update');

    });

Route::controller(ProductController::class)
    ->middleware('auth.jwt', 'shop')
    ->group(function () {
        Route::get('/all-products', 'products')->name('vendor.all.products');
        Route::post('/products/store', 'productStore')->name('vendor.product.store');
        Route::get('/products/edit/{id}', 'productEdit')->name('vendor.product.edit');
        Route::delete('/products/delete/{id}', 'productDelete')->name('vendor.product.delete');
        Route::post('/products/update/{id}', 'productUpdate')->name('vendor.product.update');
        Route::get('/all-category', 'categories')->name('vendor.all.categories');
        Route::get('/category/{id}/subcategory', 'subcategories')->name('vendor.all.subcategories');
        Route::get('/all-brand', 'brands')->name('vendor.all.brands');
        Route::get('/brand/{id}/models', 'brandModels')->name('vendor.all.model');
        Route::get('/all-specifications', 'specifications')->name('vendor.all.specifications');
        Route::delete('/motorbikes/delete/{id}', 'productDelete')->name('vendor.motorbike.delete');
    });

Route::controller(MotorbikeController::class)
    ->middleware('auth.jwt', 'shop')
    ->group(function () {
        Route::get('/all-motorbikes', 'motorbikes')->name('vendor.all.motorbikes');
        Route::post('/motorbikes/store', 'motorbikeStore')->name('vendor.motorbike.store');
        Route::get('/motorbikes/edit/{id}', 'motorbikeEdit')->name('vendor.motorbike.edit');
      
        Route::post('/motorbikes/update/{id}', 'motorbikeUpdate')->name('vendor.motorbike.update');
    });