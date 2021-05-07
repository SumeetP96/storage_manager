<?php

namespace App\Services;

use App\Product;
use Illuminate\Http\Request;

class ProductService
{
    public function findDuplicateProductLot(Request $request, $id = NULL)
    {
        if (empty($request->lot_number)) {
            if (!is_null($id)) {
                $request->validate(['name' => 'required|max:255|unique:products,name,' . $id]);
            } else {
                $request->validate(['name' => 'required|max:255|unique:products,name']);
            }
        }

        $duplicate = FALSE;

        $product = Product::where('id', '!=', $id)
            ->where('name', $request->name)
            ->where('lot_number', $request->lot_number)
            ->first();

        if (!is_null($product)) $duplicate = TRUE;

        if ($duplicate) {
            return [
                'name'          => ['Product with lot number exists.'],
                'lot_number'    => ['Product with lot number exists.']
            ];
        } else {
            return $duplicate;
        }
    }

    public function validateRequest(Request $request)
    {
        $request->validate([
            'name'  => 'required|max:255',
            'alias' => 'nullable|max:10',
            'unit'  => 'required|size:3'
        ],[
            'alias.max' => 'Only 10 letters allowed.'
        ]);
    }
}
