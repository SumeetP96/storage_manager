<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('godowns')->group(function () {
    Route::get('/', 'GodownController@index');
    Route::get('/{id}/show', 'GodownController@show');
    Route::post('/store', 'GodownController@store');
    Route::post('/{id}/update', 'GodownController@update');
    Route::post('/{id}/destroy', 'GodownController@destroy');
    Route::get('/autocomplete/{type}', 'GodownController@autocomplete');
    Route::get('/autocomplete_with_stock', 'GodownController@autocompleteWithStock');
    Route::get('/{id}/details', 'GodownController@details');
});

Route::prefix('products')->group(function () {
    Route::get('/', 'ProductController@index');
    Route::get('/{id}/show', 'ProductController@show');
    Route::post('/store', 'ProductController@store');
    Route::post('/{id}/update', 'ProductController@update');
    Route::post('/{id}/destroy', 'ProductController@destroy');
    Route::get('/autocomplete', 'ProductController@autocomplete');
    Route::get('/autocomplete_with_stock/{id}', 'ProductController@autocompleteWithStock');
    Route::get('/{id}/details/{godownId?}', 'ProductController@details');
});

Route::prefix('agents')->group(function () {
    Route::get('/', 'AgentController@index');
    Route::get('/{id}/show', 'AgentController@show');
    Route::post('/store', 'AgentController@store');
    Route::post('/{id}/update', 'AgentController@update');
    Route::post('/{id}/destroy', 'AgentController@destroy');
    Route::get('/autocomplete', 'AgentController@autocomplete');
});

Route::prefix('purchases')->group(function () {
    Route::get('/', 'PurchaseController@index');
    Route::get('/{id}/show', 'PurchaseController@show');
    Route::post('/store', 'PurchaseController@store');
    Route::post('/{id}/update', 'PurchaseController@update');
    Route::post('/{id}/destroy', 'PurchaseController@destroy');
});

Route::prefix('sales')->group(function () {
    Route::get('/', 'SalesController@index');
    Route::get('/{id}/show', 'SalesController@show');
    Route::post('/store', 'SalesController@store');
    Route::post('/{id}/update', 'SalesController@update');
    Route::post('/{id}/destroy', 'SalesController@destroy');
});

Route::prefix('inter_godowns')->group(function () {
    Route::get('/', 'InterGodownController@index');
    Route::get('/{id}/show', 'InterGodownController@show');
    Route::post('/store', 'InterGodownController@store');
    Route::post('/{id}/update', 'InterGodownController@update');
    Route::post('/{id}/destroy', 'InterGodownController@destroy');
});

Route::prefix('sales')->group(function () {
    Route::get('/', 'SalesController@index');
    Route::get('/{id}/show', 'SalesController@show');
    Route::post('/store', 'SalesController@store');
    Route::post('/{id}/update', 'SalesController@update');
    Route::post('/{id}/destroy', 'SalesController@destroy');
});

Route::prefix('reports')->group(function () {
    Route::get('/products_stock', 'ReportController@productsStock');
    Route::get('/godown_products_stock', 'ReportController@godownProductsStock');
    Route::get('/lot_stock', 'ReportController@lotStock');
    Route::get('/products_lot_stock', 'ReportController@productsLotStock');

    Route::get('/product_movements', 'ReportController@productMovement');
    Route::get('/godown_movements', 'ReportController@godownMovement');

    Route::get('/agent_transfers', 'ReportController@agentTrasnfers');
    Route::get('/all_transfers', 'ReportController@allTransfers');
});

Route::prefix('backup')->group(function () {
    Route::get('/run', 'BackupController@index');
    Route::get('/get_path', 'BackupController@getDefaultPath');
});
