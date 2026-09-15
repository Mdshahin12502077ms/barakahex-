<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupportTicketPermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allKeywords = [
            'support_ticket_read',
            'support_ticket_create',
            'support_ticket_update',
            'support_ticket_delete',
        ];

        $supportExecutiveKeywords = [
            'support_ticket_read',
            'support_ticket_create',
            'support_ticket_update',
        ];

        $branchManagerKeywords = [
            'support_ticket_read',
            'support_ticket_create',
        ];

        // 1. Create or update Support Ticket permission group in permissions table
        Permission::updateOrCreate(
            ['attribute' => 'support_tickets'],
            [
                'keywords' => [
                    'read'   => 'support_ticket_read',
                    'create' => 'support_ticket_create',
                    'update' => 'support_ticket_update',
                    'delete' => 'support_ticket_delete',
                ]
            ]
        );

        // 2. Define role mappings
        $adminRoles = ['superadmin', 'admin', 'administrator'];
        $supportRoles = ['support-executive', 'customer-support-executive', 'merchant-support'];
        $branchRoles = ['branch-manager'];

        $targetSlugs = array_merge($adminRoles, $supportRoles, $branchRoles);
        $roles = Role::whereIn('slug', $targetSlugs)->get();

        foreach ($roles as $role) {
            if (in_array($role->slug, $adminRoles)) {
                $keywords = $allKeywords;
            } elseif (in_array($role->slug, $branchRoles)) {
                $keywords = $branchManagerKeywords;
            } else {
                $keywords = $supportExecutiveKeywords;
            }

            // Update role permissions
            $existingRolePerms = $role->permissions ?? [];
            $role->permissions = array_values(array_unique(array_merge($existingRolePerms, $keywords)));
            $role->save();

            // Update users belonging to this role
            $userIds = DB::table('role_users')->where('role_id', $role->id)->pluck('user_id')->toArray();
            if (!empty($userIds)) {
                $users = User::whereIn('id', $userIds)->get();
                foreach ($users as $user) {
                    $existingUserPerms = $user->permissions ?? [];
                    $user->permissions = array_values(array_unique(array_merge($existingUserPerms, $keywords)));
                    $user->save();
                }
            }
        }

        // 3. Ensure primary Superadmin role (ID 1) and User (ID 1) get full permissions
        $superadminRole = Role::find(1);
        if ($superadminRole) {
            $superadminRole->permissions = array_values(array_unique(array_merge($superadminRole->permissions ?? [], $allKeywords)));
            $superadminRole->save();
        }

        $superadminUser = User::find(1);
        if ($superadminUser) {
            $superadminUser->permissions = array_values(array_unique(array_merge($superadminUser->permissions ?? [], $allKeywords)));
            $superadminUser->save();
        }

        $this->command->info('Support Ticket permissions seeded successfully for Superadmin, Admin, Support Executives, and Branch Managers!');
    }
}
