<?php

namespace App\Repositories;

use App\Product;
use App\GodownProductsStock;
use App\StockTransferProduct;
use App\Traits\ProductTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    use ProductTrait;

    /**
     * Fetch all products
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function fetchAll(Request $request)
    {
        return [
            'total' => $this->allRecords($request)->count(),

            'records' => $this->allRecords($request)
                ->skip($request->skip)->limit($request->limit)->get(),
        ];
    }

    /**
     * Fetch a single record by id
     */
    public function fetchOne($id)
    {
        $record = DB::table('products')->where('id', $id)
            ->selectRaw('
                id,
                name,
                alias,
                lot_number,
                unit,
                compound_unit,
                packing,
                packing div 100 as packingRaw,
                remarks,
                created_at,
                updated_at
            ')
            ->first();

        $record->recordTransactions = StockTransferProduct::where('product_id', $id)->count();

        return $record;
    }

    /**
     * Fetch records for autocomplete
     */
    public function fetchAutocompletes()
    {
        return Product::selectRaw("
            id, CONCAT_WS(' - ', name, CONCAT('( ', lot_number, ' )'), alias) AS name
        ")->orderBy('name')
        ->get();
    }

    /**
     * Fetch records for autocomplete
     */
    public function fetchAutocompletesLot()
    {
        return Product::where('lot_number', '!=', NULL)
            ->select('lot_number')
            ->distinct()
            ->orderBy('lot_number')
            ->get();
    }

    /**
     * Fetch records for autocomplete
     */
    public function fetchAutocompletesUnit()
    {
        return Product::select('unit')->distinct()->orderBy('unit')->get();
    }

    /**
     * Fetch records for autocomplete
     */
    public function fetchAutocompletesWithStock($id = null)
    {
        $query = DB::table('products as pr')
            ->leftJoin('godown_products_stocks as gps', 'gps.product_id', '=', 'pr.id');

        if (!is_null($id)) $query->where('gps.godown_id', $id);

        return $query->selectRaw("
                pr.id, CONCAT_WS(' - ', pr.name, CONCAT('( ', pr.lot_number, ' )'), alias) AS name
            ")->get();
    }

    /**
     * Fetch selected record details
     */
    public function details($id, $godownId = null)
    {
        $product = Product::where('id', $id)
            ->select('unit', 'remarks', 'compound_unit as compoundUnit', 'packing')
            ->first();

        if (!is_null($godownId)) {
            $product->stock = GodownProductsStock::where('product_id', $id)
                ->where('godown_id', $godownId)->first()
                ->current_stock;
        }

        $product->recordTransactions = StockTransferProduct::where('product_id', $id)->count();

        return $product;
    }

    /**
     * Store record in database
     */
    public function create(Request $request)
    {
        return Product::create([
            'name'          => $request->name,
            'alias'         => ($request->alias != '') ? strtoupper($request->alias) : NULL,
            'lot_number'    => ($request->lot_number != '') ? $request->lot_number : NULL,
            'unit'          => strtoupper($request->unit),
            'packing'       => $request->packing,
            'compound_unit' => strtoupper($request->compound_unit),
            'remarks'       => $request->remarks
        ])->id;
    }

    /**
     * Update record in database
     */
    public function update(Request $request, $id)
    {
        Product::find($id)->update([
            'name'          => $request->name,
            'alias'         => ($request->alias != '') ? strtoupper($request->alias) : NULL,
            'lot_number'    => ($request->lot_number != '') ? $request->lot_number : NULL,
            'unit'          => strtoupper($request->unit),
            'packing'       => $request->packing,
            'compound_unit' => strtoupper($request->compound_unit),
            'remarks'       => $request->remarks
        ]);

        $stps = StockTransferProduct::where('product_id', $id)
            ->update(['compound_quantity' => NULL]);
    }

    /**
     * Delete record from database
     */
    public function destroy($id)
    {
        $product = Product::find($id)->delete();
    }
}
