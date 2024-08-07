<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            CompanySeeder::class,
            SettingSeeder::class,
            UserSeeder::class,
            TransferTypeSeeder::class,
            // GodownSeeder::class,
            // ProductSeeder::class,
        ]);
    }
}
