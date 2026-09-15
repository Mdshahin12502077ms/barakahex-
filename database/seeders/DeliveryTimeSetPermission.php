<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DeliveryTimeSetPermission extends Seeder
{
      public function run(): void
    {
        $permission = Permission::updateOrCreate(
            ['attribute' => 'delivery_time_set'],
            [
                'keywords' => [
                    'read'   => 'delivery_time_set_read',
                    'create' => 'delivery_time_set_create',
                    'update' => 'delivery_time_set_update',
                    'delete' => 'delivery_timeset_delete',
                ]
            ]
        );

       
        $role = Role::find(1);
        if ($role) {
            $existingPermissions = $role->permissions ?? [];
            $newKeywords = ['delivery_time_set_read', 'delivery_time_set_create', 'delivery_time_set_update', 'delivery_time_set_delete'];
            $role->permissions = array_unique(array_merge($existingPermissions, $newKeywords));
            $role->save();
        }

        // Sync permissions to all staff/admin users
        $users = \App\Models\User::where('user_type', 'staff')->get();
        foreach ($users as $user) {
            $user->permissions = array_unique(array_merge($user->permissions ?? [], $newKeywords ?? ['upazila_read', 'upazila_create', 'upazila_update', 'upazila_delete']));
            $user->save();
        }

        echo "Upazila permission seeded and assigned to Superadmin successfully!\n";
    }
}
