<?php

namespace Database\Seeders;

use App\Models\VmmfgTitleCategory;
use Illuminate\Database\Seeder;

class VmmfgTitleCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        VmmfgTitleCategory::create([
            'name' => 'Assembly',
        ]);

        VmmfgTitleCategory::create([
            'name' => 'Testing',
        ]);

        VmmfgTitleCategory::create([
            'name' => 'Others',
        ]);
    }
}
