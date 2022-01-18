<?php

namespace Database\Seeders;

use App\Models\CurrencyRate;
use Illuminate\Database\Seeder;

class CurrencyRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CurrencyRate::create([
            'country_id' => 1,
            'rate' => 3.1,
        ]);

        CurrencyRate::create([
            'country_id' => 2,
            'rate' => 1,
        ]);

        CurrencyRate::create([
            'country_id' => 3,
            'rate' => 0.7,
        ]);

        CurrencyRate::create([
            'country_id' => 4,
            'rate' => 4.6,
        ]);
    }
}
