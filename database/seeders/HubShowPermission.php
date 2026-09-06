<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HubShowPermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permission = Permission::updateOrCreate(
            ['attribute' => 'hub'],
            [
                'keywords' => [
                    'read'   => 'hub_read',
                    'create' => 'hub_create',
                    'update' => 'hub_update',
                    'delete' => 'hub_delete',
                ]
            ]
        );

       
        $role = Role::find(1);
        if ($role) {
            $existingPermissions = $role->permissions ?? [];
            $newKeywords = ['hub_read', 'hub_create', 'hub_update', 'hub_delete'];
            $role->permissions = array_unique(array_merge($existingPermissions, $newKeywords));
            $role->save();
        }

        echo "Hub permission seeded and assigned to Superadmin successfully!\n";
    }
    
}
