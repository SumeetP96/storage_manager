<?php

namespace App\Services\Transfers;

use App\GodownProductsStock;
use Illuminate\Http\Request;

class SalesService
{
    public function checkExistingGPS(Request $request, $product)
    {
        $existingGPS = GodownProductsStock::where('godown_id', $request->from_godown_id)
            ->where('product_id', $product)
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
        $runningStock = [];

        foreach($request->products as $key => $product) {
            $gps = GodownProductsStock::where('product_id', $product['id'])
                ->where('godown_id', $request->from_godown_id)
                ->first();

            if(empty($product['id'])) {
                $errors['product_' . $key . '_id'] = ['Product field is required.'];
            }

            if (!empty($product['compound_quantity']) && !is_integer((int) $product['compound_quantity'])) {
                $errors['product_' . $key . '_compound_quantity'] = ['Invalid input.'];
            }

            $productId = $product['id'];
            $productQuantity = (int) $product['quantity'];

            if (empty($product['quantity'])) {
                $errors['product_' . $key . '_quantity'] = ['Quantity is required.'];
            } else if (!is_integer($productQuantity)) {
                $errors['product_' . $key . '_quantity'] = ['Invalid quantity.'];
            }

            if (array_key_exists($productId, $runningStock)) $runningStock[$productId] += $productQuantity;
            else $runningStock[$productId] = $productQuantity;

            if ($runningStock[$productId] > $gps->current_stock + $runningStock[$productId]) {
                $errors['product_' . $key . '_quantity'] = ['Quantity exceeds stock.'];
            }
        }

        return $errors;
    }
}
