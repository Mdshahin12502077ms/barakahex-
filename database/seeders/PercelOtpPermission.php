<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PercelOtpPermission extends Seeder
{
     public function run()
    {
        $newKeywords = [
            'percel_otp_read',
            'percel_otp_create',
            'percel_otp_update',
            'percel_otp_delete',
        ];

       
        Permission::updateOrCreate(
            ['attribute' => 'percel_otp'],
            [
                'keywords' => [
                    'read'   => 'percel_otp_read',
                    'create' => 'percel_otp_create',
                    'update' => 'percel_otp_update',
                    'delete' => 'percel_otp_delete',
                ]
            ]
        );

       
        $role = Role::find(1);
        if ($role) {
            $existingPermissions = $role->permissions ?? [];
            $role->permissions = array_unique(array_merge($existingPermissions, $newKeywords));
            $role->save();
        }

       
        $users = User::where('user_type', 'staff')->get();
        foreach ($users as $user) {
            $existingUserPermissions = $user->permissions ?? [];
            $user->permissions = array_unique(array_merge($existingUserPermissions, $newKeywords));
            $user->save();
        }

        echo "percel otp permission seeded and assigned to Superadmin & Staff users successfully!\n";
    }
}
