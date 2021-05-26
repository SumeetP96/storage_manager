<?php

namespace App\Http\Controllers\Exports;

use PDF;
use App\Traits\SaleTrait;
use App\Exports\SaleExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class InterGodownExportController extends Controller
{
    use SaleTrait;

    /**
     * Export to PDF
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allPdf(Request $request)
    {
        $records = $this->allRecords($request)->get();
        $pdf = PDF::loadView('exports.inter_godowns.pdf.all', compact('records'));
        return $pdf->download('inter_godowns.pdf');
    }

    /**
     * Export to Excel
     *
     * @param  Illuminate\Http\Request $request
     * @return void
     */
    public function allExcel(Request $request)
    {
        return (new SaleExport($request))->download('inter_godowns.xlsx');
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
        return view('exports.inter_godowns.print.all', compact('records'));
    }

    /**
     * Export single record as pdf
     *
     * @param  integer $id
     * @return void
     */
    public function singlePdf($id)
    {
        $details = $this->getSingleDetails($id);
        $record = $details['record'];
        $products = $details['products'];
        $pdf = PDF::loadView('exports.inter_godowns.pdf.single', compact('record', 'products'));
        return $pdf->download('inter_godown.pdf');
    }

    /**
     * Print single product
     *
     * @param  integer $id
     * @return void
     */
    public function singlePrint($id)
    {
        $details = $this->getSingleDetails($id);
        $record = $details['record'];
        $products = $details['products'];
        return view('exports.inter_godowns.print.single', compact('record', 'products'));
    }

    /**
     * Get record details
     *
     * @param  mixed $id
     * @return void
     */
    public function getSingleDetails($id)
    {
        $record = DB::table('stock_transfers as st')->where('st.id', $id)
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id')
            ->leftJoin('agents as ag', 'st.agent_id', '=', 'ag.id')
            ->selectRaw('
                st.*,
                DATE_FORMAT(st.date, "%d-%m-%Y") as dateRaw,
                fg.name as fromName,
                fg.address as fromAddress,
                fg.contact_1 as fromContact1,
                fg.contact_2 as fromContact2,
                fg.email as fromEmail,
                tg.name as toName,
                tg.address as toAddress,
                tg.contact_1 as toContact1,
                tg.contact_2 as toContact2,
                tg.email as toEmail,
                ag.name as agentName
            ')
            ->first();

        $products = DB::table('stock_transfer_products as stp')
            ->where('stp.stock_transfer_id', $id)
            ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
            ->selectRaw('
                stp.quantity div 100 as quantityRaw,
                pr.compound_unit as compoundUnit,
                stp.compound_quantity as compoundQuantity,
                stp.compound_quantity div 100 as compoundQuantityRaw,
                pr.name as name,
                pr.unit as unit,
                pr.lot_number as lotNumber
            ')
            ->get();

        return ['record' => $record, 'products' => $products];
    }
}
