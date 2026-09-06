<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThanaUpazila extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = Permission::updateOrCreate(
            ['attribute' => 'upazila'],
            [
                'keywords' => [
                    'read'   => 'upazila_read',
                    'create' => 'upazila_create',
                    'update' => 'upazila_update',
                    'delete' => 'upazila_delete',
                ]
            ]
        );

       
        $role = Role::find(1);
        if ($role) {
            $existingPermissions = $role->permissions ?? [];
            $newKeywords = ['upazila_read', 'upazila_create', 'upazila_update', 'upazila_delete'];
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
