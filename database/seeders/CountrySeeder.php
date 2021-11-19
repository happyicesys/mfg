<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Country::create([
            'name' => 'Malaysia',
            'currency_name' => 'MYR',
            'currency_symbol' => 'RM',
            'phone_code' => '60',
            'is_city' => true,
            'is_state' => true,
        ]);

        Country::create([
            'name' => 'Singapore',
            'currency_name' => 'SGD',
            'currency_symbol' => 'S$',
            'phone_code' => '65',
            'is_city' => false,
            'is_state' => false,
        ]);
    }
}
