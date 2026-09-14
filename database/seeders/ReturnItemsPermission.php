<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReturnItemsPermission extends Seeder
{
   public function run(): void
    {
        $newKeywords = [
            'return_item_status_update',
        ];

        
        Permission::updateOrCreate(
            ['attribute' => 'return_item_status'],
            [
                'keywords' => [
                    'update' => 'return_item_status_update',
                ]
            ]
        );

        
        $targetRoleSlugs = ['superadmin', 'admin', 'administrator','branch-manager'];
        $roles = Role::whereIn('slug', $targetRoleSlugs)->get();

        foreach ($roles as $role) {
            $role->permissions = array_values(array_unique(array_merge($role->permissions ?? [], $newKeywords)));
            $role->save();
        }

       
        $roleIds = $roles->pluck('id')->toArray();
        $users = User::whereHas('roles', function ($query) use ($roleIds) {
            $query->whereIn('roles.id', $roleIds);
        })->get();

        foreach ($users as $user) {
            $user->permissions = array_values(array_unique(array_merge($user->permissions ?? [], $newKeywords)));
            $user->save();
        }

        echo "Audit Log permissions seeded successfully for Superadmin, Admin, and Auditor!\n";
    }
}
