<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\VendorAuthController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

Route::controller(VendorController::class)
    ->middleware('auth.jwt')
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
    });
Route::controller(OrderController::class)
    ->middleware('auth.jwt')
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

    });