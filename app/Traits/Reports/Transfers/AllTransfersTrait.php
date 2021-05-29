<?php

namespace App\Traits\Reports\Transfers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait AllTransfersTrait
{
    /**
     * Return all records as collection
     *
     * @param  \Illuminate\Http\Request $request
     * @return Object
     */
    public function allTransfers(Request $request)
    {
        $flow = $request->get('flow');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $query = DB::table('stock_transfers as st')
            ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
            ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
            ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id');

        return $this->getFilteredAllTransfersQuery($query, $request)
            ->where(function ($query) use ($search) {
                $query->where('tt.name', 'like', '%' . $search . '%')
                ->orWhere('fg.name', 'like', '%' . $search . '%')
                ->orWhere('st.invoice_no', 'like', '%' . $search . '%')
                ->orWhere('tg.name', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                st.id as id,
                st.invoice_no as invoiceNo,
                st.date as date,
                st.updated_at,
                st.transfer_type_id as ttid,
                tg.name as toName,
                fg.name as fromName,
                tt.name as transferType
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
    public function getFilteredAllTransfersQuery($query, Request $request)
    {
        // Date range filter
        $toDate = $request->get('to');
        $fromDate = $request->get('from');
        if (!is_null($fromDate) && !is_null($toDate)) {
            $query->whereDate('st.date', '<=', $toDate)->whereDate('st.date', '>=', $fromDate);
        }

        // Transfer type only / except
        $transferTypeOnlyId = $request->get('transferTypeOnlyId');
        if (!is_null($transferTypeOnlyId)) $query->whereIn('st.transfer_type_id', explode(',', $transferTypeOnlyId));
        $transferTypeExceptId = $request->get('transferTypeExceptId');
        if (!is_null($transferTypeExceptId)) $query->whereNotIn('st.transfer_type_id', explode(',', $transferTypeExceptId));

        // Invoice no filters with / without
        $invoiceNo = $request->get('invoiceNo');
        if ($invoiceNo == 'with') $query->where(function ($query) {
            $query->whereNotNull('st.invoice_no')->where('st.invoice_no', '!=', '');
        });
        if ($invoiceNo == 'without') $query->where(function ($query) {
            $query->whereNull('st.invoice_no')->orWhere('st.invoice_no', '');
        });

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

        return $query;
    }
}
