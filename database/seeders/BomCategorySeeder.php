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
            'name' => 'DVM Unit'
        ]);
        BomSubCategory::create([
            'name' => 'Freezer',
            'bom_category_id' => $bomCategory1->id,
        ]);
        BomSubCategory::create([
            'name' => 'Exit Hole',
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
        BomSubCategory::create([
            'name' => 'Sticker',
            'bom_category_id' => $bomCategory1->id,
        ]);


        $bomCategory2 = BomCategory::create([
            'name' => 'Exit Door & Foam Door Asm'
        ]);
        BomSubCategory::create([
            'name' => 'Exit Box',
            'bom_category_id' => $bomCategory2->id,
        ]);
        BomSubCategory::create([
            'name' => 'Drop Sensor',
            'bom_category_id' => $bomCategory2->id,
        ]);
        BomSubCategory::create([
            'name' => 'Actuator Door',
            'bom_category_id' => $bomCategory2->id,
        ]);
        BomSubCategory::create([
            'name' => 'Exit Box',
            'bom_category_id' => $bomCategory2->id,
        ]);

        $bomCategory3 = BomCategory::create([
            'name' => 'Metal Door & Boards'
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
            'name' => 'S/S Rack Asm'
        ]);
        BomSubCategory::create([
            'name' => 'Top Racks & Side Plates',
            'bom_category_id' => $bomCategory4->id,
        ]);
        BomSubCategory::create([
            'name' => 'Ice Gel',
            'bom_category_id' => $bomCategory4->id,
        ]);
        BomSubCategory::create([
            'name' => 'Ice Gel Rack',
            'bom_category_id' => $bomCategory4->id,
        ]);
        BomSubCategory::create([
            'name' => 'S/S Frame',
            'bom_category_id' => $bomCategory4->id,
        ]);

        $bomCategory5 = BomCategory::create([
            'name' => 'Drive Motor Asm'
        ]);
        BomSubCategory::create([
            'name' => 'Assembly',
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
            'name' => 'Spring'
        ]);
        BomSubCategory::create([
            'name' => 'Spring',
            'bom_category_id' => $bomCategory6->id,
        ]);
    }
}
