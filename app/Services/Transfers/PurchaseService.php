<?php

namespace App\Services\Transfers;

use App\GodownProductsStock;
use Illuminate\Http\Request;

class PurchaseService
{
    public function checkExistingGPS(Request $request)
    {
        $existingGPS = GodownProductsStock::where('godown_id', $request->to_godown_id)
            ->where('product_id', $request->product_id)
            ->first();

        if (is_null($existingGPS)) {
            return FALSE;
        } else {
            return $existingGPS;
        }
    }
}
