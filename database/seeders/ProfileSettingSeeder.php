<?php

namespace Database\Seeders;

use App\Models\Profile;
use App\Models\ProfileSetting;
use Illuminate\Database\Seeder;

class ProfileSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        ProfileSetting::create([
            'profile_id' => 1,
            'theme_background_url' => '/img/background_sg.jpg',
            'theme_sidebar_background_color' => '#31353D',
            'theme_sidebar_font_color' => '#00A3E0',
            'vmmfg_job_batch_no_title' => 'Batch No',
            'vmmfg_unit_vend_id_title' => 'Vend ID',
        ]);

        ProfileSetting::create([
            'profile_id' => 2,
            'theme_background_url' => '/img/background_my.jpg',
            'theme_sidebar_background_color' => '#5a889d',
            'theme_sidebar_font_color' => 'black',
            'vmmfg_job_batch_no_title' => 'Job No',
            'vmmfg_unit_vend_id_title' => 'Batch & Model',
        ]);
    }
}
