<?php

namespace App\Http\Controllers\Exports;

use App\Agent;
use PDF;
use App\Traits\AgentTrait;
use App\Exports\AgentExport;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AgentExportController extends Controller
{
    use AgentTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
    {
        $records = $this->allRecords($request)->get();
        $pdf = PDF::loadView('exports.agents.pdf.all', compact('records'));
        return $pdf->download('agents.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request)
    {
        return (new AgentExport($request))->download('agents.xlsx');
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
        return view('exports.agents.print.all', compact('records'));
    }

    /**
     * Export single record as pdf
     *
     * @param  integer $id
     * @return void
     */
    public function singlePdf($id)
    {
        $record = Agent::find($id);
        $pdf = PDF::loadView('exports.agents.pdf.single', compact('record'));
        return $pdf->download('agent.pdf');
    }

    /**
     * Print single record
     *
     * @param  integer $id
     * @return void
     */
    public function singlePrint($id)
    {
        $record = Agent::find($id);
        return view('exports.agents.print.single', compact('record'));
    }
}
