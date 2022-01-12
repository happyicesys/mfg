<?php

namespace Database\Seeders;

use App\Models\Bom;
use Illuminate\Database\Seeder;

class BomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Bom::create([
            'name' => 'BOM Model-E',
        ]);
    }
}
