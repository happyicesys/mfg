<?php

namespace Database\Seeders;

use App\Models\BomCategory;
use App\Models\BomSubCategory;
use Illuminate\Database\Seeder;

class BomCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $bomCategory1 = BomCategory::create([
            'name' => 'MACHINE UNIT'
        ]);
        BomSubCategory::create([
            'name' => 'Freezer',
            'bom_category_id' => $bomCategory1->id,
        ]);
        BomSubCategory::create([
            'name' => 'Door Lock',
            'bom_category_id' => $bomCategory1->id,
        ]);
        BomSubCategory::create([
            'name' => 'Product Exit Hole',
            'bom_category_id' => $bomCategory1->id,
        ]);
        BomSubCategory::create([
            'name' => 'Joining',
            'bom_category_id' => $bomCategory1->id,
        ]);
        BomSubCategory::create([
            'name' => 'Freezer Fan',
            'bom_category_id' => $bomCategory1->id,
        ]);


        $bomCategory2 = BomCategory::create([
            'name' => 'EXIT BOX & DISPENSING ASM'
        ]);
        BomSubCategory::create([
            'name' => 'Exit Box',
            'bom_category_id' => $bomCategory2->id,
        ]);
        BomSubCategory::create([
            'name' => 'Foam Door Syst',
            'bom_category_id' => $bomCategory2->id,
        ]);


        $bomCategory3 = BomCategory::create([
            'name' => 'METAL DOOR & BOARDS'
        ]);
        BomSubCategory::create([
            'name' => 'Metal Door & Screen Door',
            'bom_category_id' => $bomCategory3->id,
        ]);
        BomSubCategory::create([
            'name' => 'Metal Door',
            'bom_category_id' => $bomCategory3->id,
        ]);
        BomSubCategory::create([
            'name' => 'Screen Door',
            'bom_category_id' => $bomCategory3->id,
        ]);
        BomSubCategory::create([
            'name' => 'Electronic Boards',
            'bom_category_id' => $bomCategory3->id,
        ]);

        $bomCategory4 = BomCategory::create([
            'name' => 'RACKING SYSTEM'
        ]);
        BomSubCategory::create([
            'name' => 'Top Rack Syst',
            'bom_category_id' => $bomCategory4->id,
        ]);
        BomSubCategory::create([
            'name' => 'Racking',
            'bom_category_id' => $bomCategory4->id,
        ]);


        $bomCategory5 = BomCategory::create([
            'name' => 'DRIVE MOTOR ASM'
        ]);
        BomSubCategory::create([
            'name' => 'Batch Cable',
            'bom_category_id' => $bomCategory5->id,
        ]);
        BomSubCategory::create([
            'name' => 'Freezer Fan',
            'bom_category_id' => $bomCategory5->id,
        ]);
        BomSubCategory::create([
            'name' => 'DC Motor',
            'bom_category_id' => $bomCategory5->id,
        ]);
        BomSubCategory::create([
            'name' => 'Switch',
            'bom_category_id' => $bomCategory5->id,
        ]);

        $bomCategory6 = BomCategory::create([
            'name' => 'SPRING'
        ]);
        BomSubCategory::create([
            'name' => 'Spring',
            'bom_category_id' => $bomCategory6->id,
        ]);
    }
}
