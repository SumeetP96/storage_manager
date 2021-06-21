<?php

namespace App\Traits\Reports\Stock;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait GodownProductStockTrait
{
    /**
     * Return all records as collection
     *
     * @param  \Illuminate\Http\Request $request
     * @return Object
     */
    public function allGodownProductStock(Request $request)
    {
        $flow = $request->get('flow');
        $search = $request->get('query');
        $sortBy = $request->get('sortBy');

        $query = DB::table('godown_products_stocks as gps')
            ->leftJoin('godowns as gd', 'gps.godown_id', '=', 'gd.id')
            ->leftJoin('products as pr', 'gps.product_id', '=', 'pr.id')
            ->where('gps.current_stock', '>', 0);

        return $this->getFilteredProductStockQuery($query, $request)
            ->where(function ($query) use ($search) {
                $query->where('pr.name', 'like', '%' . $search . '%')
                    ->orWhere('gd.name', 'like', '%' . $search . '%')
                    ->orWhere('gps.lot_number', 'like', '%' . $search . '%');
            })
            ->selectRaw('
                gps.current_stock as currentStock,
                gps.lot_number as productLotNumber,
                gd.id,
                gd.name as godownName,
                pr.id,
                pr.name as productName,
                pr.unit as productUnit,
                ROUND(pr.packing / 100, 0) as packing
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
    public function getFilteredProductStockQuery($query, Request $request)
    {
        // Lot no filters with / without
        $lotNumber = $request->get('lotNumber');
        if ($lotNumber == 'with') $query->whereNotNull('pr.lot_number');
        if ($lotNumber == 'without') $query->where(function ($query) {
            $query->whereNull('pr.lot_number')->orWhere('pr.lot_number', '');
        });

        // From godown only except
        $godownOnlyId = $request->get('godownOnlyId');
        if (!is_null($godownOnlyId)) $query->whereIn('gd.id', explode(',', $godownOnlyId));
        $godownExceptId = $request->get('godownExceptId');
        if (!is_null($godownExceptId)) $query->whereNotIn('gd.id', explode(',', $godownExceptId));

        // From product only except
        $productOnlyId = $request->get('productOnlyId');
        if (!is_null($productOnlyId)) $query->whereIn('pr.id', explode(',', $productOnlyId));
        $productExceptId = $request->get('productExceptId');
        if (!is_null($productExceptId)) $query->whereNotIn('pr.id', explode(',', $productExceptId));

        return $query;
    }
}
