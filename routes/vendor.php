<?php

use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;


Route::controller(VendorController::class)
->group(function () {
    Route::get('/shop/{vendor_id}/details', 'singleShop')->name('vendor.shop.details');
    Route::get('/shop/{id}/videos', 'shopVideos')->name('vendor.shop.videos');
    Route::get('/shop/{id}/deals', 'shopDeals')->name('vendor.shop.deals');
    Route::get('/shop-deals/{id}/products', 'dealsProducts')->name('vendor.shop.deals.products');
});