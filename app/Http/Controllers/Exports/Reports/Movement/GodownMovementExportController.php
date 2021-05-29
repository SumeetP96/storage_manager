<?php

namespace App\Http\Controllers\Exports\Reports\Movement;

use App\Exports\Reports\Movement\GodownMovementExport;
use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Reports\Movement\GodownMovementTrait;

class GodownMovementExportController extends Controller
{
    use GodownMovementTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request, $godownType)
    {
        $records = $this->allGodownMovement($request, $request->account_id)->get();
        $pdf = PDF::loadView('exports.reports.movement.godown_movement.pdf.all', compact('records', 'godownType'));
        return $pdf->download('godown_movement.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request, $godownType)
    {
        $name = ($godownType == 'Account') ? 'account_movements' : 'godown_movements';
        return (new GodownMovementExport($request))->download($name . '.xlsx');
    }

    /**
     * Export to Print
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPrint(Request $request, $godownType)
    {
        $records = $this->allGodownMovement($request, $request->account_id)->get();
        return view('exports.reports.movement.godown_movement.print.all', compact('records', 'godownType'));
    }
}
