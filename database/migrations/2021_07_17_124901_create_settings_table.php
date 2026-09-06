<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('value')->nullable();
            $table->string('lang')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamps();

        });

        \DB::statement("INSERT INTO `settings` (`id`, `title`, `value`, `lang`, `created_at`, `updated_at`) VALUES
        (1, 'return_charge', '40', NULL, '2021-07-23 17:22:58', '2021-07-23 17:22:58'),
        (2, 'fragile_charge', '20', NULL, '2021-07-23 17:22:58', '2024-01-09 19:39:17'),
        (3, 'pickup_accept_start', '18', NULL, '2021-07-23 17:23:04', '2021-07-23 17:23:04'),
        (4, 'pickup_accept_end', '24', NULL, '2021-07-23 17:23:04', '2021-07-23 17:23:04'),
        (5, 'outside_dhaka_days', '8', NULL, '2021-07-23 17:23:04', '2024-01-21 20:54:23'),
        (6, 'paginate_all_list', '20', NULL, '2021-07-24 03:41:14', '2021-07-24 03:41:14'),
        (7, 'paginate_parcel_merchant_list', '50', NULL, '2021-07-24 04:11:01', '2021-07-24 04:11:01'),
        (8, 'paginate_api_list', '15', NULL, '2021-07-24 03:41:14', '2021-07-24 08:56:11'),
        (11, 'sms_cli', '', NULL, '2021-07-24 06:15:32', '2021-07-24 06:15:32'),
        (21, 'api_key', 'xxxxxxxxxxx', NULL, '2021-08-04 12:09:26', '2021-08-04 16:54:44'),
        (22, 'return_charge_dhaka', '50', NULL, '2021-08-04 16:52:57', '2024-01-08 19:22:46'),
        (23, 'return_charge_sub_city', '90', NULL, '2021-08-04 16:52:57', '2021-09-04 06:55:39'),
        (24, 'return_charge_outside_dhaka', '120', NULL, '2021-08-04 16:52:57', '2021-09-04 06:55:39'),
        (25, 'return_charge_type', 'on_demand', NULL, '2021-12-15 05:54:15', '2024-01-08 19:22:46'),
        (26, 'delivery_otp', 'none', NULL, NULL, '2023-01-30 02:33:07'),
        (28, 'delivery_otp', 'all', NULL, NULL, NULL),
        (29, 'sip_domain', '103.103.35.164:3939', NULL, NULL, '2023-01-18 06:31:49'),
        (30, 'current_version', '100', NULL, NULL, NULL),
        (31, 'update_skipable', 'true', NULL, NULL, '2023-01-18 06:31:27'),
        (32, 'update_url', '#', NULL, NULL, NULL),
        (33, 'phone_visible', 'true', NULL, NULL, '2023-01-18 06:31:27'),
        (34, 'admin_panel_title', 'DeliX', 'en', '2024-01-27 22:58:58', '2024-01-27 22:58:58'),
        (35, 'system_short_name', 'DeliX', 'en', '2024-01-27 22:58:58', '2024-01-27 22:58:58'),
        (36, 'admin_panel_copyright_text', 'Copyright @2024 by SpaGreen Creative', 'en', '2024-01-27 22:58:58', '2024-01-27 22:58:58'),
        (37, 'admin_logo', 'a:6:{s:7:\"storage\";s:5:\"local\";s:14:\"original_image\";s:39:\"images/20240128113929-admin_logo488.png\";s:11:\"image_80X80\";s:44:\"images/20240128113929-admin_logo-80X8077.png\";s:13:\"image_500x500\";s:0:\"\";s:12:\"image_100x36\";s:46:\"images/20240128113929-admin_logo-100x36160.png\";s:14:\"image_1200x630\";s:0:\"\";}', 'en', '2024-01-27 22:58:59', '2024-01-27 23:39:29'),
        (38, 'admin_mini_logo', 'a:6:{s:7:\"storage\";s:5:\"local\";s:14:\"original_image\";s:44:\"images/20240128105859-admin_mini_logo361.png\";s:11:\"image_80X80\";s:50:\"images/20240128105859-admin_mini_logo-80X80234.png\";s:13:\"image_500x500\";s:0:\"\";s:12:\"image_100x36\";s:51:\"images/20240128105859-admin_mini_logo-100x36125.png\";s:14:\"image_1200x630\";s:0:\"\";}', 'en', '2024-01-27 22:58:59', '2024-01-27 22:58:59'),
        (NULL, 'version_code', '1.0.0', 'en', '2024-01-27 23:37:07', '2024-01-31 07:22:06'),
        (232, 'system_name', 'DeliX', 'en', '2024-01-27 23:37:07', '2024-01-31 07:22:06'),
        (233, 'company_name', 'DeliX', 'en', '2024-01-27 23:37:07', '2024-01-31 07:22:06'),
        (234, 'tagline', 'Parcel Delivery System', 'en', '2024-01-27 23:37:07', '2024-01-27 23:37:07'),
        (235, 'phone', '1515232921', 'en', '2024-01-27 23:37:07', '2024-01-27 23:37:07'),
        (236, 'phone_country_id', '19', 'en', '2024-01-27 23:37:07', '2024-01-27 23:37:07'),
        (237, 'email_address', 'info@delix.cloud', 'en', '2024-01-27 23:37:07', '2024-01-27 23:37:07'),
        (238, 'activation_code', 'xxxxxxxxxxxxxx', 'en', '2024-01-27 23:37:07', '2024-01-27 23:37:07'),
        (239, 'time_zone', '1', 'en', '2024-01-27 23:37:07', '2024-01-27 23:37:07'),
        (240, 'default_language', 'en', 'en', '2024-01-27 23:37:07', '2024-01-27 23:37:07'),
        (241, 'default_country', '19', 'en', '2024-01-27 23:37:07', '2024-01-27 23:37:07'),
        (242, 'default_weight', 'KG', 'en', '2024-01-27 23:37:07', '2024-01-27 23:39:51'),
        (249, 'default_currency', '$', 'en', '2024-01-27 23:37:07', '2024-01-27 23:39:51'),
        (243, 'currency_postion', 'before', 'en', '2024-01-27 23:37:07', '2024-01-27 23:37:07'),(245, 'address', 'Road-1, Block-B\r\nMirpur-1, Dhaka 1216, Bangladesh', 'en', '2024-01-31 06:21:22', '2024-01-31 06:21:22'),
        (246, 'system_name', 'DeliX', 'bn', '2024-01-31 08:56:34', '2024-01-31 08:56:34'),
        (247, 'deliX', 'deliX', 'bn', '2024-01-31 08:56:34', '2024-01-31 08:56:34');");
        Artisan::call('cache:clear');
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
