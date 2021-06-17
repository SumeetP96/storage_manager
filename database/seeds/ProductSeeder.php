<?php

use App\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                'name'          => 'Kohinoor Dubar',
                'alias'         => 'KD',
                'unit'          => 'KGS',
                'compound_unit' => 'BAG',
                'packing'       => 25
            ],
            [
                'name'          => 'Kohinoor Tibar',
                'alias'         => 'KT',
                'unit'          => 'KGS',
                'compound_unit' => 'BAG',
                'packing'       => 25
            ],
            [
                'name'          => 'Kohinoor Red Diamond',
                'alias'         => 'KR',
                'unit'          => 'KGS',
                'compound_unit' => 'BAG',
                'packing'       => 25
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name'          => $product['name'],
                'alias'         => $product['alias'],
                'unit'          => $product['unit'],
                'compound_unit' => $product['compound_unit'],
                'packing'       => $product['packing'] * 100,
            ]);
        }
    }
}
