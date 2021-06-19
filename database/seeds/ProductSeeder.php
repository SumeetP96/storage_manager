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
                'unit'          => 'BAG',
                'packing'       => 25,
                'remarks'       => 'Dubar basmati rice'
            ],
            [
                'name'          => 'Kohinoor Tibar',
                'alias'         => 'KT',
                'unit'          => 'BAG',
                'packing'       => 25,
                'remarks'       => 'Tibar basmati rice'
            ],
            [
                'name'          => 'Kohinoor Red Diamond',
                'alias'         => 'KR',
                'unit'          => 'BAG',
                'packing'       => 25,
                'remarks'       => 'Wand basmati rice'
            ],
            [
                'name'          => 'Kohinoor Traditional',
                'alias'         => 'KR',
                'unit'          => 'BAG',
                'packing'       => 25,
                'remarks'       => 'Traditional basmati rice'
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name'          => $product['name'],
                'alias'         => $product['alias'],
                'unit'          => $product['unit'],
                'packing'       => $product['packing'] * 100,
                'remarks'       => $product['remarks']
            ]);
        }
    }
}
