<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Country;
use App\Models\Profile;
use App\Models\State;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $profile = Profile::where('symbol', 'HIPL')->first();
        $profile->addresses()->create([
            'name' => 'HIPL',
            'address' => '#01-198, Block 2021, Bukit Batok St 23,',
            'postcode' => '659526',
            'is_city' => false,
            'is_state' => false,
            'is_primary' => true,
            'country_id' => Country::whereName('Singapore')->first()->id,
        ]);

        $profile = Profile::where('symbol','HISB')->first();
        $profile->addresses()->create([
            'name' => 'HISB',
            'address' => '8, Jalan Perdagangan 6, Taman Universiti',
            'postcode' => '81300',
            'city' => 'Skudai',
            'is_city' => true,
            'is_state' => true,
            'is_primary' => true,
            'country_id' => Country::whereName('Singapore')->first()->id,
            'state_id' => State::whereName('Johor')->first()->id,
        ]);
    }
}
