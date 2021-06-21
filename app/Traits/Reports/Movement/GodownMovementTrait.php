<?php

namespace App\Traits\Reports\Movement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait GodownMovementTrait
{
    /**
     * Return all records as collection
     *
     * @param  \Illuminate\Http\Request $request
     * @return Object
     */
    public function allGodownMovement(Request $request, $godownId)
    {
        $flow = $request->get('flow');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $query = DB::table('stock_transfers as st')
            ->leftJoin('stock_transfer_products as stp', 'stp.stock_transfer_id', '=', 'st.id')
            ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
            ->leftJoin('godowns as tg', 'tg.id', '=', 'st.to_godown_id')
            ->leftJoin('godowns as fg', 'fg.id', '=', 'st.from_godown_id')
            ->leftJoin('transfer_types as tt', 'tt.id', '=', 'st.transfer_type_id');

        return $this->getFilteredGodownMovementQuery($query, $request)
            ->where(function ($query) use ($godownId) {
                $query->where('st.to_godown_id', $godownId)
                    ->orWhere('st.from_godown_id', $godownId);
            })
            ->where(function ($query) use ($search) {
                $query->where('tt.name', 'like', '%' . $search . '%')
                ->orWhere('fg.name', 'like', '%' . $search . '%')
                ->orWhere('tg.name', 'like', '%' . $search . '%')
                ->orWhere('pr.name', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                st.id as id,
                stp.quantity,
                round(pr.packing / 100, 0) as packing,
                st.date as date,
                st.from_godown_id as fromId,
                st.to_godown_id as toId,
                st.transfer_type_id as ttid,
                IF(st.from_godown_id = ?, tg.name, fg.name) as name,
                pr.id as productId,
                pr.unit,
                pr.name as productName,
                stp.lot_number as lotNumber,
                tt.name as transferType
            ', [$godownId])
            ->orderBy($sortBy, $flow);
    }

    /**
     * Get filtered query
     *
     * @param  mixed $query
     * @param  mixed $request
     * @return Object
     */
    public function getFilteredGodownMovementQuery($query, Request $request)
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

        // Godown only except
        $godownOnlyId = $request->get('godownOnlyId');
        if (!is_null($godownOnlyId)) $query->where(function ($query) use ($godownOnlyId) {
            $query->whereIn('st.from_godown_id', explode(',', $godownOnlyId))
                ->orWhereIn('st.to_godown_id', explode(',', $godownOnlyId));
        });
        $godownExceptId = $request->get('godownExceptId');
        if (!is_null($godownExceptId)) $query->where(function ($query) use ($godownExceptId) {
            $query->whereNotIn('st.from_godown_id', explode(',', $godownExceptId))
                ->whereNotIn('st.to_godown_id', explode(',', $godownExceptId));
        });

        // Lot no filters with / without
        $lotNumber = $request->get('lotNumber');
        if ($lotNumber == 'with') $query->whereNotNull('pr.lot_number');
        if ($lotNumber == 'without') $query->where(function ($query) {
            $query->whereNull('pr.lot_number')->orWhere('pr.lot_number', '');
        });

        // Product only / except
        $productOnlyId = $request->get('filterProductOnlyId');
        if (!is_null($productOnlyId)) $query->whereIn('stp.product_id', explode(',', $productOnlyId));
        $productExceptId = $request->get('filterProductExceptId');
        if (!is_null($productExceptId)) $query->whereNotIn('stp.product_id', explode(',', $productExceptId));

        return $query;
    }
}
