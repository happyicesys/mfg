<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = Role::create([
            'name' => 'superadmin',
        ]);

        $admin = Role::create([
            'name' => 'admin',
        ]);

        $staff = Role::create([
            'name' => 'staff',
        ]);

        $superadminUser = User::findOrFail(1);
        $superadminUser->assignRole('superadmin');

        $daniel = User::whereName('daniel')->first();
        if($daniel) {
            $daniel->assignRole('admin');
        }

        $staff = User::whereName('staff')->first();
        if($staff) {
            $staff->assignRole('staff');
        }

        $permissionArr = [
            'profile-access',
            'profile-read',
            'profile-create',
            'profile-edit',
            'profile-delete',
            'admin-access',
            'admin-read',
            'admin-create',
            'admin-edit',
            'admin-delete',
            'vmmfg-ops-access',
            'vmmfg-ops-read',
            'vmmfg-ops-create',
            'vmmfg-ops-edit',
            'vmmfg-ops-delete',
            'vmmfg-ops-checker',
            'vmmfg-setting-access',
            'vmmfg-setting-read',
            'vmmfg-setting-create',
            'vmmfg-setting-edit',
            'vmmfg-setting-delete',
            'self-access',
            'self-read',
            'self-create',
            'self-edit',
            'self-delete',
        ];

        foreach($permissionArr as $permission) {
            Permission::create(['name' => $permission]);
        }

        $admin->givePermissionTo(Permission::all());
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
