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

        return $this->getFilteredQuery($query, $request)
            ->where(function ($query) use ($search) {
                $query->where('fg.name', 'like', '%' . $search . '%')
                    ->orWhere('ag.name', 'like', '%' . $search . '%')
                    ->orWhere('st.invoice_no', 'like', '%' . $search . '%')
                    ->orWhere('tg.name', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                st.id,
                st.date,
                st.invoice_no as invoiceNo,
                fg.name as fromName,
                tg.name as toName,
                ag.name as agent,
                st.remarks,
                st.updated_at,
                st.created_at
            ')
            ->orderBy($sortBy, $flow);
    }

    /**
     * Get filtered query
     *
     * @param  mixed $query
     * @param  mixed $request
     * @return Object
     */
    public function getFilteredQuery($query, Request $request)
    {
        // Date range filters
        $toDate = $request->get('to');
        $fromDate = $request->get('from');
        if (!is_null($fromDate) && !is_null($toDate)) {
            $query->whereDate('st.date', '<=', $toDate)->whereDate('st.date', '>=', $fromDate);
        }

        // Invoice no filters with / without
        $invoiceNo = $request->get('invoiceNo');
        if ($invoiceNo == 'with') $query->whereNotNull('st.invoice_no');
        if ($invoiceNo == 'without') $query->where(function ($query) {
            $query->whereNull('st.invoice_no')->orWhere('st.invoice_no', '');
        });

        // Agent only except
        $agentOnlyId = $request->get('agentOnlyId');
        if (!is_null($agentOnlyId)) $query->whereIn('st.agent_id', explode(',', $agentOnlyId));
        $agentExceptId = $request->get('agentExceptId');
        if (!is_null($agentExceptId)) $query->whereNotIn('st.agent_id', explode(',', $agentExceptId));

        // From godown only except
        $fromGodownOnlyId = $request->get('fromGodownOnlyId');
        if (!is_null($fromGodownOnlyId)) $query->whereIn('st.from_godown_id', explode(',', $fromGodownOnlyId));
        $fromGodownExceptId = $request->get('fromGodownExceptId');
        if (!is_null($fromGodownExceptId)) $query->whereNotIn('st.from_godown_id', explode(',', $fromGodownExceptId));

        // To godown only except
        $toGodownOnlyId = $request->get('toGodownOnlyId');
        if (!is_null($toGodownOnlyId)) $query->whereIn('st.to_godown_id', explode(',', $toGodownOnlyId));
        $toGodownExceptId = $request->get('toGodownExceptId');
        if (!is_null($toGodownExceptId)) $query->whereNotIn('st.to_godown_id', explode(',', $toGodownExceptId));

        // Updated at date range
        $updatedFromDate = $request->get('updatedFrom');
        $updatedToDate = $request->get('updatedTo');
        if (!is_null($updatedFromDate) && !is_null($updatedToDate)) {
            $query->whereDate('st.updated_at', '<=', $updatedToDate)->whereDate('st.updated_at', '>=', $updatedFromDate);
        }

        // Created at date range
        $createdFromDate = $request->get('createdFrom');
        $createdToDate = $request->get('createdTo');
        if (!is_null($createdFromDate) && !is_null($createdToDate)) {
            $query->whereDate('st.created_at', '<=', $createdToDate)->whereDate('st.created_at', '>=', $createdFromDate);
        }

        return $query;
    }
}
