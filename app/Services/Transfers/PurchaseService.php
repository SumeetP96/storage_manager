<?php

namespace App\Services\Transfers;

use App\GodownProductsStock;
use Illuminate\Http\Request;

class PurchaseService
{
    public function checkExistingGPS(Request $request, $product)
    {
        $existingGPS = GodownProductsStock::where('godown_id', $request->to_godown_id)
            ->where('product_id', $product['id'])
            ->where('lot_number', $product['lot_number'])
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

            if (!empty($product['rent']) && !is_numeric($product['rent'])) {
                $errors['product_' . $key . '_rent'] = ['Invalid.'];
            }

            if (!empty($product['labour']) && !is_numeric($product['labour'])) {
                $errors['product_' . $key . '_labour'] = ['Invalid.'];
            }

            if (!empty($product['compound_quantity']) && !is_numeric($product['compound_quantity'])) {
                $errors['product_' . $key . '_compound_quantity'] = ['Invalid.'];
            }

            if (empty($product['quantity']) || is_null($product['quantity'])) {
                $errors['product_' . $key . '_quantity'] = ['Quantity is required.'];
            } else {
                if (!is_numeric($product['quantity'])) {
                    $errors['product_' . $key . '_quantity'] = ['Invalid quantity.'];
                }
            }
        }

        return $errors;
    }
}
