<?php

namespace App\Http\Controllers\Exports;

use PDF;
use App\Traits\ProductTrait;
use Illuminate\Http\Request;
use App\Exports\ProductExport;
use App\Http\Controllers\Controller;

class ProductExportController extends Controller
{
    use ProductTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function productPdf(Request $request)
    {
        $records = $this->allRecords($request)->get();
        $pdf = PDF::loadView('exports.products.pdf.all', compact('records'));
        return $pdf->download('products.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function productExcel(Request $request)
    {
        return (new ProductExport($request))->download('products.xlsx');
    }

    /**
     * Export to Print
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function productPrint(Request $request)
    {
        $records = $this->allRecords($request)->get();
        return view('exports.products.print.all', compact('records'));
    }
}
