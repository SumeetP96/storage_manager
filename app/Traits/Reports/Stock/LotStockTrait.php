<?php

namespace App\Traits\Reports\Stock;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait LotStockTrait
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

        return DB::table('godown_products_stocks as gps')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where(function ($query) use ($search) {
                $query->where('pr.lot_number', 'like', '%' . $search . '%')
                    ->orWhere('pr.unit', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                pr.unit as unit,
                pr.lot_number as lotNumber,
                sum(gps.current_stock div pr.packing) as compoundStock,
                pr.compound_unit as compoundUnit,
                sum(gps.current_stock) as stock
            ')
            ->orderBy($sortBy, $flow)
            ->groupBy('lot_number', 'unit', 'compound_unit')
            ->get();
    }
}
