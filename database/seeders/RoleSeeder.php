<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Enums\StatusEnum;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'Superadmin',
            'slug' => 'superadmin',
            'permissions' => $this->superAdminPermissions()
        ]);
        Role::create([
            'name' => 'Admin',
            'slug' => Str::slug('Admin'),
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
            'status' => StatusEnum::ACTIVE
        ]);

        Role::create([
            'name' => 'Customer Support Executive',
            'slug' => Str::slug('Customer Support Executive'),
            'permissions' => $this->customerSupportExecutivePermissions(),
            'status' => StatusEnum::ACTIVE
        ]);

        Role::create([
            'name' => 'Merchant Support',
            'slug' => Str::slug('Merchant Support'),
            'permissions' => $this->merchantSupportPermissions(),
            'status' => StatusEnum::ACTIVE
        ]);

        Role::create([
            'name' => 'Data Entry',
            'slug' => Str::slug('Data Entry'),
            'permissions' => $this->dataEntryPermissions(),
            'status' => StatusEnum::ACTIVE
        ]);

        Role::create([
            'name' => 'Auditor',
            'slug' => Str::slug('Auditor'),
            'permissions' => $this->auditorPermissions(),
            'status' => StatusEnum::ACTIVE
        ]);
    }

    public function superAdminPermissions()
    {
        return [
            'audit_log_read',
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
            'branch_hub_report_read',
            'financial_report_read',

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
            'audit_log_read',
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
            
            'district_read',
            'district_create',
            'district_update',
            'district_delete',

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
            'branch_hub_report_read',
            'financial_report_read',

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
            "parcel_transfer_receive_to_branch",
            "parcel_delivered",
            "parcel_returned_to_merchant",
            "branch_hub_report_read"
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
        return [
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
        return [
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

    

    public function customerSupportExecutivePermissions()
    {
        return [
           
            'user_read',
            'merchant_read',
            'parcel_read',
            'read_all_parcel',
            'deliveryman_read',
            'report_read',
            'dashboard_statistics_read',
            'transaction_history_read',
            'parcels_summary_read',

          
            'parcel_create',

           
            'parcel_update',
            'parcel_reschedule_pickup',
            'parcel_reschedule_delivery',

            
            'parcel_cancel',

           
            'parcel_received',
            'parcel_received_by_pickup_man',

           
            'parcel_pickup_assigned',
            'parcel_delivery_assigned',
            'parcel_return_assigned_to_merchant',

        
            'withdraw_read',
            'withdraw_process',

           
            'download_closing_report',
            'download_payment_sheet',
        ];
    }

    public function merchantSupportPermissions()
    {
        return [
            
            'merchant_read',
            'read_all_merchant',
            'merchant_shop_read',
            'merchant_payment_account_read',
            'merchant_account_activity_read',
            'merchant_cod_charge_read',
            'merchant_charge_read',
            'merchant_payment_logs_read',
            'merchant_api_credentials_read',
            'merchant_staff_read',
            'parcel_read',
            'read_all_parcel',
            'report_read',
            'merchant_summary_report_read',
            'dashboard_statistics_read',

           
            'merchant_create',
            'merchant_shop_create',
            'merchant_staff_create',

           
            'merchant_update',
            'merchant_shop_update',
            'merchant_payment_account_update',
            'merchant_staff_update',
            'merchant_api_credentials_update',

           
            'merchant_delete',
            'merchant_shop_delete',

            
            'withdraw_read',
            'read_all_withdraw',
            'withdraw_process',
            'withdraw_reject',

           
            'parcel_return_assigned_to_merchant',
            'parcel_delivery_assigned',

            
            'add_to_bulk_withdraw',
            'bulk_withdraw_read',
            'bulk_withdraw_process',

          
            'download_closing_report',
            'download_payment_sheet',
        ];
    }

    public function dataEntryPermissions()
    {
        return [
           
            'merchant_read',
            'parcel_read',
            'deliveryman_read',
            'branch_read',
            'dashboard_statistics_read',

        
            'parcel_create',
            'merchant_create',
            'merchant_shop_create',

            
            'parcel_update',
            'merchant_update',
            'merchant_shop_update',

           
            'parcel_cancel',

            
            'parcel_pickup_assigned',
            'parcel_received_by_pickup_man',

            
            'download_closing_report',
        ];
    }

    public function auditorPermissions()
    {
       
        return [
            'audit_log_read',
            'user_read',
            'user_account_activity_read',
            'user_payment_logs_read',
            'role_read',
            'merchant_read',
            'read_all_merchant',
            'merchant_shop_read',
            'merchant_payment_account_read',
            'merchant_account_activity_read',
            'merchant_cod_charge_read',
            'merchant_charge_read',
            'merchant_payment_logs_read',
            'merchant_staff_read',
            'deliveryman_read',
            'read_all_delivery_man',
            'deliveryman_account_activity_read',
            'deliveryman_payment_logs_read',
            'parcel_read',
            'read_all_parcel',
            'income_read',
            'read_all_income',
            'expense_read',
            'read_all_expense',
            'withdraw_read',
            'read_all_withdraw',
            'report_read',
            'transaction_history_read',
            'parcels_summary_read',
            'total_summary_read',
            'income_expense_report_read',
            'profit_summary_report_read',
            'merchant_summary_report_read',
            'dashboard_statistics_read',
            'account_read',
            'read_all_account',
            'account_statement',
            'fund_transfer_read',
            'read_all_fund_transfer',
            'branch_read',
            'bulk_withdraw_read',
            'read_all_bulk_withdraw',

            
            'download_closing_report',
            'download_payment_sheet',
        ];
    }
}
