<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
       $this->call([
        // FirstUserSeeder::class,
        // CountrySeeder::class,
        // StateSeeder::class,
        // ProfileSeeder::class,
        // AddressSeeder::class,
        // VmmfgScopeSeeder::class,
        // VmmfgJobSeeder::class,
        // RolePermissionSeeder::class,
        // StaffPermission::class,
        // VmmfgTitleCategorySeeder::class,
            ProfileSettingSeeder::class,
       ]);
    //    \App\Models\User::factory(150)->create();
    }
}
