<?php

use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;


Route::controller(VendorController::class)
->group(function () {
    Route::get('/shop/{vendor_id}/details', 'singleShop')->name('vendor.shop.details');
    Route::get('/shop/{id}/videos', 'shopVideos')->name('vendor.shop.videos');
});