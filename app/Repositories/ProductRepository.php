<?php

namespace App\Repositories;

use App\Product;
use App\Traits\ProductTrait;
use Illuminate\Http\Request;
use App\StockTransferProduct;
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
            ->selectRaw('*, ROUND(packing / 100, 0) as packingRaw')
            ->first();

        $record->recordTransactions = StockTransferProduct::where('product_id', $id)->count();

        return $record;
    }

    /**
     * Store record in database
     */
    public function create(Request $request)
    {
        return Product::create([
            'name'          => $request->name,
            'alias'         => ($request->alias != '') ? strtoupper($request->alias) : NULL,
            'unit'          => strtoupper($request->unit),
            'packing'       => $request->packing,
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
            'unit'          => strtoupper($request->unit),
            'packing'       => $request->packing,
            'remarks'       => $request->remarks
        ]);
    }

    /**
     * Delete record from database
     */
    public function destroy($id)
    {
        Product::find($id)->delete();
    }
}
