<?php

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
    Route::get('/categories', 'categories');
    Route::get('/brands', 'brands');
    Route::get('/shops', 'shops');
    Route::get('/products', 'products');
    Route::get('/blogs', 'blogs');
});

Route::fallback(function(){
    return response()->json([
        'message' => 'Page Not Found. If error persists, contact with site owner'], 404);
});
