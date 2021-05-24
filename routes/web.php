<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();

Route::get('/user/password/change', 'AppController@changePassword');
Route::post('/user/password/update', 'AppController@updatePassword')->name('update.password');

Route::group(['prefix' => 'exports', 'middleware' => ['auth']], function(){

    // Products
    Route::get('/pdf/products', 'Exports\ProductExportController@productPdf');
    Route::get('/excel/products', 'Exports\ProductExportController@productExcel');
    Route::get('/print/products', 'Exports\ProductExportController@productPrint');
    Route::get('/pdf/products/{id}', 'Exports\ProductExportController@productSinglePdf');
    Route::get('/excel/products/{id}', 'Exports\ProductExportController@productSingleExcel');
    Route::get('/print/products/{id}', 'Exports\ProductExportController@productSinglePrint');


});

Route::get('/{any}', 'AppController@index')->where('any', '.*');
