<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $states = [
            'JHR' => 'Johor',
            'KDH' => 'Kedah',
            'KTN' => 'Kelantan',
            'MLK' => 'Melaka',
            'NSN' => 'Negeri Sembilan',
            'PHG' => 'Pahang',
            'PRK' => 'Perak',
            'PLS' => 'Perlis',
            'PNG' => 'Pulau Pinang',
            'SBH' => 'Sabah',
            'SWK' => 'Sarawak',
            'SGR' => 'Selangor',
            'TRG' => 'Terengganu',
            'KUL' => 'W.P. Kuala Lumpur',
            'LBN' => 'W.P. Labuan',
            'PJY' => 'W.P. Putrajaya',
        ];

        $country = Country::whereName('Malaysia')->first();

        if($country) {
            foreach($states as $symbol => $state) {
                State::create([
                    'name' => $state,
                    'symbol' => $symbol,
                    'country_id' => $country->id
                ]);
            }
        }
    }
}
