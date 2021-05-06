<?php

use App\StockTransfer;
use Illuminate\Database\Seeder;

class StockTransferSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StockTransfer::create([
            'date'      => '2021-04-21',
            'transfer_type_id'  => 2,
            'from_godown_id'=> 1,
            'to_godown_id'  => 2,
            'product_id'    => 2,
            'quantity'      => 1000 * 100,
            'agent_id'      => 3
        ]);

        StockTransfer::create([
            'date'      => '2021-04-15',
            'transfer_type_id'  => 2,
            'from_godown_id'=> 4,
            'to_godown_id'  => 1,
            'product_id'    => 1,
            'quantity'      => 200 * 100,
            'agent_id'      => 2
        ]);

        StockTransfer::create([
            'date'      => '2021-04-10',
            'transfer_type_id'  => 2,
            'from_godown_id'=> 3,
            'to_godown_id'  => 4,
            'product_id'    => 5,
            'quantity'      => 500 * 100,
            'agent_id'      => 4
        ]);
    }
}
