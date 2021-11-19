<?php

namespace Database\Seeders;

use App\Models\VmmfgScope;
use App\Models\VmmfgTitle;
use App\Models\VmmfgItem;
use Illuminate\Database\Seeder;

class VmmfgScopeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $scope = VmmfgScope::create([
            'name' => 'JB QAQC',
            'remarks' => 'For JB QA QC use'
        ]);

        $title = VmmfgTitle::create([
            'sequence' => 1,
            'name' => 'Assembly Category 1',
            'vmmfg_scope_id' => $scope->id,
        ]);

        VmmfgTitle::create([
            'sequence' => 2,
            'name' => 'Assembly Category 2',
            'vmmfg_scope_id' => $scope->id,
        ]);

        VmmfgItem::create([
            'sequence' => 1.1,
            'name' => 'Assembly Task 1',
            'vmmfg_title_id' => $title->id,
        ]);

        VmmfgItem::create([
            'sequence' => 1.2,
            'name' => 'Assembly Task 2',
            'vmmfg_title_id' => $title->id,
        ]);

        VmmfgItem::create([
            'sequence' => 1.3,
            'name' => 'Assembly Task 3',
            'vmmfg_title_id' => $title->id,
        ]);


    }
}
