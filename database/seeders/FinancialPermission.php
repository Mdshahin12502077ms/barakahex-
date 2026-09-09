<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FinancialPermission extends Seeder
{
    public function run(): void
    {
        $newKeywords = [
            'financial_report_read',
        ];

        // 1. Update report group permission so it appears under Report in Role/Staff UI
        $reportPermission = Permission::where('attribute', 'report')->first();
        if ($reportPermission) {
            $keywords = $reportPermission->keywords ?? [];
            $keywords['financial_report_read'] = 'financial_report_read';
            $reportPermission->keywords = $keywords;
            $reportPermission->save();
        }

        // 2. Standalone permission entry
        Permission::updateOrCreate(
            ['attribute' => 'financial_report'],
            [
                'keywords' => [
                    'read' => 'financial_report_read',
                ]
            ]
        );

        // 3. Assign to Target Roles (Superadmin, Admin, Administrator, Account Manager, Auditor)
        $targetRoleSlugs = ['superadmin', 'admin', 'administrator', 'account-manager', 'auditor'];
        $roles = Role::whereIn('slug', $targetRoleSlugs)->get();

        foreach ($roles as $role) {
            $existing = $role->permissions ?? [];
            $role->permissions = array_values(array_unique(array_merge($existing, $newKeywords)));
            $role->save();
        }

        // 4. Also assign directly to all active users belonging to these roles
        $roleIds = $roles->pluck('id')->toArray();
        $userIds = DB::table('role_users')->whereIn('role_id', $roleIds)->pluck('user_id')->toArray();
        $users = User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            $existing = $user->permissions ?? [];
            $user->permissions = array_values(array_unique(array_merge($existing, $newKeywords)));
            $user->save();
        }

        echo "Financial Report permissions seeded successfully for Superadmin, Admin, Account Manager, and Auditor!\n";
    }
}
