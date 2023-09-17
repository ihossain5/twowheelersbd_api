<?php

use App\Http\Controllers\VendorAuthController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;


Route::controller(VendorController::class)
->middleware('auth.jwt')
->group(function () {
    Route::get('/shop/details', 'shopDetails')->name('vendor.shop.details');
    Route::get('/shop/videos', 'shopVideos')->name('vendor.shop.videos');
    Route::get('/shop/deals', 'shopDeals')->name('vendor.shop.deals');
    Route::get('/shop-deals/{id}/products', 'dealsProducts')->name('vendor.shop.deals.products');
});


Route::controller(VendorAuthController::class)
->prefix('auth')
->middleware('api')
->group(function () {
    Route::post('/login', 'login')->name('vendor.login');
    Route::post('/logout', 'logout')->name('vendor.logout');
    Route::post('/register', 'register')->name('vendor.register');
    Route::post('/verify-otp', 'verifyOtp')->name('vendor.otp.verify');
    // Route::post('/refresh', 'refresh');
    Route::post('/me', 'getProfile')->middleware('auth.jwt')->name('vendor.profile');


});