<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DistrictPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::updateOrCreate(
        ['attribute' => 'district'],
        ['keywords' => [
            'read'   => 'district_read',
            'create' => 'district_create',
            'update' => 'district_update',
            'delete' => 'district_delete'
        ]]
    );

    $role = Role::find(1);
    if ($role) {
        $role->permissions = array_unique(array_merge($role->permissions ?? [], [
            'district_read', 'district_create', 'district_update', 'district_delete'
        ]));
        $role->save();
    }

    // Update staff/admin users permissions
    $users = \App\Models\User::where('user_type', 'staff')->get();
    foreach ($users as $user) {
        $user->permissions = array_unique(array_merge($user->permissions ?? [], [
            'district_read', 'district_create', 'district_update', 'district_delete'
        ]));
        $user->save();
    }

    }
}
