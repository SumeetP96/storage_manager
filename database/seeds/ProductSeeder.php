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
                'lot_number'    => rand(11111, 99999),
                'unit'          => 'KGS',
                'compound_unit' => 'BAG',
                'packing'       => 25
            ],
            [
                'name'          => 'Kohinoor Tibar',
                'alias'         => 'KT',
                'lot_number'    => rand(11111, 99999),
                'unit'          => 'KGS',
                'compound_unit' => 'BAG',
                'packing'       => 25
            ],
            [
                'name'          => 'Kohinoor Red Diamond',
                'alias'         => 'KR',
                'lot_number'    => NULL,
                'unit'          => 'KGS',
                'compound_unit' => 'BAG',
                'packing'       => 25
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name'          => $product['name'],
                'lot_number'    => $product['lot_number'],
                'alias'         => $product['alias'],
                'unit'          => $product['unit'],
                'compound_unit' => $product['compound_unit'],
                'packing'       => $product['packing'] * 100,
            ]);
        }
    }
}
