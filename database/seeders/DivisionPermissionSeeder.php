<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class DivisionPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       
        $permission = Permission::updateOrCreate(
            ['attribute' => 'division'],
            [
                'keywords' => [
                    'read'   => 'division_read',
                    'create' => 'division_create',
                    'update' => 'division_update',
                    'delete' => 'division_delete',
                ]
            ]
        );

       
        $role = Role::find(1);
        if ($role) {
            $existingPermissions = $role->permissions ?? [];
            $newKeywords = ['division_read', 'division_create', 'division_update', 'division_delete'];
            $role->permissions = array_unique(array_merge($existingPermissions, $newKeywords));
            $role->save();
        }

        echo "Division permission seeded and assigned to Superadmin successfully!\n";
    }
}
