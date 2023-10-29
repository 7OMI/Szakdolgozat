<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductListController;
use App\Http\Controllers\StatController;

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

Route::group(['controller' => ProductListController::class], function() {
    Route::get ('/list',              'index')->name('list');
    Route::post('/list',              'index');
    Route::post('/list/{product_id}', 'show' );
});

Route::group(['controller' => StatController::class], function() {
    Route::get ('/stat',              'index')->name('stat');
});
