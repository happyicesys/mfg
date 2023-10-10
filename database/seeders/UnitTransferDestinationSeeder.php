<?php

namespace Database\Seeders;

use App\Models\UnitTransferDestination;
use Illuminate\Database\Seeder;

class UnitTransferDestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UnitTransferDestination::create([
            'name' => 'JB',
            'base_url' => 'https://jbmfg.happyice.com.sg',
            'directory' => '/unit-transfer',
        ]);

        UnitTransferDestination::create([
            'name' => 'SG',
            'base_url' => 'https://mfg.happyice.com.sg',
            'directory' => '/unit-transfer',
        ]);

        UnitTransferDestination::create([
            'name' => 'IDN',
            'base_url' => 'https://idn-mfg.happyice.net',
            'directory' => '/unit-transfer',
        ]);
    }
}
