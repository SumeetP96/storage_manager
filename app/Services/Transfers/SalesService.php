<?php

namespace App\Services\Transfers;

use App\GodownProductsStock;
use Illuminate\Http\Request;

class SalesService
{
    public function checkExistingGPS(Request $request)
    {
        $existingGPS = GodownProductsStock::where('godown_id', $request->from_godown_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (is_null($existingGPS)) {
            return FALSE;
        } else {
            return $existingGPS;
        }
    }

    public function validateQuantity(Request $request)
    {
        $gps = GodownProductsStock::where('product_id', $request->product_id)
            ->where('godown_id', $request->from_godown_id)
            ->first();

        if ($request->quantity > $gps->current_stock) return false;
        else return true;
    }
}
