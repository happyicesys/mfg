<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionVmmfgInventoryAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $staff = Role::where('name', '=', 'staff')->first();

        $staff->givePermissionTo([
            'vmmfg-inventory-access',
        ]);
    }
}
