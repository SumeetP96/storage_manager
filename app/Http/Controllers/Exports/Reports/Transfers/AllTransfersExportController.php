<?php

namespace App\Http\Controllers\Exports\Reports\Transfers;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Reports\Transfers\AllTransfersTrait;
use App\Exports\Reports\Transfers\AllTransferExport;

class AllTransfersExportController extends Controller
{
    use AllTransfersTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
    {
        $records = $this->allTransfers($request)->get();
        $pdf = PDF::loadView('exports.reports.transfers.all_transfers.pdf.all', compact('records'));
        return $pdf->download('all_transfers.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request)
    {
        return (new AllTransferExport($request))->download('all_transfers.xlsx');
    }

    /**
     * Export to Print
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPrint(Request $request)
    {
        $records = $this->allTransfers($request)->get();
        return view('exports.reports.transfers.all_transfers.print.all', compact('records'));
    }
}
