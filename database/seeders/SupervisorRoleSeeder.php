<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SupervisorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = Role::create([
            'name' => 'supervisor',
        ]);

        $superadmin->givePermissionTo([
            'vmmfg-ops-access',
            'vmmfg-ops-read',
            'vmmfg-ops-create',
            'vmmfg-ops-edit',
            'vmmfg-ops-delete',
            'self-access',
            'self-read',
            'self-create',
            'self-edit',
            'self-delete',
            'vmmfg-inventory-access',
            'vmmfg-inventory-read',
        ]);
    }
}
