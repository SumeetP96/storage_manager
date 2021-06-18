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

Route::prefix('autofills')->group(function () {
    Route::get('/products/distinct_units', 'Autofill\ProductAutofillController@distinctUnits');
    Route::get('/products/distinct_compound_units', 'Autofill\ProductAutofillController@distinctCompoundUnits');
    Route::get('/products/used_by_godowns/{godownId}', 'Autofill\ProductAutofillController@usedByGodown');

    Route::get('/agents/with_transactions', 'Autofill\AgentAutofillController@withTransactions');

    Route::get('/godowns/with_stock', 'Autofill\GodownAutofillController@withStock');
    Route::get('/godowns/with_stock_products', 'Autofill\GodownAutofillController@withStockProducts');
    Route::get('/godowns/to_with_transactions/{transferType}', 'Autofill\GodownAutofillController@toWithTransactions');
    Route::get('/godowns/from_with_transactions/{transferType}', 'Autofill\GodownAutofillController@fromWithTransactions');
    Route::get('/godowns/to_used_by_product/{productId}', 'Autofill\GodownAutofillController@toUsedByProduct');
    Route::get('/godowns/from_used_by_product/{productId}', 'Autofill\GodownAutofillController@fromUsedByProduct');
    Route::get('/godowns/used_by_godowns/{godownId}', 'Autofill\GodownAutofillController@usedByGodown');
    Route::get('/godowns/to_used_by_agent/{agentId}', 'Autofill\GodownAutofillController@toUsedByAgent');
    Route::get('/godowns/from_used_by_agent/{agentId}', 'Autofill\GodownAutofillController@fromUsedByAgent');
    Route::get('/godowns/to_all', 'Autofill\GodownAutofillController@toAll');
    Route::get('/godowns/from_all', 'Autofill\GodownAutofillController@fromAll');

    Route::get('/transfer_types/used_by_product/{productId}', 'Autofill\TransferTypeAutofillController@usedByProduct');
    Route::get('/transfer_types/used_by_godowns/{godownId}', 'Autofill\TransferTypeAutofillController@usedByGodown');
    Route::get('/transfer_types/used_by_agent/{agentId}', 'Autofill\TransferTypeAutofillController@usedByAgent');
    Route::get('/transfer_types/all', 'Autofill\TransferTypeAutofillController@all');
});

Route::prefix('godowns')->group(function () {
    Route::get('/', 'GodownController@index');
    Route::get('/{id}/show', 'GodownController@show');
    Route::post('/store', 'GodownController@store');
    Route::post('/{id}/update', 'GodownController@update');
    Route::post('/{id}/destroy', 'GodownController@destroy');

    Route::get('/{id}/details', 'GodownController@details');
    Route::get('/autocomplete/{type}', 'GodownController@autocomplete');
    Route::get('/autocomplete_with_stock', 'GodownController@autocompleteWithStock');
    Route::get('/autocomplete_with_transfers', 'GodownController@autocompleteWithTransfer');
});

Route::prefix('products')->group(function () {
    Route::get('/', 'ProductController@index');
    Route::get('/{id}/show', 'ProductController@show');
    Route::post('/store', 'ProductController@store');
    Route::post('/{id}/update', 'ProductController@update');
    Route::post('/{id}/destroy', 'ProductController@destroy');

    Route::get('/{id}/details/{godownId?}', 'ProductController@details');
    Route::get('/autocomplete', 'ProductController@autocomplete');
    Route::get('/autocomplete_with_stock/{id?}', 'ProductController@autocompleteWithStock');
    Route::get('/{id}/stock_details/{godownId}/{lotNumber}', 'ProductController@stockDetails');
});

Route::prefix('agents')->group(function () {
    Route::get('/', 'AgentController@index');
    Route::get('/{id}/show', 'AgentController@show');
    Route::post('/store', 'AgentController@store');
    Route::post('/{id}/update', 'AgentController@update');
    Route::post('/{id}/destroy', 'AgentController@destroy');
    Route::get('/autocomplete', 'AgentController@autocomplete');
    Route::get('/autocomplete_with_transfers', 'AgentController@autocompleteWithTransfer');
});

Route::prefix('purchases')->group(function () {
    Route::get('/', 'PurchaseController@index');
    Route::get('/new', 'PurchaseController@new');
    Route::get('/{id}/show', 'PurchaseController@show');
    Route::get('/transfer_products/{purchaseId}', 'PurchaseController@showTransferProducts');
    Route::post('/store', 'PurchaseController@store');
    Route::post('/{id}/update', 'PurchaseController@update');
    Route::post('/{id}/destroy', 'PurchaseController@destroy');
});

Route::prefix('sales')->group(function () {
    Route::get('/', 'SalesController@index');
    Route::get('/new', 'SalesController@new');
    Route::get('/{id}/show', 'SalesController@show');
    Route::post('/store', 'SalesController@store');
    Route::post('/{id}/update', 'SalesController@update');
    Route::post('/{id}/destroy', 'SalesController@destroy');
});

Route::prefix('inter_godowns')->group(function () {
    Route::get('/', 'InterGodownController@index');
    Route::get('/{id}/show', 'InterGodownController@show');
    Route::get('/transfer_products/{transferId}', 'InterGodownController@showTransferProducts');
    Route::post('/store', 'InterGodownController@store');
    Route::post('/{id}/update', 'InterGodownController@update');
    Route::post('/{id}/destroy', 'InterGodownController@destroy');
});

Route::prefix('sales')->group(function () {
    Route::get('/', 'SalesController@index');
    Route::get('/{id}/show', 'SalesController@show');
    Route::get('/transfer_products/{salesId}', 'SalesController@showTransferProducts');
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

Route::prefix('settings')->group(function () {
    Route::get('/get_lock_date', 'SettingController@getLockDate');
    Route::post('/lock_date/update', 'SettingController@update');
});
