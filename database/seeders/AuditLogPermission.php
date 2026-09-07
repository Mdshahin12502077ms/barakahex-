<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogPermission extends Seeder
{
    public function run(): void
    {
        $newKeywords = [
            'audit_log_read',
        ];

        
        Permission::updateOrCreate(
            ['attribute' => 'audit_log'],
            [
                'keywords' => [
                    'read' => 'audit_log_read',
                ]
            ]
        );

        
        $targetRoleSlugs = ['superadmin', 'admin', 'administrator', 'auditor'];
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
