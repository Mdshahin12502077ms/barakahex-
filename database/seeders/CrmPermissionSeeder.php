<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CrmPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $newKeywords = [
            'crm_history_read',
        ];

        Permission::updateOrCreate(
            ['attribute' => 'crm_history'],
            [
                'keywords' => [
                    'read' => 'crm_history_read',
                ]
            ]
        );

        // Assign permission to Superadmin, Admin, Branch Manager and Account Manager
        $targetRoleSlugs = ['superadmin', 'admin', 'administrator', 'branch-manager', 'account-manager'];
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

        echo "CRM History permissions seeded successfully for Superadmin, Admin, Branch Manager, and Account Manager!\n";
    }
}
