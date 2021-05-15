<?php

use App\Godown;
use Illuminate\Database\Seeder;

class GodownSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $godowns = [
            [
                'is_account'    => FALSE,
                'name'      => 'Main Shop',
                'alias'     => NULL,
                'address'   => '3, Mansi Residency, Daxini Society, Maninagar',
                'contact_1' => '9876543210',
                'contact_2' => '21304567',
                'email'     => 'mainshop@gmail.com'
            ],
            [
                'is_account'    => FALSE,
                'name'      => 'Hira Cold Storage Pvt. Ltd.',
                'alias'     => 'HRC',
                'address'   => 'Phase-2, Naroda',
                'contact_1' => '9876543210',
                'contact_2' => '21304567',
                'email'     => 'hiracoldstorage@gmail.com'
            ],
            [
                'is_account'    => FALSE,
                'name'      => 'Mansi Godown',
                'alias'     => 'MSG',
                'address'   => '1, Mansi Residency, Daxini Society, Maninagar',
                'contact_1' => '9876543210',
                'contact_2' => '21304567',
                'email'     => 'mansigodown@gmail.com'
            ],
            [
                'is_account'    => TRUE,
                'name'      => 'MS Corporation',
                'alias'     => 'MS',
                'address'   => 'Kalupur',
                'contact_1' => '9662865252',
                'contact_2' => '',
                'email'     => ''
            ],
        ];

        foreach ($godowns as $godown) {
            Godown::create([
                'is_account'    => $godown['is_account'],
                'name'      => $godown['name'],
                'alias'     => $godown['alias'],
                'address'   => $godown['address'],
                'contact_1' => $godown['contact_1'],
                'contact_2' => $godown['contact_2'],
                'email'     => $godown['email'],
                'remarks'   => 'This is ' . $godown['name']
            ]);
        }

        // for ($i=1; $i < 100; $i++) {
        //     Godown::create([
        //         'is_account'    => false,
        //         'name'      => 'Godown name ' . $i,
        //         'alias'     => 'GN' . $i,
        //         'address'   => 'Some godown location ' . $i,
        //         'contact_1' => '9898989898',
        //         'contact_2' => '6565656565',
        //         'email'     => 'godownname' . $i . '@gmail.com',
        //         'remarks'   => 'This is Godown ' . $i
        //     ]);
        // }
    }
}
