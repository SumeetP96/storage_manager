<?php

namespace App\Services\Transfers;

use App\GodownProductsStock;
use Illuminate\Http\Request;

class InterGodownService
{
    public function checkExistingGPS(Request $request, $productId)
    {
        $existingGPS = GodownProductsStock::where('godown_id', $request->to_godown_id)
            ->where('product_id', $productId)
            ->first();

        if (is_null($existingGPS)) {
            return FALSE;
        } else {
            return $existingGPS;
        }
    }

    public function validateProducts(Request $request)
    {
        $errors = [];
        foreach($request->products as $key => $product) {
            $gps = GodownProductsStock::where('product_id', $product['id'])
                ->where('godown_id', $request->from_godown_id)
                ->first();

            if(empty($product['id'])) {
                $errors['product_' . $key . '_id'] = ['Product field is required.'];
            }

            if (empty($product['quantity'])) {
                $errors['product_' . $key . '_quantity'] = ['Quantity is required.'];
            } else if (!is_integer((int) $product['quantity'])) {
                $errors['product_' . $key . '_quantity'] = ['Invalid quantity.'];
            } else if ($product['quantity'] > $gps->current_stock) {
                $errors['product_' . $key . '_quantity'] = ['Quantity exceeds stock.'];
            }
        }

        return $errors;
    }
}
