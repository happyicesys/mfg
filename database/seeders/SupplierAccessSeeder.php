<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SupplierAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissionArr = [
            'bom-supplier-access',
            'bom-supplier-read',
            'bom-supplier-create',
            'bom-supplier-edit',
            'bom-supplier-delete'
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
