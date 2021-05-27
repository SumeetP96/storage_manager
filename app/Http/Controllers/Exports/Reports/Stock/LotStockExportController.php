<?php

namespace App\Http\Controllers\Exports\Reports\Stock;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Reports\Stock\LotStockTrait;
use App\Exports\Reports\Stock\LotStockExport;

class LotStockExportController extends Controller
{
    use LotStockTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
    {
        $records = $this->allRecords($request);
        $pdf = PDF::loadView('exports.reports.stock.lot_stock.pdf.all', compact('records'));
        return $pdf->download('lot_stock.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request)
    {
        return (new LotStockExport($request))->download('lot_stock.xlsx');
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
        return view('exports.reports.stock.lot_stock.print.all', compact('records'));
    }
}
