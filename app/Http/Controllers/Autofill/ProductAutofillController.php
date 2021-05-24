<?php

namespace App\Http\Controllers\Autofill;

use App\Product;
use App\Http\Controllers\Controller;

class ProductAutofillController extends Controller
{
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
}
