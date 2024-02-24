<?php

namespace Database\Seeders;

use App\Models\MasterUnit;
use Illuminate\Database\Seeder;

class MasterUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for($x = 1; $x <= 7; $x++) {
            for($i = 1; $i <= 66; $i++) {
                MasterUnit::create([
                    'batch' => null,
                    'code' => $x * $i,
                    'container' => $x,
                    'name' => null,
                    'remarks' => null,
                ]);
            }
        }
    }
}
