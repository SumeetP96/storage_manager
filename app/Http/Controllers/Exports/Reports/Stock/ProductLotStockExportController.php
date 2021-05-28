<?php

namespace App\Http\Controllers\Exports\Reports\Stock;

use App\Exports\Reports\Stock\ProductLotStockExport;
use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Reports\Stock\ProductLotStockTrait;

class ProductLotStockExportController extends Controller
{
    use ProductLotStockTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
    {
        $records = $this->allProductLotStock($request)->get();
        $pdf = PDF::loadView('exports.reports.stock.product_lot_stock.pdf.all', compact('records'));
        return $pdf->download('product_lot_stock.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request)
    {
        return (new ProductLotStockExport($request))->download('product_lot_stock.xlsx');
    }

    /**
     * Export to Print
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPrint(Request $request)
    {
        $records = $this->allProductLotStock($request)->get();
        return view('exports.reports.stock.product_lot_stock.print.all', compact('records'));
    }
}
