<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

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

Route::group(['controller' => HomeController::class], function() {
    Route::get ('/',       'index'  )->name('home');
    Route::post('/login',  'store'  )->name('login');
    Route::get ('/logout', 'destroy')->name('logout');
});

Route::get('/interface', function () {
    return view('page.interface', ['data'=>["mode"=>"login", "page"=>"index"]]);
})->middleware(['auth', 'verified'])->name('interface');

