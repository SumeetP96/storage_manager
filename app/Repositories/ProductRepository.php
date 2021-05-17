<?php

namespace App\Repositories;

use App\GodownProductsStock;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRepository
{
    /**
     * Fetch all records
     */
    public function fetchAll(Request $request)
    {
        $search = $request->get('query');
        $limit = $request->get('limit');
        $skip = $request->get('skip');
        $sortBy = $request->get('sortBy');
        $flow = $request->get('flow');

        $results = DB::table('products')
            ->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('alias', 'like', '%' . $search . '%')
                    ->orWhere('remarks', 'like', '%' . $search . '%')
                    ->orWhere('lot_number', 'like', '%' . $search . '%');
            });

        $total = $results->count();
        $records = $results->skip($skip)->limit($limit)->orderBy($sortBy, $flow)->get();

        return ['records' => $records, 'total' => $total];
    }

    /**
     * Fetch a single record by id
     */
    public function fetchOne($id)
    {
        return Product::find($id);
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

        return $query->where('gps.current_stock', '>', 0)
            ->selectRaw("
                pr.id, CONCAT_WS(' - ', pr.name, CONCAT('( ', pr.lot_number, ' )'), alias) AS name
            ")->get();
    }

    /**
     * Fetch selected record details
     */
    public function details($id, $godownId = null)
    {
        $product = Product::where('id', $id)
            ->select('unit', 'remarks')
            ->first();

        if (!is_null($godownId)) {
            $product->stock = GodownProductsStock::where('product_id', $id)
                ->where('godown_id', $godownId)->first()
                ->current_stock;
        }

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
            'remarks'       => $request->remarks
        ]);
    }

    /**
     * Delete record from database
     */
    public function destroy($id)
    {
        $product = Product::find($id)->delete();
    }
}
