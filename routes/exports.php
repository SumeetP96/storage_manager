<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'exports', 'middleware' => ['auth']], function(){

    // Products
    Route::get('/pdf/products', 'Exports\ProductExportController@allPdf');
    Route::get('/excel/products', 'Exports\ProductExportController@allExcel');
    Route::get('/print/products', 'Exports\ProductExportController@allPrint');
    Route::get('/pdf/products/{id}', 'Exports\ProductExportController@singlePdf');
    Route::get('/print/products/{id}', 'Exports\ProductExportController@singlePrint');

    // Godowns
    Route::get('/pdf/godowns', 'Exports\GodownExportController@allPdf');
    Route::get('/excel/godowns', 'Exports\GodownExportController@allExcel');
    Route::get('/print/godowns', 'Exports\GodownExportController@allPrint');
    Route::get('/pdf/godowns/{id}', 'Exports\GodownExportController@singlePdf');
    Route::get('/print/godowns/{id}', 'Exports\GodownExportController@singlePrint');

    // Agents
    Route::get('/pdf/agents', 'Exports\AgentExportController@allPdf');
    Route::get('/excel/agents', 'Exports\AgentExportController@allExcel');
    Route::get('/print/agents', 'Exports\AgentExportController@allPrint');
    Route::get('/pdf/agents/{id}', 'Exports\AgentExportController@singlePdf');
    Route::get('/print/agents/{id}', 'Exports\AgentExportController@singlePrint');

});