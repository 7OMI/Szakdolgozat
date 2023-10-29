<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuditController;

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

Route::group(['middleware' => ['auth', 'verified']], function() {

    Route::group(['controller' => ProductController::class], function() {
        Route::get ('/interface',           'create' )->name('interface');
        Route::post('/product/create',      'store'  )->name('product.create');
        Route::get ('/product/{id}/{mode}', 'edit'   )->name('product.edit')->where('mode', 'new|edit');
        Route::post('/product/{id}/edit',   'update' )->name('product.update');
        Route::get ('/product/{id}/delete', 'destroy')->name('product.delete');
    });

    Route::group(['controller' => AuditController::class], function() {
        Route::get ('/audit/{id}/edit',   'edit'   )->name('audit.edit');
        Route::post('/audit/{id}/edit',   'update' )->name('audit.update');
        Route::get ('/audit/{id}/delete', 'destroy')->name('audit.delete');
    });

});

/*
Route::get('/interface', function () {
    return view('page.interface', ['data'=>["mode"=>"login", "page"=>"index"]]);
})->middleware(['auth', 'verified'])->name('interface');*/
