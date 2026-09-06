<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BagPermission extends Seeder
{
    public function run(): void
    {
        $newKeywords = [
            'bag_read',
            'bag_create',
            'bag_update',
            'bag_delete',
        ];

        Permission::updateOrCreate(
            ['attribute' => 'bag'],
            [
                'keywords' => [
                    'read'   => 'bag_read',
                    'create' => 'bag_create',
                    'update' => 'bag_update',
                    'delete' => 'bag_delete',
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

        echo "Bag permissions seeded successfully!\n";
    }
}
