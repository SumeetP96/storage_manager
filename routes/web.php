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

require('exports.php');

Route::get('/{any}', 'AppController@index')->where('any', '.*');
