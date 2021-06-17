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
            'compound_unit' => 'nullable|size:3|required_unless:packing,""',
            'packing'       => 'nullable|required_unless:compound_unit,""'
        ],[
            'alias.max'         => 'Only 10 letters allowed.',
            'packing.integer'   => 'Invalid packing.',
            'compound_unit.required_unless'   => 'Field is required',
            'packing.required_unless'   => 'Packing is required',
        ]);
    }
}
