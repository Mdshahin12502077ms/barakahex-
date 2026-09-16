<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnalyticsPermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = \App\Models\Permission::updateOrCreate(
            ['attribute' => 'analytics'],
            [
                'keywords' => [
                    'operations' => 'analytics_operations_read',
                    'rider'      => 'analytics_rider_read',
                    'merchant'   => 'analytics_merchant_read',
                    'financial'  => 'analytics_financial_read',
                ]
            ]
        );

        $role = \App\Models\Role::find(1); // Superadmin role
        if ($role) {
            $existingPermissions = $role->permissions ?? [];
            $newKeywords = ['analytics_operations_read', 'analytics_rider_read', 'analytics_merchant_read', 'analytics_financial_read'];
            $role->permissions = array_unique(array_merge($existingPermissions, $newKeywords));
            $role->save();
        }

        // Sync permissions to superadmin user (usually ID 1)
        $superadmin = \App\Models\User::find(1);
        if ($superadmin) {
            $superadmin->permissions = array_unique(array_merge($superadmin->permissions ?? [], $newKeywords ?? []));
            $superadmin->save();
        }

        echo "Analytics permissions seeded and assigned to Superadmin successfully!\n";
    }
}
