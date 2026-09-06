<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\RoleUser;
use App\Enums\UserTypeEnum;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Cartalyst\Sentinel\Laravel\Facades\Activation;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('en_BD');

        $superAdminRole = Role::find(1);
        $adminRole      = Role::find(2);

        $roleSeeder = new RoleSeeder();
        // // Start superadmin
        $admin = User::create([
            'first_name'        => 'Admin',
            'last_name'         => '',
            'dashboard'         => 'admin',
            'email'             => 'admin@admin.com',
            'permissions'       => $roleSeeder->adminPermissions(),
            'password'          => bcrypt(123456),
            'user_type'         => UserTypeEnum::STAFF,
        ]);

        $superAdmin = User::create([
            'first_name'        => 'Super',
            'last_name'         => 'Admin',
            'dashboard'         => 'admin',
            'email'             => 'superadmin@admin.com',
            'permissions'       => $roleSeeder->superAdminPermissions(),
            'password'          => bcrypt(123456),
            'user_type'         => UserTypeEnum::STAFF,
        ]);


        $activation = Activation::create($admin);
        Activation::complete($admin, $activation->code);
        $adminRole->users()->attach($admin);
        $pass_has =  bcrypt(123456);

        $activation = Activation::create($superAdmin);
        Activation::complete($superAdmin, $activation->code);
        $superAdminRole->users()->attach($superAdmin);
        $pass_has =  bcrypt(123456);

    }


}
