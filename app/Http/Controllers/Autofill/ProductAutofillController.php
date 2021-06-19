<?php

namespace App\Http\Controllers\Autofill;

use App\Product;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ProductAutofillController extends Controller
{
    /**
     * Fetch all records
     */
    public function all()
    {
        return Product::selectRaw('id, CONCAT_WS(" - ", name, CONCAT("(", alias, ")")) as name')->get();
    }

    /**
     * Product details
     */
    public function details($productId)
    {
        $product = Product::where('id', $productId)->select('remarks', 'unit', 'packing')->first();

        return $product;
    }

    /**
     * Fetch distinct units
     */
    public function distinctUnits()
    {
        return Product::distinct()->get(['unit']);
    }

    /**
     * Fetch distinct compound units
     */
    public function distinctCompoundUnits()
    {
        return Product::distinct()->get(['compound_unit']);
    }

    public function usedByGodown($godownId)
    {
        return DB::table('stock_transfers as st')
        ->leftJoin('stock_transfer_products as stp', 'stp.stock_transfer_id', '=', 'st.id')
        ->leftJoin('products as pr', 'pr.id', '=', 'stp.product_id')
        ->where(function ($query) use ($godownId) {
            $query->where('st.to_godown_id', $godownId)
                ->orWhere('st.from_godown_id', $godownId);
        })
        ->distinct()
        ->select('pr.id', 'pr.name')
        ->get(['pr.id']);
    }
}
