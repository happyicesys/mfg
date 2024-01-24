<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VmmfgUnit;
use App\Traits\HasProgress;

class SyncProgressSeeder extends Seeder
{
    use HasProgress;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vmmfgUnits = VmmfgUnit::all();

        foreach($vmmfgUnits as $vmmfgUnit) {
            $this->syncProgress($vmmfgUnit);
        }
    }
}
