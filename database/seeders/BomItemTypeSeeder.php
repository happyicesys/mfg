<?php

namespace Database\Seeders;

use App\Models\BomItemType;
use Illuminate\Database\Seeder;

class BomItemTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BomItemType::create([
            'name' => 'Part',
        ]);

        BomItemType::create([
            'name' => 'Part, Fab',
        ]);

        BomItemType::create([
            'name' => 'C',
        ]);

        BomItemType::create([
            'name' => 'CB',
        ]);

        BomItemType::create([
            'name' => 'EE',
        ]);

        BomItemType::create([
            'name' => 'Acrylic',
        ]);
    }
}
