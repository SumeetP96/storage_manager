<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'exports', 'middleware' => ['auth']], function() {

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

    // Purchases
    Route::get('/pdf/purchases', 'Exports\PurchaseExportController@allPdf');
    Route::get('/excel/purchases', 'Exports\PurchaseExportController@allExcel');
    Route::get('/print/purchases', 'Exports\PurchaseExportController@allPrint');
    Route::get('/pdf/purchases/{id}', 'Exports\PurchaseExportController@singlePdf');
    Route::get('/print/purchases/{id}', 'Exports\PurchaseExportController@singlePrint');

    // Sales
    Route::get('/pdf/sales', 'Exports\SaleExportController@allPdf');
    Route::get('/excel/sales', 'Exports\SaleExportController@allExcel');
    Route::get('/print/sales', 'Exports\SaleExportController@allPrint');
    Route::get('/pdf/sales/{id}', 'Exports\SaleExportController@singlePdf');
    Route::get('/pdf/sales/delivery_slip/{id}', 'Exports\SaleExportController@deliverySlip');
    Route::get('/print/sales/{id}', 'Exports\SaleExportController@singlePrint');

    // Inter godowns
    Route::get('/pdf/inter_godowns', 'Exports\InterGodownExportController@allPdf');
    Route::get('/excel/inter_godowns', 'Exports\InterGodownExportController@allExcel');
    Route::get('/print/inter_godowns', 'Exports\InterGodownExportController@allPrint');
    Route::get('/pdf/inter_godowns/{id}', 'Exports\InterGodownExportController@singlePdf');
    Route::get('/print/inter_godowns/{id}', 'Exports\InterGodownExportController@singlePrint');

    // Inter godowns
    Route::get('/pdf/inter_godowns', 'Exports\InterGodownExportController@allPdf');
    Route::get('/excel/inter_godowns', 'Exports\InterGodownExportController@allExcel');
    Route::get('/print/inter_godowns', 'Exports\InterGodownExportController@allPrint');
    Route::get('/pdf/inter_godowns/{id}', 'Exports\InterGodownExportController@singlePdf');
    Route::get('/print/inter_godowns/{id}', 'Exports\InterGodownExportController@singlePrint');

    // Product Stock
    Route::get('/pdf/reports/product_stock', 'Exports\Reports\Stock\ProductStockExportController@allPdf');
    Route::get('/excel/reports/product_stock', 'Exports\Reports\Stock\ProductStockExportController@allExcel');
    Route::get('/print/reports/product_stock', 'Exports\Reports\Stock\ProductStockExportController@allPrint');
    // Godown Product Stock
    Route::get('/pdf/reports/godown_product_stock', 'Exports\Reports\Stock\GodownProductStockExportController@allPdf');
    Route::get('/excel/reports/godown_product_stock', 'Exports\Reports\Stock\GodownProductStockExportController@allExcel');
    Route::get('/print/reports/godown_product_stock', 'Exports\Reports\Stock\GodownProductStockExportController@allPrint');
    // Lot Stock
    Route::get('/pdf/reports/lot_stock', 'Exports\Reports\Stock\LotStockExportController@allPdf');
    Route::get('/excel/reports/lot_stock', 'Exports\Reports\Stock\LotStockExportController@allExcel');
    Route::get('/print/reports/lot_stock', 'Exports\Reports\Stock\LotStockExportController@allPrint');
    // Product Lot Stock
    Route::get('/pdf/reports/product_lot_stock', 'Exports\Reports\Stock\ProductLotStockExportController@allPdf');
    Route::get('/excel/reports/product_lot_stock', 'Exports\Reports\Stock\ProductLotStockExportController@allExcel');
    Route::get('/print/reports/product_lot_stock', 'Exports\Reports\Stock\ProductLotStockExportController@allPrint');

    // Product Movement
    Route::get('/pdf/reports/product_movement', 'Exports\Reports\Movement\ProductMovementExportController@allPdf');
    Route::get('/excel/reports/product_movement', 'Exports\Reports\Movement\ProductMovementExportController@allExcel');
    Route::get('/print/reports/product_movement', 'Exports\Reports\Movement\ProductMovementExportController@allPrint');
    // Godown / Account Movement
    Route::get('/pdf/reports/godown_movement/{godownType}', 'Exports\Reports\Movement\GodownMovementExportController@allPdf');
    Route::get('/excel/reports/godown_movement/{godownType}', 'Exports\Reports\Movement\GodownMovementExportController@allExcel');
    Route::get('/print/reports/godown_movement/{godownType}', 'Exports\Reports\Movement\GodownMovementExportController@allPrint');

    // Agent Transfers
    Route::get('/pdf/reports/agent_transfers', 'Exports\Reports\Transfers\AgentTransfersExportController@allPdf');
    Route::get('/excel/reports/agent_transfers', 'Exports\Reports\Transfers\AgentTransfersExportController@allExcel');
    Route::get('/print/reports/agent_transfers', 'Exports\Reports\Transfers\AgentTransfersExportController@allPrint');

    // All Transfers
    Route::get('/pdf/reports/all_transfers', 'Exports\Reports\Transfers\AllTransfersExportController@allPdf');
    Route::get('/excel/reports/all_transfers', 'Exports\Reports\Transfers\AllTransfersExportController@allExcel');
    Route::get('/print/reports/all_transfers', 'Exports\Reports\Transfers\AllTransfersExportController@allPrint');

    // Invoices
    Route::get('/pdf/invoices/{month}', 'Exports\Invoices\InvoiceExportController@invoice');
});
