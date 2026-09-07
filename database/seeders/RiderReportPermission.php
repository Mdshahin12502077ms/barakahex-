<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RiderReportPermission extends Seeder
{
    public function run(): void
    {
        $newKeywords = [
            'rider_report_read',
        ];

        Permission::updateOrCreate(
            ['attribute' => 'rider_report'],
            [
                'keywords' => [
                    'read' => 'rider_report_read',
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

        echo "Rider Report permissions seeded successfully for Superadmin, Admin, Branch Manager, and Account Manager!\n";
    }
}
