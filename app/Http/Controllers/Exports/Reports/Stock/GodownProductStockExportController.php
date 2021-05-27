<?php

namespace App\Http\Controllers\Exports\Reports\Stock;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Reports\Stock\GodownProductStockTrait;
use App\Exports\Reports\Stock\GodownProductStockExport;

class GodownProductStockExportController extends Controller
{
    use GodownProductStockTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
    {
        $records = $this->allGodownProductStock($request)->get();
        $pdf = PDF::loadView('exports.reports.stock.godown_product_stock.pdf.all', compact('records'));
        return $pdf->download('godown_product_stock.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request)
    {
        return (new GodownProductStockExport($request))->download('godown_product_stock.xlsx');
    }

    /**
     * Export to Print
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPrint(Request $request)
    {
        $records = $this->allGodownProductStock($request)->get();
        return view('exports.reports.stock.godown_product_stock.print.all', compact('records'));
    }
}
