<?php

namespace App\Traits\Reports\Movement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait ProductMovementTrait
{
    /**
     * Return all records as collection
     *
     * @param  \Illuminate\Http\Request $request
     * @return Object
     */
    public function allProductMovement(Request $request, $productId)
    {
        $flow = $request->get('flow');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $query = DB::table('stock_transfers as st')
            ->where('stp.product_id', $productId)
            ->leftJoin('stock_transfer_products as stp', 'stp.stock_transfer_id', '=', 'st.id')
            ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
            ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
            ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
            ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id');

        return $this->getFilteredProductMovementQuery($query, $request)
            ->where(function ($query) use ($search) {
                $query->where('tt.name', 'like', '%' . $search . '%')
                    ->orWhere('fg.name', 'like', '%' . $search . '%')
                    ->orWhere('tg.name', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                stp.quantity,
                pr.unit,
                round(pr.packing / 100, 0) as packing,
                st.id as id,
                st.date as date,
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
    public function getFilteredProductMovementQuery($query, Request $request)
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

        return $query;
    }
}
