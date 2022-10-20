<?php

namespace Database\Seeders;

use App\Models\VmmfgUnit;
use Illuminate\Database\Seeder;

class VmmfgUnitCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vmmfgUnits = VmmfgUnit::all();

        if($vmmfgUnits) {
            foreach($vmmfgUnits as $vmmfgUnit) {
                $vmmfgUnit->update(['code' => $vmmfgUnit->vmmfgJob->batch_no.'-'.$vmmfgUnit->unit_no]);
            }
        }

    }
}
