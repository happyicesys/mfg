<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class StaffPermission extends Seeder
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
        ]);
    }
}
