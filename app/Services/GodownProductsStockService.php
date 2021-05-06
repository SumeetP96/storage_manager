<?php

namespace App\Services;

use App\GodownProductsStock;

class GodownProductsStockService
{
    /**
     * Find existing record
     */
    public function findExistingGps($godownId, $productId)
    {
        $gps = GodownProductsStock::where('godown_id', $godownId)
            ->where('product_id', $productId)
            ->first();

        if (is_null($gps)) return false;
        else return $gps;
    }
}
