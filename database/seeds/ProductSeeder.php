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

        // $packings = [10, 25, 30, 50];
        // for($i = 1; $i <= 100; $i++) {
        //     Product::create([
        //         'name'          => 'Product Name ' . $i,
        //         'lot_number'    => rand(10000, 99999),
        //         'alias'         => 'PRD' . $i,
        //         'unit'          => 'KGS',
        //         'compound_unit' => 'BAG',
        //         'packing'       => $packings[rand(0, 3)] * 100,
        //         'remarks'       => 'Lorem ipsum dolor sit amet, consectetur adipisicing'
        //     ]);
        // }
    }
}
