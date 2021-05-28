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
                stp.compound_quantity as compoundQuantity,
                pr.compound_unit as compoundUnit,
                pr.packing,
                st.id as id,
                st.date as date,
                st.transfer_type_id as ttid,
                pr.unit as unit,
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
        return $query;
    }
}
