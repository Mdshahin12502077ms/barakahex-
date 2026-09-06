<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use App\Models\RoleUser;
use App\Enums\UserTypeEnum;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Database\Seeders\RoleSeeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeds a single global default branch with a branch manager user (same pattern as previous code).
     *
     * @return void
     */
    public function run()
    {
        if (!Schema::hasTable('branches')) {
            return;
        }
        if (DB::table('branches')->where('default', 1)->exists()) {
            return;
        }

        $faker = Faker::create('en');
        $roleSeeder = new RoleSeeder();
        $pass_has = bcrypt(123456);

        $branch = Branch::create([
            'user_id'       => null,
            'name'          => 'Main Branch',
            'address'       => 'Default address',
            'phone_number'  => null,
            'status'        => 'active',
            'default'       => 1,
        ]);

        $email = 'staff_' . $branch->id . '@spagreen.net';
        $user = new User();
        $user->first_name   = $faker->firstName;
        $user->last_name   = $faker->lastName;
        $user->email       = $email;
        $user->password    = $pass_has;
        $user->permissions = $roleSeeder->branchManagerPermissions();
        $user->image_id    = null;
        $user->user_type   = UserTypeEnum::STAFF;
        $user->branch_id   = $branch->id;
        $user->save();

        $branch->user_id = $user->id;
        $branch->update();

        $role = new RoleUser();
        $role->user_id = $user->id;
        $role->role_id = 2;
        $role->save();

        $activation = Activation::create($user);
        Activation::complete($user, $activation->code);
    }
}
