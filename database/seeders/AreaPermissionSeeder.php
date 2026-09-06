<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AreaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $newKeywords = [
            'area_read',
            'area_create',
            'area_update',
            'area_delete',
        ];

       
        Permission::updateOrCreate(
            ['attribute' => 'area'],
            [
                'keywords' => [
                    'read'   => 'area_read',
                    'create' => 'area_create',
                    'update' => 'area_update',
                    'delete' => 'area_delete',
                ]
            ]
        );

       
        $role = Role::find(1);
        if ($role) {
            $existingPermissions = $role->permissions ?? [];
            $role->permissions = array_unique(array_merge($existingPermissions, $newKeywords));
            $role->save();
        }

       
        $users = User::where('user_type', 'staff')->get();
        foreach ($users as $user) {
            $existingUserPermissions = $user->permissions ?? [];
            $user->permissions = array_unique(array_merge($existingUserPermissions, $newKeywords));
            $user->save();
        }

        echo "Area permission seeded and assigned to Superadmin & Staff users successfully!\n";
    }
}
