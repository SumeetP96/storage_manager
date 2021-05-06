<?php

use App\TransferType;
use Illuminate\Database\Seeder;

class TransferTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TransferType::create(['name' => 'Inter Godown']);
        TransferType::create(['name' => 'Purchase']);
        TransferType::create(['name' => 'Sales']);
    }
}
