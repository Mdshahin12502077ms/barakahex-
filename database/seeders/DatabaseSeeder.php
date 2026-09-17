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
        $this->call(BangladeshLocationSeeder::class);
        
        // Permissions Seeders
        $this->call(DivisionPermissionSeeder::class);
        $this->call(DistrictPermissionSeeder::class);
        $this->call(ThanaUpazila::class);
        $this->call(DeliveryZonePermissionSeeder::class);
        $this->call(AreaPermissionSeeder::class);
        $this->call(BagPermission::class);
        $this->call(HubShowPermission::class);
        $this->call(PercelOtpPermission::class);
        $this->call(AuditLogPermission::class);
        $this->call(RiderReportPermission::class);
        $this->call(FinancialPermission::class);
        $this->call(SupportTicketPermission::class);
        $this->call(AnalyticsPermission::class);
        $this->call(CrmPermissionSeeder::class);
        $this->call(DeliveryTimeSetPermission::class);
        $this->call(ReturnItemsPermission::class);
    }
}
