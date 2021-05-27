<?php

namespace App\Http\Controllers\Exports\Reports\Stock;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Reports\Stock\ProductStockTrait;
use App\Exports\Reports\Stock\ProductStockExport;

class ProductStockExportController extends Controller
{
    use ProductStockTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
    {
        $records = $this->allRecords($request);
        $pdf = PDF::loadView('exports.reports.stock.product_stock.pdf.all', compact('records'));
        return $pdf->download('product_stock.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request)
    {
        return (new ProductStockExport($request))->download('product_stock.xlsx');
    }

    /**
     * Export to Print
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPrint(Request $request)
    {
        $records = $this->allRecords($request);
        return view('exports.reports.stock.product_stock.print.all', compact('records'));
    }
}
