<?php

namespace App\Http\Controllers\Exports\Reports\Transfers;

use PDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Reports\Transfers\AgentTransfersTrait;
use App\Exports\Reports\Transfers\AgentTransferExport;

class AgentTransfersExportController extends Controller
{
    use AgentTransfersTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
    {
        $records = $this->allAgentTransfers($request, $request->agent_id)->get();
        $pdf = PDF::loadView('exports.reports.transfers.agent_transfers.pdf.all', compact('records'));
        return $pdf->download('agent_transfers.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request)
    {
        return (new AgentTransferExport($request))->download('agent_transfers.xlsx');
    }

    /**
     * Export to Print
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPrint(Request $request)
    {
        $records = $this->allAgentTransfers($request, $request->agent_id)->get();
        return view('exports.reports.transfers.agent_transfers.print.all', compact('records'));
    }
}
