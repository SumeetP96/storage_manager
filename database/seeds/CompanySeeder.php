<?php

use App\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Company::create([
            'name'      => 'Aadhya Distribution Co.',
            'address'   => 'Shop no. 1, Tirupati Complex, Chokha Bajar, Kalupur - 380002',
            'contact_1' => '9726555500'
        ]);
    }
}
