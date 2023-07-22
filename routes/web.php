<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/clear-cache/',function(){
    $configCache = Artisan::call('config:cache');
    $clearCache  = Artisan::call('cache:clear');
    $clearRoute  = Artisan::call('route:clear');
    $clearView   = Artisan::call('view:clear');
    // return what you want
    return "Finished";
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact with site owner'], 404);
});