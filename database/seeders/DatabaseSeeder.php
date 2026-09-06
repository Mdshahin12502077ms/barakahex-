<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\BranchSeeder;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(DemoSeeder::class);
        $this->call(DivisionPermissionSeeder::class);
        $this->call(DistrictPermissionSeeder::class);
        $this->call(ThanaUpazila::class);
        $this->call(DeliveryZonePermissionSeeder::class);
        $this->call(AreaPermissionSeeder::class);
    }
}
