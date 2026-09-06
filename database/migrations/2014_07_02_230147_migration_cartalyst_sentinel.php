<?php

/*
 * Part of the Sentinel package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Sentinel
 * @version    5.1.0
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2020, Cartalyst LLC
 * @link       https://cartalyst.com
 */
use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Role;
use App\Models\User;
use App\Models\Branch;
use App\Models\RoleUser;
use App\Enums\UserTypeEnum;
use Faker\Factory as Faker;
use Database\Seeders\RoleSeeder;


class MigrationCartalystSentinel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('name');
            $table->text('permissions')->nullable();
            $table->timestamps();
            $table->engine = 'InnoDB';
            $table->unique('slug');
            $table->enum('status', [
                StatusEnum::ACTIVE->value,
                StatusEnum::INACTIVE->value,
            ])->default(StatusEnum::ACTIVE->value);
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('phone_number')->nullable()->comment('for merchant login instead of email');
            $table->string('password');
            $table->text('permissions')->nullable();
            $table->text('shops')->nullable()->comment('will be used for merchant staff');
            $table->timestamp('last_login')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->enum('user_type', ['staff', 'delivery', 'merchant', 'merchant_staff'])->default('staff')->comment('staff for all super admin and admin type all');
            $table->enum('status', [
                StatusEnum::ACTIVE->value,
                StatusEnum::INACTIVE->value,
            ])->default(StatusEnum::ACTIVE->value);
            $table->integer('otp')->nullable()->comment('used for reset password request confirmation from app');
            $table->tinyInteger('is_primary')->default(0)->comment('May it will use for merchant');
            $table->unsignedBigInteger('merchant_id')->nullable();
            // $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('last_ip')->nullable();
            $table->timestamp('last_password_change')->nullable();
            $table->text('image_id')->nullable();
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->string('dashboard')->nullable();

            // $table->foreign('branch_id')->references('id')->on('branches')->onDelete('set null')->onUpdate('cascade');

            $table->decimal('balance', 8, 2)->default(0.00)->comment('cash amount when register for company staff');
            $table->string('lang')->default('en');
            // Foreign keys

            // Foreign keys
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

        });


        Schema::create('activations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('code');
            $table->boolean('completed')->default(0);
            $table->timestamp('completed_at')->nullable();
//            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create('persistences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('code');
            $table->unique('code');
//           $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            $table->engine = 'InnoDB';
        });

        Schema::create('reminders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('code');
            $table->boolean('completed')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->foreign('user_id')->references('id')->on('users')
            ->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            // $table->engine = 'InnoDB';
        });

        Schema::create('role_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('set null');
            $table->foreign('role_id')->references('id')->on('roles')->onUpdate('cascade')->onDelete('set null');
            $table->nullableTimestamps();
            // $table->engine = 'InnoDB';
        });

        Schema::create('throttle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type');
            $table->string('ip')->nullable();
            $table->index('user_id');
            $table->foreign('user_id')->references('id')->on('users')
            ->onUpdate('cascade')->onDelete('cascade');
            $table->timestamps();
            // $table->engine = 'InnoDB';
        });

        $this->roleSeeder();
    }


    public function roleSeeder()
    {
        Role::create([
            'name' => 'Superadmin',
            'slug' => 'superadmin',
            'permissions' => $this->superAdminPermissions()
        ]);
        Role::create([
            'name' => 'Admin',
            'slug' =>  Str::slug('Admin'),
            'permissions' => $this->adminPermissions()
        ]);
        Role::create([
            'name' => 'Branch Manager',
            'slug' => Str::slug('Branch Manager'),
            'permissions' => $this->branchManagerPermissions()
        ]);
        Role::create([
            'name' => 'Account Manager',
            'slug' => Str::slug('Account Manager'),
            'permissions' => $this->accountManagerPermissions()
        ]);
        Role::create([
            'name' => 'Support Executive',
            'slug' => Str::slug('Support Executive'),
            'permissions' => $this->supportExecutivePermissions()
        ]);
        Role::create([
            'name' => 'Administrator',
            'slug' => Str::slug('Administrator'),
            'permissions' => $this->adminPermissions(),
            'status'     => StatusEnum::ACTIVE
        ]);
    }

    public function superAdminPermissions()
    {
        return [
            'user_create',
            'user_read',
            'user_update',
            'user_delete',
            'user_account_activity_read',
            'user_payment_logs_read',
            'user_logout_from_devices',

            'role_create',
            'role_read',
            'role_update',
            'role_delete',

            'permission_read',
            'permission_create',
            'permission_update',
            'permission_delete',

            'apikeys.index',
            'apikeys.create',
            'apikeys.edit',
            'apikeys.revoke',
            'apikeys.destroy',

            'merchant_create',
            'merchant_read',
            'use_all_merchant',
            'read_all_merchant',
            'merchant_update',
            'merchant_delete',
            'merchant_shop_read',
            'merchant_shop_create',
            'merchant_shop_delete',
            'merchant_shop_update',
            'merchant_payment_account_read',
            'merchant_payment_account_update',
            'merchant_account_activity_read',
            'merchant_cod_charge_read',
            'merchant_charge_read',
            'merchant_payment_logs_read',
            'merchant_api_credentials_read',
            'merchant_api_credentials_update',
            'merchant_staff_read',
            'merchant_staff_create',
            'merchant_staff_update',
            'download_closing_report',

            'deliveryman_create',
            'deliveryman_read',
            'read_all_delivery_man',
            'use_all_delivery_man',
            'deliveryman_update',
            'deliveryman_delete',
            'deliveryman_account_activity_read',
            'deliveryman_payment_logs_read',

            'parcel_create',
            'parcel_read',
            'parcel_update',
            'parcel_delete',

            'read_all_parcel',
            'parcel_pickup_assigned',
            'parcel_reschedule_pickup',
            'parcel_received_by_pickup_man',
            'parcel_received',
            'parcel_transfer_to_branch',
            'parcel_transfer_receive_to_branch',
            'parcel_delivery_assigned',
            'parcel_reschedule_delivery',
            'parcel_returned_to_warehouse',
            'parcel_return_assigned_to_merchant',
            'parcel_delivered',
            'parcel_backward',
            'parcel_returned_to_merchant',
            'parcel_cancel',
            'parcel_delete',
            'send_to_paperfly',

            'income_create',
            'income_read',
            'read_all_income',
            'income_update',
            'income_delete',

            'expense_create',
            'expense_read',
            'read_all_expense',
            'expense_update',
            'expense_delete',

            'withdraw_read',
            'read_all_withdraw',
            'withdraw_create',
            'withdraw_update',
            'withdraw_process',
            'withdraw_reject',
            'add_to_bulk_withdraw',

            'sms_setting_read',
            'sms_setting_update',
            'sms_campaign_message_send',
            'custom_sms_send',

            'report_read',
            'transaction_history_read',
            'parcels_summary_read',
            'total_summary_read',
            'income_expense_report_read',
            'profit_summary_report_read',
            'merchant_summary_report_read',
            'dashboard_statistics_read',

            'account_read',
            'account_create',
            'account_update',
            'read_all_account',
            'account_statement',

            'fund_transfer_read',
            'read_all_fund_transfer',
            'fund_transfer_create',
            'fund_transfer_update',
            'fund_transfer_delete',

            'branch_read',
            'branch_create',
            'branch_update',
            'branch_delete',

            'email_template_read',
            'email_template_create',
            'email_template_update',
            'email_template_delete',
            'server_configuration_update',

            'third_party_read',
            'third_party_create',
            'third_party_update',
            'third_party_delete',

            'language_read',
            'language_create',
            'language_update',
            'language_delete',

            'payment_method_read',
            'payment_method_create',
            'payment_method_update',
            'payment_method_delete',

            'country_read',
            'country_create',
            'country_update',
            'country_delete',

            "email_template_read",
            "email_template_create",
            "email_template_update",
            "email_template_delete",

            'notice_read',
            'notice_create',
            'notice_update',
            'notice_delete',

            'server_info',
            'system_update',
            'extension_library',
            'filesystem',

            'settings_read',
            'sms_settings_update',
            'charge_setting_update',
            'pickup_and_delivery_time_setting_update',
            'preference_setting_update',
            'panel_setting',
            'preference',
            'payment_method',
            'general_setting',

            'bulk_withdraw_read',
            'read_all_bulk_withdraw',
            'bulk_withdraw_create',
            'bulk_withdraw_update',
            'bulk_withdraw_process',
            'download_payment_sheet',
            'news_and_event.index',
            'news_and_event.create',
            'news_and_event.edit',
            'news_and_event.destroy',
            'about.index',
            'about.create',
            'about.edit',
            'about.destroy',
            'service.index',
            'service.create',
            'service.edit',
            'service.destroy',
            'feature.index',
            'feature.create',
            'feature.edit',
            'feature.destroy',
            'statistic.index',
            'statistic.create',
            'statistic.edit',
            'statistic.destroy',
            'price.section',
            'contact.section',
            'website.themes',
            'website.menu',
            'section.title',
            'hero.section',
            'website.cta',
            'footer.content',
            'website_setting.seo',
            'website_setting.custom_js',
            'website_setting.custom_css',
            'website_setting.google_setup',
            'website_setting.fb_pixel',
            'website_setting.gdpr',
            'partner_logo.index',
            'partner_logo.create',
            'partner_logo.edit',
            'partner_logo.destroy',
            'testimonial.index',
            'testimonial.create',
            'testimonial.edit',
            'testimonial.destroy',
            'faq.index',
            'faq.create',
            'faq.destroy',
            'faq.edit',
            'pages.index',
            'pages.create',
            'pages.edit',
            'pages.destroy',
        ];
    }
    public function adminPermissions()
    {
        return [
            'user_create',
            'user_read',
            'user_update',
            'user_delete',
            'user_account_activity_read',
            'user_payment_logs_read',
            'user_logout_from_devices',

            'role_create',
            'role_read',
            'role_update',
            'role_delete',

            'permission_read',
            'permission_create',
            'permission_update',
            'permission_delete',
            'apikeys.index',
            'apikeys.create',
            'apikeys.edit',
            'apikeys.revoke',
            'apikeys.destroy',

            'merchant_create',
            'merchant_read',
            'use_all_merchant',
            'read_all_merchant',
            'merchant_update',
            'merchant_delete',
            'merchant_shop_read',
            'merchant_shop_create',
            'merchant_shop_delete',
            'merchant_shop_update',
            'merchant_payment_account_read',
            'merchant_payment_account_update',
            'merchant_account_activity_read',
            'merchant_cod_charge_read',
            'merchant_charge_read',
            'merchant_payment_logs_read',
            'merchant_api_credentials_read',
            'merchant_api_credentials_update',
            'merchant_staff_read',
            'merchant_staff_create',
            'merchant_staff_update',
            'download_closing_report',

            'deliveryman_create',
            'deliveryman_read',
            'read_all_delivery_man',
            'use_all_delivery_man',
            'deliveryman_update',
            'deliveryman_delete',
            'deliveryman_account_activity_read',
            'deliveryman_payment_logs_read',

            'parcel_create',
            'parcel_read',
            'parcel_update',
            'parcel_delete',

            'read_all_parcel',
            'parcel_pickup_assigned',
            'parcel_reschedule_pickup',
            'parcel_received_by_pickup_man',
            'parcel_received',
            'parcel_transfer_to_branch',
            'parcel_transfer_receive_to_branch',
            'parcel_delivery_assigned',
            'parcel_reschedule_delivery',
            'parcel_returned_to_warehouse',
            'parcel_return_assigned_to_merchant',
            'parcel_delivered',
            'parcel_backward',
            'parcel_returned_to_merchant',
            'parcel_cancel',
            'parcel_delete',
            'send_to_paperfly',

            'income_create',
            'income_read',
            'read_all_income',
            'income_update',
            'income_delete',

            'expense_create',
            'expense_read',
            'read_all_expense',
            'expense_update',
            'expense_delete',

            'withdraw_read',
            'read_all_withdraw',
            'withdraw_create',
            'withdraw_update',
            'withdraw_process',
            'withdraw_reject',
            'add_to_bulk_withdraw',

            'sms_setting_read',
            'sms_setting_update',
            'sms_campaign_message_send',
            'custom_sms_send',

            'report_read',
            'transaction_history_read',
            'parcels_summary_read',
            'total_summary_read',
            'income_expense_report_read',
            'profit_summary_report_read',
            'merchant_summary_report_read',
            'dashboard_statistics_read',

            'account_read',
            'account_create',
            'account_update',
            'read_all_account',
            'account_statement',

            'fund_transfer_read',
            'read_all_fund_transfer',
            'fund_transfer_create',
            'fund_transfer_update',
            'fund_transfer_delete',

            'branch_read',
            'branch_create',
            'branch_update',
            'branch_delete',

            'email_template_read',
            'email_template_create',
            'email_template_update',
            'email_template_delete',
            'server_configuration_update',

            'third_party_read',
            'third_party_create',
            'third_party_update',
            'third_party_delete',

            'language_read',
            'language_create',
            'language_update',
            'language_delete',

            'payment_method_read',
            'payment_method_create',
            'payment_method_update',
            'payment_method_delete',

            'country_read',
            'country_create',
            'country_update',
            'country_delete',

            "email_template_read",
            "email_template_create",
            "email_template_update",
            "email_template_delete",

            'notice_read',
            'notice_create',
            'notice_update',
            'notice_delete',

            'server_info',
            'system_update',
            'extension_library',
            'filesystem',

            'settings_read',
            'sms_settings_update',
            'charge_setting_update',
            'pickup_and_delivery_time_setting_update',
            'preference_setting_update',
            'panel_setting',
            'preference',
            'payment_method',
            'general_setting',

            'bulk_withdraw_read',
            'read_all_bulk_withdraw',
            'bulk_withdraw_create',
            'bulk_withdraw_update',
            'bulk_withdraw_process',
            'download_payment_sheet',

            'news_and_event.index',
            'news_and_event.create',
            'news_and_event.edit',
            'news_and_event.destroy',
            'about.index',
            'about.create',
            'about.edit',
            'about.destroy',
            'service.index',
            'service.create',
            'service.edit',
            'service.destroy',
            'feature.index',
            'feature.create',
            'feature.edit',
            'feature.destroy',
            'statistic.index',
            'statistic.create',
            'statistic.edit',
            'statistic.destroy',
            'price.section',
            'contact.section',
            'website.themes',
            'website.menu',
            'section.title',
            'hero.section',
            'website.cta',
            'footer.content',
            'website_setting.seo',
            'website_setting.custom_js',
            'website_setting.custom_css',
            'website_setting.google_setup',
            'website_setting.fb_pixel',
            'website_setting.gdpr',
            'partner_logo.index',
            'partner_logo.create',
            'partner_logo.edit',
            'partner_logo.destroy',
            'testimonial.index',
            'testimonial.create',
            'testimonial.edit',
            'testimonial.destroy',
            'faq.index',
            'faq.create',
            'faq.destroy',
            'faq.edit',
            'pages.index',
            'pages.create',
            'pages.edit',
            'pages.destroy',
        ];
    }

    public function branchManagerPermissions()
    {
        return [
            "merchant_read",
            "use_all_merchant",
            "deliveryman_read",
            "parcel_read",
            "parcel_create",
            "parcel_pickup_assigned",
            "parcel_reschedule_pickup",
            "parcel_received",
            "parcel_delivery_assigned",
            "parcel_reschedule_delivery",
            "parcel_returned_to_warehouse",
            "parcel_return_assigned_to_merchant",
            "parcel_delivered",
            "parcel_returned_to_merchant"
        ];
    }
    public function agentPermissions()
    {
        return [
            "merchant_read",
            "deliveryman_read",
            "parcel_read",
            "parcel_create",
            "parcel_update",
            "parcel_pickup_assigned",
            "parcel_reschedule_pickup",
            "parcel_received_by_pickup_man",
            "parcel_received",
            "parcel_transfer_to_hub",
            "parcel_transfer_receive_to_hub",
            "parcel_delivery_assigned",
            "parcel_reschedule_delivery",
            "parcel_returned_to_warehouse",
            "parcel_return_assigned_to_merchant",
            "parcel_delivered",
            "parcel_returned_to_merchant",
            "report_read",
            "dashboard_statistics_read"
        ];
    }


    public function accountManagerPermissions()
    {
        return [
            "merchant_read",
            "use_all_merchant",
            "deliveryman_read",
            "use_all_delivery_man",
            "parcel_read",
            "read_all_parcel",
            "parcel_create",
            "parcel_update",
            "parcel_pickup_assigned",
            "parcel_reschedule_pickup",
            "parcel_received_by_pickup_man",
            "parcel_received",
            "parcel_transfer_to_hub",
            "parcel_transfer_receive_to_hub",
            "parcel_delivery_assigned",
            "parcel_reschedule_delivery",
            "parcel_returned_to_warehouse",
            "parcel_return_assigned_to_merchant",
            "parcel_delivered",
            "parcel_returned_to_merchant",
            "parcel_cancel"

        ];
    }
    public function nightTeamPermissions()
    {
        return [
            "merchant_read",
            "use_all_merchant",
            "deliveryman_read",
            "use_all_delivery_man",
            "parcel_read",
            "read_all_parcel",
            "parcel_create",
            "parcel_update",
            "parcel_pickup_assigned",
            "parcel_reschedule_pickup",
            "parcel_received_by_pickup_man",
            "parcel_received",
            "parcel_transfer_to_hub",
            "parcel_transfer_receive_to_hub",
            "parcel_delivery_assigned",
            "parcel_reschedule_delivery",
            "parcel_returned_to_warehouse",
            "parcel_return_assigned_to_merchant",
            "parcel_delivered",
            "parcel_returned_to_merchant",
            "parcel_cancel"

        ];
    }

    public function supportExecutivePermissions()
    {
        return[
            "manage_parcel",
            "all_parcel",
            "manage_payment",
            "all_parcel_payment",
            "read_logs",
            "all_parcel_logs",
            "all_payment_logs",
            "manage_company_information",
            "manage_payment_accounts",
            "manage_shops",
            "delivery_charge",
            "cash_on_delivery_charge"
        ];
    }

    public function merchatStaffPermissions()
    {
        return[
            "manage_parcel",
            "all_parcel",
            "manage_payment",
            "all_parcel_payment",
            "read_logs",
            "all_parcel_logs",
            "all_payment_logs",
            "manage_company_information",
            "manage_payment_accounts",
            "manage_shops",
            "delivery_charge",
            "cash_on_delivery_charge"
        ];
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
        });

        Schema::dropIfExists('users');
        Schema::dropIfExists('activations');
        Schema::dropIfExists('persistences');
        Schema::dropIfExists('reminders');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('role_users');
        Schema::dropIfExists('throttle');
    }

}
