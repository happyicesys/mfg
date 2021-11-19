<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Profile;
use Illuminate\Database\Seeder;

class ProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Profile::create([
            'name' => 'HAPPY ICE PTE LTD',
            'symbol' => 'HIPL',
            'reg_no' => '201302530W',
            'country_id' => Country::whereName('Singapore')->first()->id,
        ]);

        Profile::create([
            'name' => 'HAPPY ICE SDN BHD',
            'symbol' => 'HISB',
            'reg_no' => '1207008-D',
            'country_id' => Country::whereName('Malaysia')->first()->id,
        ]);
    }
}
