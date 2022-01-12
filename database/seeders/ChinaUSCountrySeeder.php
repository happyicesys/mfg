<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class ChinaUSCountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            'name' => 'United States of America',
            'currency_name' => 'USD',
            'currency_symbol' => '$',
            'phone_code' => '1',
            'is_city' => false,
            'is_state' => false,
        ]);

        Country::create([
            'name' => 'China',
            'currency_name' => 'RMB',
            'currency_symbol' => '¥',
            'phone_code' => '86',
            'is_city' => false,
            'is_state' => false,
        ]);
    }
}
