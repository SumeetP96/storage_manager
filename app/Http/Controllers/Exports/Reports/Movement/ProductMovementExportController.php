<?php

namespace App\Http\Controllers\Exports\Reports\Movement;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Exports\Reports\Movement\ProductMovementExport;
use App\Traits\Reports\Movement\ProductMovementTrait;

class ProductMovementExportController extends Controller
{
    use ProductMovementTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
    {
        $records = $this->allProductMovement($request, $request->product_id)->get();
        $pdf = PDF::loadView('exports.reports.movement.product_movement.pdf.all', compact('records'));
        return $pdf->download('product_movement.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request)
    {
        return (new ProductMovementExport($request))->download('product_movement.xlsx');
    }

    /**
     * Export to Print
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPrint(Request $request)
    {
        $records = $this->allProductMovement($request, $request->product_id)->get();
        return view('exports.reports.movement.product_movement.print.all', compact('records'));
    }
}
