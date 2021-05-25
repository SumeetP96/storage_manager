<?php

namespace App\Traits;

use App\StockTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait PurchaseTrait
{
    /**
     * Return all records as collection
     *
     * @param  \Illuminate\Http\Request $request
     * @return Object
     */
    public function allRecords(Request $request)
    {
        $flow = $request->get('flow');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $query = DB::table('stock_transfers as st')
            ->where('st.transfer_type_id', StockTransfer::PURCHASE)
            ->leftJoin('agents as ag', 'st.agent_id', '=', 'ag.id')
            ->leftJoin('godowns as fg', 'st.from_godown_id', '=', 'fg.id')
            ->leftJoin('godowns as tg', 'st.to_godown_id', '=', 'tg.id');

        // Filters
        $toDate = $request->get('to');
        $fromDate = $request->get('from');
        if (!is_null($fromDate) && !is_null($toDate)) {
            $query->whereDate('st.date', '<=', $toDate)->whereDate('st.date', '>=', $fromDate);
        }

        return $query->where(function ($query) use ($search) {
            $query->where('fg.name', 'like', '%' . $search . '%')
                ->orWhere('ag.name', 'like', '%' . $search . '%')
                ->orWhere('st.invoice_no', 'like', '%' . $search . '%')
                ->orWhere('tg.name', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                st.id,
                st.date,
                st.remarks,
                st.invoice_no as invoiceNo,
                st.updated_at,
                st.created_at,
                fg.name as fromName,
                tg.name as toName,
                ag.name as agent
            ')
            ->orderBy($sortBy, $flow);;
    }
}
