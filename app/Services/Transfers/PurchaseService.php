<?php

namespace App\Services\Transfers;

use App\GodownProductsStock;
use Illuminate\Http\Request;

class PurchaseService
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
            if(empty($product['id'])) {
                $errors['product_' . $key . '_id'] = ['Product field is required.'];
            }

            if (empty($product['quantity'])) {
                $errors['product_' . $key . '_quantity'] = ['Quantity is required.'];
            } else if (!is_integer((int) $product['quantity'])) {
                $errors['product_' . $key . '_quantity'] = ['Invalid quantity.'];
            }
        }

        return $errors;
    }
}
