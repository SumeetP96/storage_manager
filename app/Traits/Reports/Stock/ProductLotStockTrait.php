<?php

namespace App\Traits\Reports\Stock;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait ProductLotStockTrait
{
    /**
     * Return all records as collection
     *
     * @param  \Illuminate\Http\Request $request
     * @return Object
     */
    public function allProductLotStock(Request $request)
    {
        $flow = $request->get('flow');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $query = DB::table('godown_products_stocks as gps')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where('gps.current_stock', '>', 0);

        return $this->getFilteredProductLotQuery($query, $request)
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('lot_number', 'like', '%' . $search . '%')
                    ->orWhere('unit', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                pr.unit as productUnit,
                pr.lot_number as lotNumber,
                pr.name as name,
                gps.current_stock div pr.packing as compoundStock,
                pr.packing,
                pr.compound_unit as compoundUnit,
                gps.current_stock as currentStock
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
    public function getFilteredProductLotQuery($query, Request $request)
    {
        // From product only except
        $productOnlyId = $request->get('productOnlyId');
        if (!is_null($productOnlyId)) $query->whereIn('pr.id', explode(',', $productOnlyId));
        $productExceptId = $request->get('productExceptId');
        if (!is_null($productExceptId)) $query->whereNotIn('pr.id', explode(',', $productExceptId));

        return $query;
    }
}
