<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('attribute')->nullable();
            $table->text('keywords')->nullable();
            $table->timestamps();
        });

        $attributes = [
            //for staff
            'users' => ['read' => 'user_read', 'create' => 'user_create', 'update' => 'user_update', 'delete' => 'user_delete', 'account_activity_read' => 'user_account_activity_read', 'payment_logs_read' => 'user_payment_logs_read', 'logout_from_devices' => 'user_logout_from_devices'],
            'roles' => ['read' => 'role_read', 'create' => 'role_create', 'update' => 'role_update', 'delete' => 'role_delete'],
            'permissions' => ['read' => 'permission_read', 'create' => 'permission_create', 'update' => 'permission_update', 'delete' => 'permission_delete'],
            'merchant' => ['read' => 'merchant_read', 'read_all' => 'read_all_merchant', 'use_all' => 'use_all_merchant', 'create' => 'merchant_create', 'update' => 'merchant_update', 'delete' => 'merchant_delete', 'shop_read' => 'merchant_shop_read', 'shop_create' => 'merchant_shop_create', 'shop_delete' => 'merchant_shop_delete', 'shop_update' => 'merchant_shop_update', 'staff_read' => 'merchant_staff_read', 'staff_create' => 'merchant_staff_create', 'staff_update' => 'merchant_staff_update', 'payment_account_read' => 'merchant_payment_account_read', 'payment_account_update' => 'merchant_payment_account_update', 'account_activity_read' => 'merchant_account_activity_read', 'cod_charge_read' => 'merchant_cod_charge_read', 'charge_read' => 'merchant_charge_read', 'payment_logs_read' => 'merchant_payment_logs_read', 'api_credentials_read' => 'merchant_api_credentials_read', 'api_credentials_update' => 'merchant_api_credentials_update', 'download_closing_report' => 'download_closing_report'],
            'delivery_man' => ['read' => 'deliveryman_read', 'read_all' => 'read_all_delivery_man', 'use_all' => 'use_all_delivery_man', 'create' => 'deliveryman_create', 'update' => 'deliveryman_update', 'delete' => 'deliveryman_delete', 'account_activity_read' => 'deliveryman_account_activity_read', 'payment_logs_read' => 'deliveryman_payment_logs_read'],
            'parcel' => ['read' => 'parcel_read', 'read_all' => 'read_all_parcel', 'create' => 'parcel_create', 'update' => 'parcel_update', 'delete' => 'parcel_delete', 'pickup_assigned' => 'parcel_pickup_assigned', 'reschedule_pickup' => 'parcel_reschedule_pickup', 'received_by_pickup_man' => 'parcel_received_by_pickup_man', 'received_to_warehouse' => 'parcel_received', 'transfer_to_branch' => 'parcel_transfer_to_branch', 'transfer_receive_to_branch' => 'parcel_transfer_receive_to_branch', 'delivery_assigned' => 'parcel_delivery_assigned', 'reschedule_delivery' => 'parcel_reschedule_delivery', 'returned_to_warehouse' => 'parcel_returned_to_warehouse', 'return_assigned_to_merchant' => 'parcel_return_assigned_to_merchant', 'delivered' => 'parcel_delivered', 'delivery_backward' => 'parcel_backward', 'returned_to_merchant' => 'parcel_returned_to_merchant', 'cancel' => 'parcel_cancel', 'send_to_paperfly' => 'send_to_paperfly'],
            'income' => ['read' => 'income_read', 'read_all' => 'read_all_income', 'create' => 'income_create', 'update' => 'income_update', 'delete' => 'income_delete'],
            'expense' => ['read' => 'expense_read', 'read_all' => 'read_all_expense', 'create' => 'expense_create', 'update' => 'expense_update', 'delete' => 'expense_delete'],
            'withdraw' => ['read' => 'withdraw_read', 'read_all' => 'read_all_withdraw', 'create' => 'withdraw_create', 'update' => 'withdraw_update', 'process' => 'withdraw_process', 'reject' => 'withdraw_reject', 'add_to_bulk_withdraw' => 'add_to_bulk_withdraw'],
            'bulk_withdraw' => ['read' => 'bulk_withdraw_read', 'read_all' => 'read_all_bulk_withdraw', 'create' => 'bulk_withdraw_create', 'update' => 'bulk_withdraw_update', 'process' => 'bulk_withdraw_process', 'download_payment_sheet' => 'download_payment_sheet'],
            'sms_setting' => ['read' => 'sms_setting_read', 'update' => 'sms_setting_update', 'send_bulk_sms' => 'sms_campaign_message_send', 'send_sms' => 'custom_sms_send'],
            'report' => ['read' => 'report_read', 'transaction_history_read' => 'transaction_history_read', 'parcels_summary_read' => 'parcels_summary_read', 'total_summary_read' => 'total_summary_read', 'income_expense_report_read' => 'income_expense_report_read', 'profit_summary_report_read' => 'profit_summary_report_read', 'merchant_summary_report_read' => 'merchant_summary_report_read', 'dashboard_statistics_read' => 'dashboard_statistics_read'],
            'account' => ['read' => 'account_read', 'read_all' => 'read_all_account', 'create' => 'account_create', 'update' => 'account_update', 'statement' => 'account_statement'],
            'fund_transfer' => ['read' => 'fund_transfer_read', 'read_all' => 'read_all_fund_transfer', 'create' => 'fund_transfer_create', 'update' => 'fund_transfer_update', 'delete' => 'fund_transfer_delete'],
            'branch' => ['read' => 'branch_read', 'create' => 'branch_create', 'update' => 'branch_update', 'delete' => 'branch_delete'],
            'third_party' => ['read' => 'third_party_read', 'create' => 'third_party_create', 'update' => 'third_party_update', 'delete' => 'third_party_delete'],
            'notice' => ['read' => 'notice_read', 'create' => 'notice_create', 'update' => 'notice_update', 'delete' => 'notice_delete'],
            'settings' => ['read' => 'settings_read', 'sms_settings_update' => 'sms_settings_update', 'charge_update' => 'charge_setting_update', 'pickup_and_delivery_time_update' => 'pickup_and_delivery_time_setting_update', 'preference_update' => 'preference_setting_update', 'panel_setting' => 'panel_setting', 'preference' => 'preference', 'payment_method' => 'payment_method'],
            'email_template' => ['read' => 'email_template_read', 'create' => 'email_template_create', 'update' => 'email_template_update', 'delete' => 'email_template_delete', 'server_configuration_update' => 'server_configuration_update'],
            'currency' => ['read' => 'currency_read', 'create' => 'currency_create', 'update' => 'currency_update', 'delete' => 'currency_delete', 'default_currency', 'currency_format'],
            'country' => ['read' => 'country_read', 'create' => 'country_create', 'update' => 'country_update', 'delete' => 'country_delete'],
            'utility' => ['read' => 'server_info', 'update' => 'system_update', 'extension_library' => 'extension_library', 'filesystem' => 'filesystem', 'system_info' => 'system_info'],
            'payment_method' => ['read' => 'payment_method_read', 'create' => 'payment_method_create', 'update' => 'payment_method_update', 'delete' => 'payment_method_delete'],
            'apikeys' => ['view' => 'apikeys.index', 'create' => 'apikeys.create', 'edit' => 'apikeys.edit', 'revoke' => 'apikeys.revoke', 'delete' => 'apikeys.destroy',],
            'website setting' => ['website_themes' => 'website.themes', 'website_menu' => 'website.menu', 'section_title' => 'section.title', 'hero_section' => 'hero.section', 'price_section' => 'price.section', 'contact_section' => 'contact.section', 'call_to_action' => 'website.cta', 'footer_content' => 'footer.content', 'website_seo' => 'website_setting.seo', 'custom_js' => 'website_setting.custom_js', 'custom_css' => 'website_setting.custom_css', 'google_setup' => 'website_setting.google_setup', 'facebook_pixel' => 'website_setting.fb_pixel',],
            'partner_logo' => ['view' => 'partner_logo.index', 'create' => 'partner_logo.create', 'edit' => 'partner_logo.edit', 'delete' => 'partner_logo.destroy',],
            'news_and_event' => ['view' => 'news_and_event.index', 'create' => 'news_and_event.create', 'edit' => 'news_and_event.edit', 'delete' => 'news_and_event.destroy',],
            'about' => ['view' => 'about.index', 'create' => 'about.create', 'edit' => 'about.edit', 'delete' => 'about.destroy',],
            'service' => ['view' => 'service.index', 'create' => 'service.create', 'edit' => 'service.edit', 'delete' => 'service.destroy',],
            'feature' => ['view' => 'feature.index', 'create' => 'feature.create', 'edit' => 'feature.edit', 'delete' => 'feature.destroy',],
            'statistic' => ['view' => 'statistic.index', 'create' => 'statistic.create', 'edit' => 'statistic.edit', 'delete' => 'statistic.destroy',],
            'testimonial' => ['view' => 'testimonial.index', 'create' => 'testimonial.create', 'edit' => 'testimonial.edit', 'delete' => 'testimonial.destroy',],
            'faq' => ['view' => 'faq.index', 'create' => 'faq.create', 'edit' => 'faq.edit', 'delete' => 'faq.destroy',],
            'pages' => ['view' => 'pages.index', 'create' => 'pages.create', 'edit' => 'pages.edit', 'delete' => 'pages.destroy',],
              'division' => [
                                'read'   => 'division_read',
                                'create' => 'division_create',
                                'update' => 'division_update',
                                'delete' => 'division_delete'
                            ],

                            'district' => [
                                    'read'   => 'district_read',
                                    'create' => 'district_create',
                                    'update' => 'district_update',
                                    'delete' => 'district_delete'
                                ],
                    'upazila' => [
                                    'read'   => 'upazila_read',
                                    'create' => 'upazila_create',
                                    'update' => 'upazila_update',
                                    'delete' => 'upazila_delete'
                                ],

                           'delivery_zone'=>[
                               'read' =>'delivery_zone.read',
                               'create' =>'delivery_zone.create',
                               'update' =>'delivery_zone.update',
                               'delete' =>'delivery_zone.delete'
                           ]     



        ];

        foreach ($attributes as $key => $attribute) {
            $permission = new Permission();
            $permission->attribute = $key;
            $permission->keywords = $attribute;
            $permission->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
