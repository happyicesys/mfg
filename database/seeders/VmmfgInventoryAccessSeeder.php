<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class VmmfgInventoryAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionArr = [
            'vmmfg-inventory-access',
            'vmmfg-inventory-read',
            'vmmfg-inventory-create',
            'vmmfg-inventory-edit',
            'vmmfg-inventory-delete'
        ];

        foreach($permissionArr as $permission) {
            Permission::create(['name' => $permission]);
        }

        $admin = Role::where('name', 'admin')->first();

        if($admin) {
            $admin->givePermissionTo($permissionArr);
        }
    }
}
