<?php

namespace App\Http\Controllers\Exports;

use PDF;
use App\Godown;
use App\Traits\GodownTrait;
use Illuminate\Http\Request;
use App\Exports\GodownExport;
use App\Http\Controllers\Controller;

class GodownExportController extends Controller
{
    use GodownTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
    {
        $godownType = (bool) $request->is_account ? 'Accounts' : 'Godowns';

        $records = $this->allRecords($request)->get();
        $pdf = PDF::loadView('exports.godowns.pdf.all', compact('records', 'godownType'));
        return $pdf->download(strtolower($godownType) . '.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request)
    {
        $godownType = (bool) $request->is_account ? 'accounts' : 'godowns';
        return (new GodownExport($request))->download($godownType . '.xlsx');
    }

    /**
     * Export to Print
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPrint(Request $request)
    {
        $godownType = (bool) $request->is_account ? 'Accounts' : 'Godowns';
        $records = $this->allRecords($request)->get();
        return view('exports.godowns.print.all', compact('records', 'godownType'));
    }

    /**
     * Export single record as pdf
     *
     * @param  integer $id
     * @return void
     */
    public function singlePdf($id)
    {
        $record = Godown::find($id);
        $godownType = (bool) $record->is_account ? 'Account' : 'Godown';
        $pdf = PDF::loadView('exports.godowns.pdf.single', compact('record', 'godownType'));
        return $pdf->download(strtolower($godownType) . '.pdf');
    }

    /**
     * Print single record
     *
     * @param  integer $id
     * @return void
     */
    public function singlePrint($id)
    {
        $record = Godown::find($id);
        $godownType = (bool) $record->is_account ? 'Account' : 'Godown';
        return view('exports.godowns.print.single', compact('record', 'godownType'));
    }
}
