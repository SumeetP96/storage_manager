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
                'name'          => 'Wheat',
                'alias'         => 'NW',
                'lot_number'    => NULL,
            ],
            [
                'name'          => 'Rice',
                'alias'         => 'NR',
                'lot_number'    => rand(11111, 99999),
            ],
            [
                'name'          => 'Toor Dal',
                'alias'         => 'TD',
                'lot_number'    => NULL,
            ],
            [
                'name'          => 'Makkai',
                'alias'         => 'MK',
                'lot_number'    => rand(11111, 99999),
            ],
            [
                'name'          => 'Edible Oil',
                'alias'         => 'EO',
                'lot_number'    => rand(11111, 99999),
            ],
        ];

        foreach ($products as $product) {
            Product::create([
                'name'          => $product['name'],
                'lot_number'    => $product['lot_number'],
                'alias'         => $product['alias'],
                'unit'          => 'KGS',
                'remarks'       => 'This is ' . $product['name'],
            ]);
        }
    }
}
