<?php

namespace App\Http\Controllers\Exports;

use PDF;
use App\Traits\ProductTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\ProductExport;
use App\Product;

class ProductExportController extends Controller
{
    use ProductTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
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
    public function allExcel(Request $request)
    {
        return (new ProductExport($request))->download('products.xlsx');
    }

    /**
     * Export to Print
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPrint(Request $request)
    {
        $records = $this->allRecords($request)->get();
        return view('exports.products.print.all', compact('records'));
    }

    /**
     * Export single record as pdf
     *
     * @param  integer $id
     * @return void
     */
    public function singlePdf($id)
    {
        $record = Product::find($id);
        $pdf = PDF::loadView('exports.products.pdf.single', compact('record'));
        return $pdf->download('product.pdf');
    }

    /**
     * Print single product
     *
     * @param  integer $id
     * @return void
     */
    public function singlePrint($id)
    {
        $record = Product::find($id);
        return view('exports.products.print.single', compact('record'));
    }
}
