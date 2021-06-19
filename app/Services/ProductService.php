<?php

namespace App\Services;

use Illuminate\Http\Request;

class ProductService
{
    public function validateRequest(Request $request, $id = null)
    {
        $request->validate([
            'name'          => 'required|max:255|unique:products,name,' . $id,
            'alias'         => 'nullable|max:10',
            'unit'          => 'required|size:3',
            'packing'       => 'required|integer'
        ],[
            'alias.max'         => 'Only 10 letters allowed.',
            'packing.integer'   => 'Invalid packing.',
        ]);
    }
}
