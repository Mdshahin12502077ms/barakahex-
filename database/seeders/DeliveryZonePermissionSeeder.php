<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DeliveryZonePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $newKeywords = [
            'delivery_zone_read',
            'delivery_zone_create',
            'delivery_zone_update',
            'delivery_zone_delete',
        ];

        Permission::updateOrCreate(
            ['attribute' => 'delivery_zone'],
            [
                'keywords' => [
                    'read'   => 'delivery_zone_read',
                    'create' => 'delivery_zone_create',
                    'update' => 'delivery_zone_update',
                    'delete' => 'delivery_zone_delete',
                ]
            ]
        );

        $role = Role::find(1);
        if ($role) {
            $role->permissions = array_unique(array_merge($role->permissions ?? [], $newKeywords));
            $role->save();
        }

        $users = User::where('user_type', 'staff')->get();
        foreach ($users as $user) {
            $user->permissions = array_unique(array_merge($user->permissions ?? [], $newKeywords));
            $user->save();
        }

        echo "Delivery Zone permissions seeded successfully!\n";
    }
}
