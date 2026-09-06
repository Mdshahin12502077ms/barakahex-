<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'id' => 1,
                'title' => 'return_charge',
                'value' => '40',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-07-23 11:22:58',
                'updated_at' => '2021-07-23 11:22:58'
            ],
            [
                'id' => 2,
                'title' => 'fragile_charge',
                'value' => '20',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-07-23 11:22:58',
                'updated_at' => '2024-01-09 13:39:17'
            ],
            [
                'id' => 3,
                'title' => 'pickup_accept_start',
                'value' => '18',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-07-23 11:23:04',
                'updated_at' => '2021-07-23 11:23:04'
            ],
            [
                'id' => 4,
                'title' => 'pickup_accept_end',
                'value' => '24',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-07-23 11:23:04',
                'updated_at' => '2021-07-23 11:23:04'
            ],
            [
                'id' => 5,
                'title' => 'outside_dhaka_days',
                'value' => '8',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-07-23 11:23:04',
                'updated_at' => '2024-01-21 14:54:23'
            ],
            [
                'id' => 6,
                'title' => 'paginate_all_list',
                'value' => '20',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-07-23 21:41:14',
                'updated_at' => '2021-07-23 21:41:14'
            ],
            [
                'id' => 7,
                'title' => 'paginate_parcel_merchant_list',
                'value' => '50',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-07-23 22:11:01',
                'updated_at' => '2021-07-23 22:11:01'
            ],
            [
                'id' => 8,
                'title' => 'paginate_api_list',
                'value' => '15',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-07-23 21:41:14',
                'updated_at' => '2021-07-24 02:56:11'
            ],
            [
                'id' => 11,
                'title' => 'sms_cli',
                'value' => '',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-07-24 00:15:32',
                'updated_at' => '2021-07-24 00:15:32'
            ],
            [
                'id' => 21,
                'title' => 'api_key',
                'value' => 'xxxxxxxxxxx',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-08-04 06:09:26',
                'updated_at' => '2021-08-04 10:54:44'
            ],
            [
                'id' => 22,
                'title' => 'return_charge_dhaka',
                'value' => '50',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-08-04 10:52:57',
                'updated_at' => '2024-01-08 13:22:46'
            ],
            [
                'id' => 23,
                'title' => 'return_charge_sub_city',
                'value' => '90',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-08-04 10:52:57',
                'updated_at' => '2021-09-04 00:55:39'
            ],
            [
                'id' => 24,
                'title' => 'return_charge_outside_dhaka',
                'value' => '120',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-08-04 10:52:57',
                'updated_at' => '2021-09-04 00:55:39'
            ],
            [
                'id' => 25,
                'title' => 'return_charge_type',
                'value' => 'full_delivery_charge',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2021-12-14 23:54:15',
                'updated_at' => '2024-12-11 10:08:31'
            ],
            [
                'id' => 26,
                'title' => 'delivery_otp',
                'value' => 'none',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => '2023-01-29 20:33:07'
            ],
            [
                'id' => 28,
                'title' => 'delivery_otp',
                'value' => 'all',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id' => 29,
                'title' => 'sip_domain',
                'value' => '103.103.35.164:3939',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => '2023-01-18 00:31:49'
            ],
            [
                'id' => 30,
                'title' => 'current_version',
                'value' => '100',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id' => 31,
                'title' => 'update_skipable',
                'value' => 'true',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => '2023-01-18 00:31:27'
            ],
            [
                'id' => 32,
                'title' => 'update_url',
                'value' => '#',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL
            ],
            [
                'id' => 33,
                'title' => 'phone_visible',
                'value' => 'true',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => NULL,
                'updated_at' => '2023-01-18 00:31:27'
            ],
            [
                'id' => 34,
                'title' => 'admin_panel_title',
                'value' => 'Demo Delivery',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 16:58:58',
                'updated_at' => '2024-12-04 11:29:52'
            ],
            [
                'id' => 35,
                'title' => 'system_short_name',
                'value' => 'Delix',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 16:58:58',
                'updated_at' => '2024-12-23 11:52:41'
            ],
            [
                'id' => 36,
                'title' => 'admin_panel_copyright_text',
                'value' => 'Copyright @2024 by SpaGreen Creative',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 16:58:58',
                'updated_at' => '2024-01-27 16:58:58'
            ],
            [
                'id' => 37,
                'title' => 'admin_logo',
                'value' => 'a:6:{s:7:"storage";s:5:"local";s:14:"original_image";s:39:"images/20241223115600-admin_logo444.png";s:11:"image_80x80";s:44:"images/20241223115600-admin_logo-80x8065.png";s:13:"image_500x500";s:0:"";s:12:"image_100x36";s:45:"images/20241223115600-admin_logo-100x3634.png";s:14:"image_1200x630";s:0:"";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 16:58:59',
                'updated_at' => '2024-12-23 11:56:00'
            ],
            [
                'id' => 38,
                'title' => 'admin_mini_logo',
                'value' => 'a:6:{s:7:"storage";s:5:"local";s:14:"original_image";s:44:"images/20241223115641-admin_mini_logo367.png";s:11:"image_80x80";s:50:"images/20241223115641-admin_mini_logo-80x80291.png";s:13:"image_500x500";s:0:"";s:12:"image_100x36";s:50:"images/20241223115641-admin_mini_logo-100x3664.png";s:14:"image_1200x630";s:0:"";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 16:58:59',
                'updated_at' => '2024-12-23 11:56:41'
            ],
            [
                'id' => 39,
                'title' => 'version_code',
                'value' => '1.0.0',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-01-31 01:22:06'
            ],
            [
                'id' => 232,
                'title' => 'system_name',
                'value' => 'Delix',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-11-12 10:15:26'
            ],
            [
                'id' => 233,
                'title' => 'company_name',
                'value' => 'Delix',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-11-12 10:15:26'
            ],
            [
                'id' => 234,
                'title' => 'tagline',
                'value' => 'Parcel Delivery System',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-01-27 17:37:07'
            ],
            [
                'id' => 235,
                'title' => 'phone',
                'value' => '1400620055',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-12-23 13:05:41'
            ],
            [
                'id' => 236,
                'title' => 'phone_country_id',
                'value' => '19',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-12-23 13:05:41'
            ],
            [
                'id' => 237,
                'title' => 'email_address',
                'value' => 'contact@spagreen.net',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-12-23 13:05:41'
            ],
            [
                'id' => 238,
                'title' => 'activation_code',
                'value' => 'xxxxxxxxxxxxxx',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-01-27 17:37:07'
            ],
            [
                'id' => 239,
                'title' => 'time_zone',
                'value' => '195',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-12-19 20:28:50'
            ],
            [
                'id' => 240,
                'title' => 'default_language',
                'value' => 'en',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2025-01-26 07:40:25'
            ],
            [
                'id' => 241,
                'title' => 'default_country',
                'value' => '37',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-12-23 12:19:44'
            ],
            [
                'id' => 242,
                'title' => 'default_weight',
                'value' => 'KG',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-01-27 17:39:51'
            ],
            [
                'id' => 249,
                'title' => 'default_currency',
                'value' => 'R$',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2025-01-26 07:40:25'
            ],
            [
                'id' => 243,
                'title' => 'currency_postion',
                'value' => 'before',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-27 17:37:07',
                'updated_at' => '2024-01-27 17:37:07'
            ],
            [
                'id' => 245,
                'title' => 'address',
                'value' => 'اليمن المكلا مقابل المؤسسة الاقتصادية',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-31 00:21:22',
                'updated_at' => '2024-12-19 17:30:29'
            ],
            [
                'id' => 246,
                'title' => 'system_name',
                'value' => 'DeliX',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-31 02:56:34',
                'updated_at' => '2024-01-31 02:56:34'
            ],
            [
                'id' => 247,
                'title' => 'deliX',
                'value' => 'deliX',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-01-31 02:56:34',
                'updated_at' => '2024-01-31 02:56:34'
            ],
            [
                'id' => 250,
                'title' => 'active_sms_provider',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-23 01:57:02',
                'updated_at' => '2024-12-17 15:40:18'
            ],
            [
                'id' => 251,
                'title' => 'spagreen_sms_api_key',
                'value' => 'd6db374c7a5f8495',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-23 02:08:02',
                'updated_at' => '2024-06-23 02:08:02'
            ],
            [
                'id' => 252,
                'title' => 'spagreen_secret_key',
                'value' => '80498497',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-23 02:08:02',
                'updated_at' => '2024-06-23 02:08:02'
            ],
            [
                'id' => 253,
                'title' => 'spagreen_sms_url',
                'value' => 'http://apismpp.revesms.com/sendtext',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-23 02:08:02',
                'updated_at' => '2024-06-23 02:08:02'
            ],
            [
                'id' => 254,
                'title' => 'spagreen_sender_id',
                'value' => 'SpaGreen',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-23 02:08:02',
                'updated_at' => '2024-06-23 02:08:02'
            ],
            [
                'id' => 255,
                'title' => 'label_sticker',
                'value' => 'default',
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 08:48:26',
                'updated_at' => '2024-11-17 15:51:57'
            ],
            [
                'id' => 256,
                'title' => 'partner_logo_section_title',
                'value' => 'Proudly working with',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:33:46',
                'updated_at' => '2024-06-24 13:33:46'
            ],
            [
                'id' => 257,
                'title' => 'about_section_title',
                'value' => 'Digitl & Trusted Transport Logistic Company.',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:33:46',
                'updated_at' => '2024-06-24 13:33:46'
            ],
            [
                'id' => 258,
                'title' => 'service_section_title',
                'value' => 'Moving Your Products Across All Borders',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:33:46',
                'updated_at' => '2024-06-24 13:33:46'
            ],
            [
                'id' => 259,
                'title' => 'feature_section_title',
                'value' => 'Popular Features Of Logistics Services',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:33:46',
                'updated_at' => '2024-06-24 13:33:46'
            ],
            [
                'id' => 260,
                'title' => 'statistic_section_title',
                'value' => 'Follow Our Growth In Real Time!',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:33:46',
                'updated_at' => '2024-06-24 13:33:46'
            ],
            [
                'id' => 261,
                'title' => 'testimonial_section_title',
                'value' => 'What Says Our Happy Clients',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:33:46',
                'updated_at' => '2024-06-24 13:33:46'
            ],
            [
                'id' => 262,
                'title' => 'about_section_subtitle',
                'value' => 'Our operations are powered by the latest technology, ensuring that we remain at the forefront of the e-commerce industry.',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:33:46',
                'updated_at' => '2024-06-24 13:33:46'
            ],
            [
                'id' => 263,
                'title' => 'is_modal',
                'value' => '0',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:35:03',
                'updated_at' => '2024-06-24 13:35:03'
            ],
            [
                'id' => 264,
                'title' => 'hero_title',
                'value' => 'Tranport Your Goods Around the Worlds.',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:35:03',
                'updated_at' => '2024-06-24 13:35:03'
            ],
            [
                'id' => 265,
                'title' => 'hero_subtitle',
                'value' => 'Track your courier easily and receive it within a few hours with our efficient and safe delivery service.',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:35:03',
                'updated_at' => '2024-06-24 13:35:03'
            ],
            [
                'id' => 266,
                'title' => 'hero_main_action_btn_label',
                'value' => 'Track Parcel',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:35:03',
                'updated_at' => '2024-06-24 13:35:03'
            ],
            [
                'id' => 267,
                'title' => 'header1_hero_image1',
                'value' => 'a:4:{s:7:"storage";s:5:"local";s:14:"original_image";s:56:"images/20241223122401_original_header1_hero_image172.png";s:13:"image_577x505";s:59:"images/20241223122401image_577x505header1_hero_image123.png";s:11:"image_80x80";s:57:"images/20241223122401image_80x80header1_hero_image190.png";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:35:03',
                'updated_at' => '2024-12-23 12:24:01'
            ],
            [
                'id' => 268,
                'title' => 'about_image',
                'value' => 'a:4:{s:7:"storage";s:5:"local";s:14:"original_image";s:49:"images/20240624133927_original_about_image340.jpg";s:13:"image_526X617";s:52:"images/20240624133927image_526X617about_image335.jpg";s:11:"image_80x80";s:50:"images/20240624133927image_80x80about_image498.jpg";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 13:39:28',
                'updated_at' => '2024-06-24 13:39:28'
            ],
            [
                'id' => 269,
                'title' => 'counter1_title',
                'value' => 'Million',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:04:27'
            ],
            [
                'id' => 270,
                'title' => 'counter1_sub_title',
                'value' => 'Parcels Delivered',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:03:34'
            ],
            [
                'id' => 271,
                'title' => 'counter1_value',
                'value' => '10',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:03:20'
            ],
            [
                'id' => 272,
                'title' => 'counter2_title',
                'value' => 'Country',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:04:27'
            ],
            [
                'id' => 273,
                'title' => 'counter2_sub_title',
                'value' => 'Worldwide Coverage',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:04:27'
            ],
            [
                'id' => 274,
                'title' => 'counter2_value',
                'value' => '106',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:03:20'
            ],
            [
                'id' => 275,
                'title' => 'counter3_title',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:03:20'
            ],
            [
                'id' => 276,
                'title' => 'counter3_sub_title',
                'value' => 'Delivery Agents',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:04:27'
            ],
            [
                'id' => 277,
                'title' => 'counter3_value',
                'value' => '1500',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:03:20'
            ],
            [
                'id' => 278,
                'title' => 'counter4_title',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:03:20'
            ],
            [
                'id' => 279,
                'title' => 'counter4_sub_title',
                'value' => 'Registered Merchants',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:04:27'
            ],
            [
                'id' => 280,
                'title' => 'counter4_value',
                'value' => '45000',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:03:20',
                'updated_at' => '2024-06-24 14:03:20'
            ],
            [
                'id' => 281,
                'title' => 'price_title',
                'value' => 'Calculate Pricing',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:05:57',
                'updated_at' => '2024-06-24 14:05:57'
            ],
            [
                'id' => 282,
                'title' => 'price_subtitle',
                'value' => 'Please check out our product calculator for pricing information.',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:05:57',
                'updated_at' => '2024-06-24 14:05:57'
            ],
            [
                'id' => 283,
                'title' => 'price_description',
                'value' => '<ul class="content__list" style="list-style: inherit; color: rgb(33, 37, 41); font-family: Jost, sans-serif;"><li style="margin-bottom: 16px; font-size: 18px; line-height: 28px; letter-spacing: 0.16px; color: rgb(73, 80, 87);">1% cash on delivery (COD) charge applicable</li><li style="margin-bottom: 16px; font-size: 18px; line-height: 28px; letter-spacing: 0.16px; color: rgb(73, 80, 87);">Price Excluding VAT</li><li style="margin-bottom: 16px; font-size: 18px; line-height: 28px; letter-spacing: 0.16px; color: rgb(73, 80, 87);">Price may vary depending on items to be delivered.</li><li style="margin-bottom: 16px; font-size: 18px; line-height: 28px; letter-spacing: 0.16px; color: rgb(73, 80, 87);">Inside Dhaka-48 Hours, Sub Area-48 Hours, Outside Dhaka-72 Hours Delivery after Pickup.  (The timeline may vary based on the remote area, Natural calamities, Unavoidable political issues)</li><li style="margin-bottom: 16px; font-size: 18px; line-height: 28px; letter-spacing: 0.16px; color: rgb(73, 80, 87);">Parcel Live Tracking.</li><li style="margin-bottom: 16px; font-size: 18px; line-height: 28px; letter-spacing: 0.16px; color: rgb(73, 80, 87);">Everyday report generation.</li><li style="font-size: 18px; line-height: 28px; letter-spacing: 0.16px; color: rgb(73, 80, 87);">To ensure safety and security, Large parcels will be delivered via Parceldex pickup van</li></ul>',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:05:57',
                'updated_at' => '2024-07-02 06:45:46'
            ],
            [
                'id' => 284,
                'title' => 'cta_title',
                'value' => 'Small Business Grow Fast With Our Logistic Services',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:08:16',
                'updated_at' => '2024-06-24 14:08:16'
            ],
            [
                'id' => 285,
                'title' => 'cta_main_action_btn_label',
                'value' => 'Become a Merchant',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:08:16',
                'updated_at' => '2024-06-24 14:08:16'
            ],
            [
                'id' => 286,
                'title' => 'cta_main_action_btn_url',
                'value' => 'https://demo.delix.cloud/register',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:08:16',
                'updated_at' => '2024-11-13 11:35:06'
            ],
            [
                'id' => 287,
                'title' => 'cta_enable',
                'value' => '1',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:08:16',
                'updated_at' => '2024-06-24 14:08:16'
            ],
            [
                'id' => 288,
                'title' => 'contact_title',
                'value' => 'Get In Touch With Our Experts.',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:10:11',
                'updated_at' => '2024-06-24 14:10:11'
            ],
            [
                'id' => 289,
                'title' => 'contact_subtitle',
                'value' => 'Drop an email or visit your nearest Trax office and get your queries answered.',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:10:11',
                'updated_at' => '2024-06-24 14:10:11'
            ],
            [
                'id' => 290,
                'title' => 'contact_btn_label',
                'value' => 'Submit Now',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:10:11',
                'updated_at' => '2024-06-24 14:10:11'
            ],
            [
                'id' => 291,
                'title' => 'contact_map',
                'value' => 's:257:"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d58403.65712450906!2d90.36228065!3d23.81046985!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c0c1c61277db%3A0xc7d18838730e2e59!2sMirpur%2C%20Dhaka!5e0!3m2!1sen!2sbd!4v1719231226364!5m2!1sen!2sbd";',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:10:11',
                'updated_at' => '2024-06-24 14:16:05'
            ],
            [
                'id' => 292,
                'title' => 'contact_email',
                'value' => 'contact@spagreen.net',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:11:06',
                'updated_at' => '2024-12-23 12:53:01'
            ],
            [
                'id' => 293,
                'title' => 'contact_phone',
                'value' => '01400-620055',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:11:06',
                'updated_at' => '2024-12-23 12:54:08'
            ],
            [
                'id' => 294,
                'title' => 'high_lighted_text',
                'value' => 'Empower your business with our logistic solutions, your catalyst for growth.',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:11:06',
                'updated_at' => '2024-06-24 14:11:06'
            ],
            [
                'id' => 295,
                'title' => 'show_quick_link',
                'value' => '1',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:12:14',
                'updated_at' => '2024-06-24 14:12:14'
            ],
            [
                'id' => 296,
                'title' => 'quick_link_title',
                'value' => 'Quick Links',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:12:14',
                'updated_at' => '2024-06-24 14:12:25'
            ],
            [
                'id' => 297,
                'title' => 'lang',
                'value' => 'en',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:12:14',
                'updated_at' => '2024-07-02 11:48:29'
            ],
            [
                'id' => 298,
                'title' => 'footer_quick_link_menu',
                'value' => 'a:4:{i:0;a:3:{s:5:"label";s:14:"Track Products";s:3:"url";s:33:"https://demo.delix.cloud/tracking";s:9:"mega_menu";N;}i:1;a:3:{s:5:"label";s:10:"Contact Us";s:3:"url";s:9:"/#contact";s:9:"mega_menu";N;}i:2;a:3:{s:5:"label";s:18:"Terms & Conditions";s:3:"url";s:46:"https://demo.delix.cloud/page/terms-conditions";s:9:"mega_menu";N;}i:3;a:3:{s:5:"label";s:14:"Privacy Policy";s:3:"url";s:44:"https://demo.delix.cloud/page/privacy-policy";s:9:"mega_menu";N;}}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:12:14',
                'updated_at' => '2024-12-23 12:22:46'
            ],
            [
                'id' => 304,
                'title' => 'header_menu',
                'value' => 'a:5:{i:0;a:2:{s:5:"label";s:8:"About Us";s:3:"url";s:7:"/#about";}i:1;a:2:{s:5:"label";s:8:"Tracking";s:3:"url";s:33:"https://demo.delix.cloud/tracking";}i:2;a:2:{s:5:"label";s:5:"Price";s:3:"url";s:9:"/#pricing";}i:3;a:2:{s:5:"label";s:7:"Service";s:3:"url";s:9:"/#service";}i:4;a:2:{s:5:"label";s:10:"Contact Us";s:3:"url";s:9:"/#contact";}}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:17:45',
                'updated_at' => '2024-12-23 12:18:46'
            ],
            [
                'id' => 299,
                'title' => 'show_apps_link',
                'value' => '1',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:12:39',
                'updated_at' => '2024-06-24 14:12:39'
            ],
            [
                'id' => 300,
                'title' => 'play_store_link',
                'value' => 'https://play.google.com/store/apps/details?id=com.delix.merchant',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:12:39',
                'updated_at' => '2024-11-13 11:32:14'
            ],
            [
                'id' => 301,
                'title' => 'app_store_link',
                'value' => 'https://play.google.com/store/apps/details?id=com.delix.rider',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:12:39',
                'updated_at' => '2024-11-13 11:32:45'
            ],
            [
                'id' => 302,
                'title' => 'show_copyright',
                'value' => '1',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:13:02',
                'updated_at' => '2024-06-24 14:13:02'
            ],
            [
                'id' => 303,
                'title' => 'copyright_title',
                'value' => 'Delix | Powered by SpaGreen Creative',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-24 14:13:02',
                'updated_at' => '2024-12-23 12:49:33'
            ],
            [
                'id' => 340,
                'title' => 'header_menu',
                'value' => 'a:5:{i:0;a:2:{s:5:"label";s:31:"আমাদের তথ্য";s:3:"url";s:34:"https://demo.delix.cloud/bn/#about";}i:1;a:2:{s:5:"label";s:27:"ট্র্যাকিং";s:3:"url";s:36:"https://demo.delix.cloud/bn/tracking";}i:2;a:2:{s:5:"label";s:21:"সার্ভিস";s:3:"url";s:36:"https://demo.delix.cloud/bn/#service";}i:3;a:2:{s:5:"label";s:18:"প্রাইস";s:3:"url";s:36:"https://demo.delix.cloud/bn/#pricing";}i:4;a:2:{s:5:"label";s:24:"কন্টাক্ট";s:3:"url";s:36:"https://demo.delix.cloud/bn/#contact";}}',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 11:33:34',
                'updated_at' => '2024-07-02 06:44:08'
            ],
            [
                'id' => 305,
                'title' => 'hero_title',
                'value' => 'আপনার পণ্য সর্বত্র পরিবহন করুন',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 08:24:38',
                'updated_at' => '2024-06-26 12:59:03'
            ],
            [
                'id' => 306,
                'title' => 'hero_subtitle',
                'value' => 'আপনার কুরিয়ার সহজে ট্র্যাক করুন এবং দ্রুততম সময়ে আমাদের দক্ষ এবং নিরাপদ ডেলিভারি পরিষেবা গ্রহণ করুন।',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 08:24:38',
                'updated_at' => '2024-06-26 12:59:59'
            ],
            [
                'id' => 307,
                'title' => 'hero_main_action_btn_label',
                'value' => 'ট্র্যাক পার্সেল',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 08:24:38',
                'updated_at' => '2024-06-25 08:24:38'
            ],
            [
                'id' => 308,
                'title' => 'partner_logo_section_title',
                'value' => 'আমাদের পার্টনার হিসেবে যারা আছে',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 08:29:37',
                'updated_at' => '2024-06-26 13:02:21'
            ],
            [
                'id' => 309,
                'title' => 'about_section_title',
                'value' => 'ডিজিটাল & ট্রাস্টেড ট্রান্সপোর্ট কোম্পানি',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 08:29:37',
                'updated_at' => '2024-06-25 08:36:24'
            ],
            [
                'id' => 310,
                'title' => 'service_section_title',
                'value' => 'আপনার পণ্য সর্বত্র পরিবহন করুন',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 08:29:37',
                'updated_at' => '2024-06-26 13:02:21'
            ],
            [
                'id' => 311,
                'title' => 'feature_section_title',
                'value' => 'আমাদের জনপ্রিয় সেবা সমূহ',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 08:29:37',
                'updated_at' => '2024-07-08 06:33:40'
            ],
            [
                'id' => 312,
                'title' => 'statistic_section_title',
                'value' => 'আমাদের সাফল্যের পরিসংখ্যান',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 08:29:37',
                'updated_at' => '2024-07-08 06:35:03'
            ],
            [
                'id' => 313,
                'title' => 'testimonial_section_title',
                'value' => 'গ্রাহকদের অভিজ্ঞতা',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 08:29:37',
                'updated_at' => '2024-07-08 06:37:50'
            ],
            [
                'id' => 314,
                'title' => 'about_section_subtitle',
                'value' => 'আমাদের কার্যক্রম আধুনিক প্রযুক্তি দ্বারা চালিত হয়, যা নিশ্চিত করে আমাদের ই-কমার্স শিল্পে সফলতা',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 08:29:37',
                'updated_at' => '2024-06-26 13:02:21'
            ],
            [
                'id' => 315,
                'title' => 'is_google_analytics_activated',
                'value' => '0',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 09:53:20',
                'updated_at' => '2024-06-25 11:12:10'
            ],
            [
                'id' => 316,
                'title' => 'tracking_code',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 09:53:20',
                'updated_at' => '2024-06-25 11:12:10'
            ],
            [
                'id' => 317,
                'title' => 'is_recaptcha_activated',
                'value' => '0',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 09:53:20',
                'updated_at' => '2024-11-19 10:34:44'
            ],
            [
                'id' => 318,
                'title' => 'recaptcha_site_key',
                'value' => '6LcY2QAqAAAAALYr2hDqKGVDUdsfNves_XfNHMaJ',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 09:53:20',
                'updated_at' => '2024-06-25 09:53:20'
            ],
            [
                'id' => 319,
                'title' => 'recaptcha_secret',
                'value' => '6LcY2QAqAAAAAN5_rBfjHPI9Me83afzF5msAzMCh',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 09:53:20',
                'updated_at' => '2024-06-25 09:53:20'
            ],
            [
                'id' => 320,
                'title' => 'counter1_title',
                'value' => 'মিলিয়ন',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:30:47',
                'updated_at' => '2024-06-25 10:30:47'
            ],
            [
                'id' => 321,
                'title' => 'counter1_sub_title',
                'value' => 'পার্সেল ডেলিভারি',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:30:47',
                'updated_at' => '2024-06-25 10:30:47'
            ],
            [
                'id' => 322,
                'title' => 'counter2_title',
                'value' => 'দেশে',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:30:47',
                'updated_at' => '2024-06-25 10:30:47'
            ],
            [
                'id' => 323,
                'title' => 'counter2_sub_title',
                'value' => 'বিশ্বব্যাপী কভারেজ',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:30:47',
                'updated_at' => '2024-06-25 10:30:47'
            ],
            [
                'id' => 324,
                'title' => 'counter3_title',
                'value' => NULL,
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:30:47',
                'updated_at' => '2024-06-25 10:30:47'
            ],
            [
                'id' => 325,
                'title' => 'counter3_sub_title',
                'value' => 'ডেলিভারি এজেন্ট',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:30:47',
                'updated_at' => '2024-06-25 10:30:47'
            ],
            [
                'id' => 326,
                'title' => 'counter4_title',
                'value' => NULL,
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:30:47',
                'updated_at' => '2024-06-25 10:30:47'
            ],
            [
                'id' => 327,
                'title' => 'counter4_sub_title',
                'value' => 'রেজিস্টার্ড মার্চেন্ট',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:30:47',
                'updated_at' => '2024-06-25 10:30:47'
            ],
            [
                'id' => 328,
                'title' => 'price_title',
                'value' => 'প্রাইস ক্যালকুলেট',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:42:50',
                'updated_at' => '2024-07-08 06:36:58'
            ],
            [
                'id' => 329,
                'title' => 'price_subtitle',
                'value' => 'মূল্য জানতে আমাদের পণ্যের ক্যালকুলেটরটা চেক করে দেখুন',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:42:50',
                'updated_at' => '2024-06-25 10:42:50'
            ],
            [
                'id' => 330,
                'title' => 'price_description',
                'value' => '<ul><li>১% ক্যাশ অন ডেলিভারি (COD) চার্জ প্রযোজ্য</li><li>ভ্যাট ছাড়া মূল্য</li><li>সরবরাহের পণ্যের উপর নির্ভর করে মূল্য পরিবর্তিত হতে পারে</li><li>ঢাকা শহরে - ৪৮ ঘণ্টা, পার্শ্ববর্তী এলাকা - ৪৮ ঘণ্টা, ঢাকার বাইরে - পিকআপের পর ৭২ ঘণ্টা ডেলিভারি। (দূরবর্তী এলাকা, প্রাকৃতিক দুর্যোগ বা অনিবার্য রাজনৈতিক কারণে সময়সূচি কিছুটা পরিবর্তিত হতে পারে)</li><li>পার্সেল লাইভ ট্র্যাকিং</li><li>প্রতিদিনের রিপোর্ট তৈরি</li><li>নিরাপত্তা ও সুরক্ষা নিশ্চিত করার জন্য, বড় পার্সেলগুলি ডেলিভারি ভ্যানের মাধ্যমে সরবরাহ করা হবে।</li></ul>',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 10:42:50',
                'updated_at' => '2024-07-02 07:02:14'
            ],
            [
                'id' => 387,
                'title' => 'system_name',
                'value' => 'Delix',
                'lang' => 'bra',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2025-05-24 13:01:24',
                'updated_at' => '2025-05-24 13:01:24'
            ],
            [
                'id' => 388,
                'title' => 'company_name',
                'value' => 'Delix',
                'lang' => 'bra',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2025-05-24 13:01:24',
                'updated_at' => '2025-05-24 13:01:24'
            ],
            [
                'id' => 379,
                'title' => 'return_charge_city',
                'value' => NULL,
                'lang' => NULL,
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-12-11 10:08:31',
                'updated_at' => '2024-12-11 10:08:31'
            ],
            [
                'id' => 331,
                'title' => 'contact_title',
                'value' => 'আমাদের এক্সপার্টদের সাথে কথা বলুন',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 11:07:55',
                'updated_at' => '2024-06-25 11:07:55'
            ],
            [
                'id' => 332,
                'title' => 'contact_subtitle',
                'value' => 'ইমেইল করুন অথবা আপনার নিকটস্থ ডেলিভারি অফিসে যান এবং আপনার প্রশ্নের উত্তর জেনে নিন',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 11:07:55',
                'updated_at' => '2024-06-26 13:03:25'
            ],
            [
                'id' => 333,
                'title' => 'contact_btn_label',
                'value' => 'জমা দিন',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 11:07:55',
                'updated_at' => '2024-06-25 11:07:55'
            ],
            [
                'id' => 334,
                'title' => 'contact_map',
                'value' => 's:257:"https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d58403.65712450906!2d90.36228065!3d23.81046985!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755c0c1c61277db%3A0xc7d18838730e2e59!2sMirpur%2C%20Dhaka!5e0!3m2!1sen!2sbd!4v1719231226364!5m2!1sen!2sbd";',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 11:07:55',
                'updated_at' => '2024-06-25 11:07:55'
            ],
            [
                'id' => 335,
                'title' => 'cta_title',
                'value' => 'আমাদের লজিস্টিক সমাধানের সঙ্গে আপনার ব্যবসা যুক্ত করুন',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 11:09:58',
                'updated_at' => '2024-06-26 13:04:00'
            ],
            [
                'id' => 336,
                'title' => 'cta_main_action_btn_label',
                'value' => 'মার্চেন্ট হোন',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 11:09:58',
                'updated_at' => '2024-06-25 11:09:58'
            ],
            [
                'id' => 337,
                'title' => 'contact_email',
                'value' => 'infoswiftship@gmail.com',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 11:29:40',
                'updated_at' => '2024-06-25 11:29:40'
            ],
            [
                'id' => 338,
                'title' => 'contact_phone',
                'value' => '101-555-0127',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 11:29:40',
                'updated_at' => '2024-06-25 11:29:40'
            ],
            [
                'id' => 339,
                'title' => 'high_lighted_text',
                'value' => 'আমাদের লজিস্টিক সমাধানের সঙ্গে যুক্ত হন এবং দ্রুত সাফল্যের অংশীদার হয়ে সাথে থাকুন',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 11:29:40',
                'updated_at' => '2024-06-26 13:06:15'
            ],
            [
                'id' => 350,
                'title' => 'footer_quick_link_menu',
                'value' => 'a:4:{i:0;a:3:{s:5:"label";s:43:"ট্র্যাক পার্সেল";s:3:"url";s:36:"https://demo.delix.cloud/bn/tracking";s:9:"mega_menu";N;}i:1;a:3:{s:5:"label";s:24:"কন্টাক্ট";s:3:"url";s:36:"https://demo.delix.cloud/bn/#contact";s:9:"mega_menu";N;}i:2;a:3:{s:5:"label";s:48:"টার্মস & কন্ডিশন্স";s:3:"url";s:49:"https://demo.delix.cloud/bn/page/terms-conditions";s:9:"mega_menu";N;}i:3;a:3:{s:5:"label";s:43:"প্রাইভেসি পলিসি";s:3:"url";s:47:"https://demo.delix.cloud/bn/page/privacy-policy";s:9:"mega_menu";N;}}',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:42:46',
                'updated_at' => '2024-07-02 07:27:16'
            ],
            [
                'id' => 341,
                'title' => 'company_name',
                'value' => 'DeliX',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:39:01',
                'updated_at' => '2024-06-25 13:39:01'
            ],
            [
                'id' => 342,
                'title' => 'tagline',
                'value' => 'Parcel Delivery System',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:39:01',
                'updated_at' => '2024-06-25 13:39:01'
            ],
            [
                'id' => 343,
                'title' => 'facebook',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:39:01',
                'updated_at' => '2024-06-25 13:39:01'
            ],
            [
                'id' => 344,
                'title' => 'twitter',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:39:01',
                'updated_at' => '2024-06-25 13:39:01'
            ],
            [
                'id' => 345,
                'title' => 'instagram',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:39:01',
                'updated_at' => '2024-06-25 13:39:01'
            ],
            [
                'id' => 346,
                'title' => 'linkedin',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:39:01',
                'updated_at' => '2024-06-25 13:39:01'
            ],
            [
                'id' => 347,
                'title' => 'working_day',
                'value' => 'সোম - শনি / সকাল ৮-১২টা  বৃহ-শনি / সকাল ১১টা - রাত ১০ টা',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:39:01',
                'updated_at' => '2024-06-25 13:39:01'
            ],
            [
                'id' => 348,
                'title' => 'closing_day',
                'value' => NULL,
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:39:01',
                'updated_at' => '2024-06-25 13:39:01'
            ],
            [
                'id' => 349,
                'title' => 'quick_link_title',
                'value' => 'কুইক লিংক',
                'lang' => 'bn',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:42:46',
                'updated_at' => '2024-06-25 13:42:46'
            ],
            [
                'id' => 351,
                'title' => 'working_day',
                'value' => 'Mon - Sat / 08am : 12 pm Thusday - Sat / 11am : 10 pm',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:44:59',
                'updated_at' => '2024-06-25 13:44:59'
            ],
            [
                'id' => 352,
                'title' => 'closing_day',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-06-25 13:44:59',
                'updated_at' => '2024-06-25 13:44:59'
            ],
            [
                'id' => 353,
                'title' => 'admin_favicon',
                'value' => 'a:24:{s:17:"originalImage_url";s:38:"images/favicon/admin-panel/favicon.png";s:15:"image_16x16_url";s:45:"images/favicon/admin-panel/favicon-16x16-.jpg";s:15:"image_32x32_url";s:45:"images/favicon/admin-panel/favicon-32x32-.jpg";s:15:"image_57x57_url";s:45:"images/favicon/admin-panel/favicon-57x57-.jpg";s:15:"image_60x60_url";s:45:"images/favicon/admin-panel/favicon-60x60-.jpg";s:15:"image_72x72_url";s:45:"images/favicon/admin-panel/favicon-72x72-.jpg";s:15:"image_76x76_url";s:45:"images/favicon/admin-panel/favicon-76x76-.jpg";s:15:"image_96x96_url";s:45:"images/favicon/admin-panel/favicon-96x96-.jpg";s:17:"image_114x114_url";s:47:"images/favicon/admin-panel/favicon-114x114-.jpg";s:17:"image_120x120_url";s:47:"images/favicon/admin-panel/favicon-120x120-.jpg";s:17:"image_144x144_url";s:47:"images/favicon/admin-panel/favicon-144x144-.jpg";s:17:"image_152x152_url";s:47:"images/favicon/admin-panel/favicon-152x152-.jpg";s:17:"image_180x180_url";s:47:"images/favicon/admin-panel/favicon-180x180-.jpg";s:17:"image_192x192_url";s:47:"images/favicon/admin-panel/favicon-192x192-.jpg";s:19:"splash_640x1136_url";s:48:"images/favicon/admin-panel/favicon-640x1136-.jpg";s:19:"splash_750x1334_url";s:48:"images/favicon/admin-panel/favicon-750x1334-.jpg";s:20:"splash_1242x2208_url";s:49:"images/favicon/admin-panel/favicon-1242x2208-.jpg";s:20:"splash_1125x2436_url";s:49:"images/favicon/admin-panel/favicon-1125x2436-.jpg";s:19:"splash_828x1792_url";s:48:"images/favicon/admin-panel/favicon-828x1792-.jpg";s:20:"splash_1242x2688_url";s:49:"images/favicon/admin-panel/favicon-1242x2688-.jpg";s:20:"splash_1536x2048_url";s:49:"images/favicon/admin-panel/favicon-1536x2048-.jpg";s:20:"splash_1668x2224_url";s:49:"images/favicon/admin-panel/favicon-1668x2224-.jpg";s:20:"splash_1668x2388_url";s:49:"images/favicon/admin-panel/favicon-1668x2388-.jpg";s:20:"splash_2048x2732_url";s:49:"images/favicon/admin-panel/favicon-2048x2732-.jpg";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-07-02 07:44:15',
                'updated_at' => '2024-11-13 09:48:20'
            ],
            [
                'id' => 354,
                'title' => 'author_name',
                'value' => 'Delix',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-07-02 07:49:18',
                'updated_at' => '2024-07-02 07:49:18'
            ],
            [
                'id' => 355,
                'title' => 'meta_title',
                'value' => 'Delix Website',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-07-02 07:49:18',
                'updated_at' => '2024-07-02 07:49:18'
            ],
            [
                'id' => 356,
                'title' => 'meta_keywords',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-07-02 07:49:18',
                'updated_at' => '2024-07-02 07:49:18'
            ],
            [
                'id' => 357,
                'title' => 'meta_description',
                'value' => 'Meta Description',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-07-02 07:49:18',
                'updated_at' => '2024-07-02 07:51:04'
            ],
            [
                'id' => 358,
                'title' => 'og_title',
                'value' => 'OG Title',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-07-02 07:49:18',
                'updated_at' => '2024-07-02 07:51:04'
            ],
            [
                'id' => 359,
                'title' => 'og_description',
                'value' => 'OG Description',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-07-02 07:49:18',
                'updated_at' => '2024-07-02 07:51:04'
            ],
            [
                'id' => 360,
                'title' => 'meta_image',
                'value' => 'a:6:{s:7:"storage";s:5:"local";s:14:"original_image";s:38:"images/20241113113528-meta_image52.png";s:11:"image_80x80";s:45:"images/20241113113528-meta_image-80x80298.png";s:13:"image_500x500";s:46:"images/20241113113528-meta_image-500x50028.png";s:12:"image_100x36";s:0:"";s:14:"image_1200x630";s:48:"images/20241113113528-meta_image-1200x630421.png";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-07-02 07:51:05',
                'updated_at' => '2024-11-13 11:35:28'
            ],
            [
                'id' => 361,
                'title' => 'og_image',
                'value' => 'a:6:{s:7:"storage";s:5:"local";s:14:"original_image";s:37:"images/20240702081208-og_image372.png";s:11:"image_80x80";s:42:"images/20240702081208-og_image-80x8055.png";s:13:"image_500x500";s:43:"images/20240702081208-og_image-500x5002.png";s:12:"image_100x36";s:0:"";s:14:"image_1200x630";s:46:"images/20240702081208-og_image-1200x630235.png";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-07-02 07:51:05',
                'updated_at' => '2024-07-02 08:12:08'
            ],
            [
                'id' => 380,
                'title' => 'menu_name',
                'value' => 'header_menu',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-12-19 17:35:23',
                'updated_at' => '2024-12-19 17:35:23'
            ],
            [
                'id' => 363,
                'title' => 'mail_server',
                'value' => 'smtp',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-13 09:52:31',
                'updated_at' => '2024-11-13 09:52:31'
            ],
            [
                'id' => 362,
                'title' => 'rider_privacy_policy',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-07-14 16:50:38',
                'updated_at' => '2024-07-14 16:50:38'
            ],
            [
                'id' => 364,
                'title' => 'smtp_server_address',
                'value' => 'smtp.zeptomail.com',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-13 09:52:31',
                'updated_at' => '2024-11-13 09:52:31'
            ],
            [
                'id' => 365,
                'title' => 'smtp_user_name',
                'value' => 'emailapikey',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-13 09:52:31',
                'updated_at' => '2024-11-13 09:52:31'
            ],
            [
                'id' => 366,
                'title' => 'smtp_password',
                'value' => 'wSsVR60k/RH4Cat4zjelIL0/nghRB1/0EUsriVLy6nD5S/GQ9sdpxRCbAgGlFfdJEmZpRTUQou96nh5SgDcOi44qng0EWyiF9mqRe1U4J3x17qnvhDzPW2xbkhWKJIMLwA9immFhE88n+g==',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-13 09:52:31',
                'updated_at' => '2024-11-13 09:52:31'
            ],
            [
                'id' => 367,
                'title' => 'smtp_mail_port',
                'value' => '465',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-13 09:52:31',
                'updated_at' => '2024-11-13 09:52:31'
            ],
            [
                'id' => 368,
                'title' => 'smtp_encryption_type',
                'value' => 'ssl',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-13 09:52:31',
                'updated_at' => '2024-11-13 09:52:31'
            ],
            [
                'id' => 369,
                'title' => 'smtp_mail_from_name',
                'value' => 'Delix',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-13 09:52:31',
                'updated_at' => '2024-11-13 09:52:31'
            ],
            [
                'id' => 370,
                'title' => 'mail_from_address',
                'value' => 'envato@delix.cloud',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-13 09:52:31',
                'updated_at' => '2024-11-13 09:52:31'
            ],
            [
                'id' => 371,
                'title' => 'mail_signature',
                'value' => NULL,
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-13 09:52:31',
                'updated_at' => '2024-11-13 09:52:31'
            ],
            [
                'id' => 372,
                'title' => 'copyright_logo',
                'value' => 'a:10:{s:7:"storage";s:5:"local";s:14:"original_image";s:52:"images/20241113113306_original_copyright_logo121.png";s:11:"image_40x40";s:53:"images/20241113113306image_40x40copyright_logo115.png";s:11:"image_80x80";s:53:"images/20241113113306image_80x80copyright_logo143.png";s:11:"image_68x48";s:53:"images/20241113113306image_68x48copyright_logo486.png";s:13:"image_190x230";s:55:"images/20241113113306image_190x230copyright_logo259.png";s:13:"image_163x116";s:55:"images/20241113113306image_163x116copyright_logo222.png";s:13:"image_295x248";s:55:"images/20241113113306image_295x248copyright_logo194.png";s:13:"image_417x384";s:54:"images/20241113113306image_417x384copyright_logo11.png";s:15:"image_thumbnail";s:57:"images/20241113113306image_thumbnailcopyright_logo123.png";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-13 11:33:06',
                'updated_at' => '2024-11-13 11:33:06'
            ],
            [
                'id' => 373,
                'title' => 'system_name',
                'value' => 'اليمن',
                'lang' => 'ar',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-25 13:28:23',
                'updated_at' => '2024-12-19 17:29:23'
            ],
            [
                'id' => 374,
                'title' => 'company_name',
                'value' => 'مجموعة ابراهيم الاحمدي اليمنية',
                'lang' => 'ar',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-25 13:28:23',
                'updated_at' => '2024-12-19 17:29:23'
            ],
            [
                'id' => 375,
                'title' => 'tagline',
                'value' => 'Parcel Delivery System',
                'lang' => 'ar',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-25 13:28:23',
                'updated_at' => '2024-11-25 13:28:23'
            ],
            [
                'id' => 376,
                'title' => 'working_day',
                'value' => 'Mon - Sat / 08am : 12 pm Thusday - Sat / 11am : 10 pm',
                'lang' => 'ar',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-25 13:28:23',
                'updated_at' => '2024-11-25 13:28:23'
            ],
            [
                'id' => 377,
                'title' => 'closing_day',
                'value' => NULL,
                'lang' => 'ar',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-11-25 13:28:23',
                'updated_at' => '2024-11-25 13:28:23'
            ],
            [
                'id' => 378,
                'title' => 'admin_panel_copyright_text',
                'value' => 'Copyright @2024 by SpaGreen Creative',
                'lang' => 'ar',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-12-02 06:41:39',
                'updated_at' => '2024-12-02 06:41:39'
            ],
            [
                'id' => 381,
                'title' => 'light_logo',
                'value' => 'a:6:{s:7:"storage";s:5:"local";s:14:"original_image";s:38:"images/20241223115835-light_logo16.png";s:11:"image_80x80";s:0:"";s:13:"image_500x500";s:0:"";s:12:"image_100x36";s:0:"";s:14:"image_1200x630";s:0:"";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-12-19 17:35:23',
                'updated_at' => '2024-12-23 11:58:35'
            ],
            [
                'id' => 382,
                'title' => 'dark_logo',
                'value' => 'a:6:{s:7:"storage";s:5:"local";s:14:"original_image";s:38:"images/20241223115835-dark_logo422.png";s:11:"image_80x80";s:0:"";s:13:"image_500x500";s:0:"";s:12:"image_100x36";s:0:"";s:14:"image_1200x630";s:0:"";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-12-19 17:35:23',
                'updated_at' => '2024-12-23 11:58:35'
            ],
            [
                'id' => 383,
                'title' => 'favicon',
                'value' => 'a:24:{s:17:"originalImage_url";s:34:"images/favicon/website/favicon.png";s:15:"image_16x16_url";s:41:"images/favicon/website/favicon-16x16-.jpg";s:15:"image_32x32_url";s:41:"images/favicon/website/favicon-32x32-.jpg";s:15:"image_57x57_url";s:41:"images/favicon/website/favicon-57x57-.jpg";s:15:"image_60x60_url";s:41:"images/favicon/website/favicon-60x60-.jpg";s:15:"image_72x72_url";s:41:"images/favicon/website/favicon-72x72-.jpg";s:15:"image_76x76_url";s:41:"images/favicon/website/favicon-76x76-.jpg";s:15:"image_96x96_url";s:41:"images/favicon/website/favicon-96x96-.jpg";s:17:"image_114x114_url";s:43:"images/favicon/website/favicon-114x114-.jpg";s:17:"image_120x120_url";s:43:"images/favicon/website/favicon-120x120-.jpg";s:17:"image_144x144_url";s:43:"images/favicon/website/favicon-144x144-.jpg";s:17:"image_152x152_url";s:43:"images/favicon/website/favicon-152x152-.jpg";s:17:"image_180x180_url";s:43:"images/favicon/website/favicon-180x180-.jpg";s:17:"image_192x192_url";s:43:"images/favicon/website/favicon-192x192-.jpg";s:19:"splash_640x1136_url";s:44:"images/favicon/website/favicon-640x1136-.jpg";s:19:"splash_750x1334_url";s:44:"images/favicon/website/favicon-750x1334-.jpg";s:20:"splash_1242x2208_url";s:45:"images/favicon/website/favicon-1242x2208-.jpg";s:20:"splash_1125x2436_url";s:45:"images/favicon/website/favicon-1125x2436-.jpg";s:19:"splash_828x1792_url";s:44:"images/favicon/website/favicon-828x1792-.jpg";s:20:"splash_1242x2688_url";s:45:"images/favicon/website/favicon-1242x2688-.jpg";s:20:"splash_1536x2048_url";s:45:"images/favicon/website/favicon-1536x2048-.jpg";s:20:"splash_1668x2224_url";s:45:"images/favicon/website/favicon-1668x2224-.jpg";s:20:"splash_1668x2388_url";s:45:"images/favicon/website/favicon-1668x2388-.jpg";s:20:"splash_2048x2732_url";s:45:"images/favicon/website/favicon-2048x2732-.jpg";}',
                'lang' => 'en',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-12-19 17:35:26',
                'updated_at' => '2024-12-23 11:58:37'
            ],
            [
                'id' => 384,
                'title' => 'hero_title',
                'value' => 'Transport Your Goods Around the Worlds.',
                'lang' => 'ar',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-12-19 17:36:47',
                'updated_at' => '2024-12-19 17:36:47'
            ],
            [
                'id' => 385,
                'title' => 'hero_subtitle',
                'value' => 'Track your courier easily and receive it within a few hours with our efficient and safe delivery service.',
                'lang' => 'ar',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-12-19 17:36:47',
                'updated_at' => '2024-12-19 17:36:47'
            ],
            [
                'id' => 386,
                'title' => 'hero_main_action_btn_label',
                'value' => 'Track Parcel',
                'lang' => 'ar',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2024-12-19 17:36:47',
                'updated_at' => '2024-12-19 17:36:47'
            ],
            [
                'id' => 389,
                'title' => 'tagline',
                'value' => 'Parcel Delivery System',
                'lang' => 'bra',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2025-05-24 13:01:24',
                'updated_at' => '2025-05-24 13:01:24'
            ],
            [
                'id' => 390,
                'title' => 'working_day',
                'value' => 'Mon - Sat / 08am : 12 pm Thusday - Sat / 11am : 10 pm',
                'lang' => 'bra',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2025-05-24 13:01:24',
                'updated_at' => '2025-05-24 13:01:24'
            ],
            [
                'id' => 391,
                'title' => 'closing_day',
                'value' => NULL,
                'lang' => 'bra',
                'created_by' => NULL,
                'updated_by' => NULL,
                'created_at' => '2025-05-24 13:01:24',
                'updated_at' => '2025-05-24 13:01:24'
            ]
        ];

        foreach ($settings as $setting) {
            // Use updateOrInsert to avoid duplicate key errors
            DB::table('settings')->updateOrInsert(
                ['id' => $setting['id']], // Search condition
                $setting // Data to insert/update
            );
        }

        $services = [
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134138_original_service_image120.jpg","image_358X270":"images\/20240624134138image_358X270service_image492.jpg","image_80x80":"images\/20240624134138image_80x80service_image119.jpg"}',
                'title' => 'ডকুমেন্ট ডেলিভারি',
                'description' => 'ডেলিক্স একটি নিরাপদ এবং ডিজিটাইজড প্রক্রিয়া প্রদান করে যেখানে সিম, ডেবিট এবং ক্রেডিট কার্ড সহ অত্যন্ত সংহ্রাদপূর্ণ পাঠানো হয়।',
                'status' => 1,
                'created_at' => '2024-06-24 13:41:39',
                'updated_at' => '2024-06-25 10:27:35'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134204_original_service_image369.jpg","image_358X270":"images\/20240624134204image_358X270service_image232.jpg","image_80x80":"images\/20240624134204image_80x80service_image61.jpg"}',
                'title' => 'বাল্ক মুভমেন্ট',
                'description' => 'আমরা ভারি পণ্য শিপমেন্ট বিশ্বব্যাপী করে থাকি, যা আপনাকে সহায়তা করবে',
                'status' => 1,
                'created_at' => '2024-06-24 13:42:04',
                'updated_at' => '2024-06-25 10:27:20'
            ],
            // ... include the rest without 'id' field
        ];

        foreach ($services as $service) {
            DB::table('website_services')->insert($service);
        }

        $serviceLanguages = [
            [
                'website_service_id' => 1,
                'lang' => 'en',
                'title' => 'Document Deliveries',
                'description' => 'Trax offers a secure and digitized process to send highly sensitive shipments like SIMs, debit and credit cards.',
                'created_at' => '2024-06-24 13:41:39',
                'updated_at' => '2024-06-24 13:41:39'
            ],
            [
                'website_service_id' => 2,
                'lang' => 'en',
                'title' => 'Bulk Movements',
                'description' => 'We can help you with moving heavy shipments across USA, which will take the weight off your shoulders.',
                'created_at' => '2024-06-24 13:42:04',
                'updated_at' => '2024-06-24 13:42:04'
            ],
            [
                'website_service_id' => 3,
                'lang' => 'en',
                'title' => 'International Logistics',
                'description' => 'Our global services span across 232 locations, enabling your business to reach new heights and expand your products worldwide.',
                'created_at' => '2024-06-24 13:42:26',
                'updated_at' => '2024-06-24 13:42:26'
            ],
            [
                'website_service_id' => 4,
                'lang' => 'en',
                'title' => 'Warehousing & Fulfillment',
                'description' => 'We provide a digital solution for managing inventory that includes pick-up, packing, and dispatch.',
                'created_at' => '2024-06-24 13:42:54',
                'updated_at' => '2024-06-24 13:42:54'
            ],
            [
                'website_service_id' => 5,
                'lang' => 'en',
                'title' => 'MOVIT/Moving & Packing',
                'description' => 'Our team of experts has the necessary knowledge to ensure that all your belongings are packed and transported safely to your new home or office.',
                'created_at' => '2024-06-24 13:43:29',
                'updated_at' => '2024-06-24 13:43:29'
            ],
            [
                'website_service_id' => 6,
                'lang' => 'en',
                'title' => 'E-Commerce Logistics',
                'description' => 'Managing eCommerce logistics in-house involves overseeing the supply chain, tracking shipments from the supplier to the warehouse',
                'created_at' => '2024-06-24 13:43:53',
                'updated_at' => '2024-06-24 13:43:53'
            ],
            [
                'website_service_id' => 6,
                'lang' => 'bn',
                'title' => 'ই-কমার্স লজিস্টিক্স',
                'description' => 'ই-কমার্স লজিস্টিক্স নিজেরাই পরিচালনা করা , সাপ্লাই চেইন তত্ত্বাবধান করা, সরবরাহকারী থেকে ওয়ারহাউজ পর্যন্ত চালান ট্র্যাক করা',
                'created_at' => '2024-06-25 08:58:03',
                'updated_at' => '2024-06-25 08:58:03'
            ],
            [
                'website_service_id' => 5,
                'lang' => 'bn',
                'title' => 'পরিবহন ও প্যাকিং',
                'description' => 'আমাদের বিশেষজ্ঞ দলের প্রয়োজনীয় জ্ঞান রয়েছে যাতে আপনার সমস্ত জিনিসপত্র নিরাপদে আপনার নতুন বাড়ি বা অফিসে প্যাক এবং পরিবহন করা হয়।',
                'created_at' => '2024-06-25 09:03:02',
                'updated_at' => '2024-06-25 09:03:02'
            ],
            [
                'website_service_id' => 4,
                'lang' => 'bn',
                'title' => 'ওয়ারহাউজ সংরক্ষণ',
                'description' => 'আমরা সংগ্রহ, প্যাকিং, এবং প্রেরণসহ ইনভেন্টরি ব্যবস্থাপনার জন্য একটি ডিজিটাল সমাধান প্রদান করি।',
                'created_at' => '2024-06-25 10:26:13',
                'updated_at' => '2024-06-25 10:26:13'
            ],
            [
                'website_service_id' => 3,
                'lang' => 'bn',
                'title' => 'আন্তর্জাতিক লজিস্টিক্স',
                'description' => 'আমাদের বিশ্বব্যাপী সেবাগুলি ২৩২ স্থানে ছড়িয়ে পড়ে, যা আপনার ব্যবসাকে নতুন উচ্চারণে পৌঁছানো এবং আপনার পণ্যগুলি বিশ্বব্যাপী প্রসার করতে সহায়ক।',
                'created_at' => '2024-06-25 10:26:41',
                'updated_at' => '2024-06-25 10:26:41'
            ],
            [
                'website_service_id' => 2,
                'lang' => 'bn',
                'title' => 'বাল্ক মুভমেন্ট',
                'description' => 'আমরা ভারি পণ্য শিপমেন্ট বিশ্বব্যাপী করে থাকি, যা আপনাকে সহায়তা করবে',
                'created_at' => '2024-06-25 10:27:20',
                'updated_at' => '2024-06-25 10:27:20'
            ],
            [
                'website_service_id' => 1,
                'lang' => 'bn',
                'title' => 'ডকুমেন্ট ডেলিভারি',
                'description' => 'ডেলিক্স একটি নিরাপদ এবং ডিজিটাইজড প্রক্রিয়া প্রদান করে যেখানে সিম, ডেবিট এবং ক্রেডিট কার্ড সহ অত্যন্ত সংহ্রাদপূর্ণ পাঠানো হয়।',
                'created_at' => '2024-06-25 10:27:35',
                'updated_at' => '2024-06-25 10:27:35'
            ]
        ];

        foreach ($serviceLanguages as $language) {
            DB::table('website_service_languages')->updateOrInsert(
                [
                    'website_service_id' => $language['website_service_id'],
                    'lang' => $language['lang']
                ],
                $language
            );
        }

        $testimonials = [
            [
                'name' => 'শর্মিলা ইসলাম',
                'title' => 'চমৎকার সেবা এবং দুর্দান্ত সমর্থন!',
                'designation' => 'ই-কমার্স উদ্যোক্তা',
                'description' => '“একজন পরিবহন সমন্বয়কারী হিসেবে, আমি সুইফ্টশিপের রিয়েল-টাইম সুবিধাগুলির সরলতা এবং কার্যকারিতা পছন্দ করি। গাড়িবহর পরিচালনা, রুট পর্যবেক্ষণ এবং সমস্যাগুলি প্রাক-সক্রিয়ভাবে মোকাবেলা করা - সবই এক জায়গায়। এটি লজিস্টিক ক্ষেত্রের যে কারো জন্যই একটা গেম চেঞ্জার"',
                'media_id' => NULL,
                'image' => '{"storage":"local","original_image":"images\/20240624142304_original_testimonial_image420.png","image_96X96":"images\/20240624142304image_96X96testimonial_image34.png","image_80x80":"images\/20240624142304image_80x80testimonial_image50.png"}',
                'rating' => '5',
                'status' => 1,
                'created_at' => '2024-06-24 14:23:05',
                'updated_at' => '2024-06-25 11:04:26'
            ],
            [
                'name' => 'হাসান মাহমুদ',
                'title' => 'চমৎকার সেবা এবং দুর্দান্ত সমর্থন!',
                'designation' => 'ই-কমার্স উদ্যোক্তা',
                'description' => '“একজন পরিবহন সমন্বয়কারী হিসেবে, আমি সুইফ্টশিপের রিয়েল-টাইম সুবিধাগুলির সরলতা এবং কার্যকারিতা পছন্দ করি। গাড়িবহর পরিচালনা, রুট পর্যবেক্ষণ এবং সমস্যাগুলি প্রাক-সক্রিয়ভাবে মোকাবেলা করা - সবই এক জায়গায়। এটি লজিস্টিক ক্ষেত্রের যে কারো জন্যই একটা গেম চেঞ্জার"',
                'media_id' => NULL,
                'image' => '{"storage":"local","original_image":"images\/20240624142415_original_testimonial_image174.png","image_96X96":"images\/20240624142415image_96X96testimonial_image38.png","image_80x80":"images\/20240624142415image_80x80testimonial_image12.png"}',
                'rating' => '5',
                'status' => 1,
                'created_at' => '2024-06-24 14:24:15',
                'updated_at' => '2024-06-25 11:00:51'
            ]
        ];

        foreach ($testimonials as $testimonial) {
            DB::table('website_testimonials')->updateOrInsert(
                [
                    'name' => $testimonial['name'],
                    'title' => $testimonial['title']
                ],
                $testimonial
            );
        }

        $aboutLanguages = [
            [
                'website_about_id' => 1,
                'lang' => 'en',
                'title' => 'Global Service',
                'description' => 'Tellus in hac habitasse platea dictumst vestibulum. Facilisi cras fermentum odio eu feugiat.',
                'created_at' => '2024-06-24 13:40:09',
                'updated_at' => '2024-06-24 14:09:20'
            ],
            [
                'website_about_id' => 2,
                'lang' => 'en',
                'title' => 'Domestic Service',
                'description' => 'Local logistics services" is a clear phrase, but we can make it more concise',
                'created_at' => '2024-06-24 13:40:34',
                'updated_at' => '2024-06-24 14:09:13'
            ],
            [
                'website_about_id' => 3,
                'lang' => 'en',
                'title' => 'Local Service',
                'description' => 'Local logistics services" is a clear phrase, but we can make it more concise.',
                'created_at' => '2024-06-24 13:40:54',
                'updated_at' => '2024-06-24 14:09:03'
            ],
            [
                'website_about_id' => 3,
                'lang' => 'bn',
                'title' => 'স্থানীয় সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য স্থানীয় লজিস্টিক্স সেবা',
                'created_at' => '2024-06-25 08:33:12',
                'updated_at' => '2024-06-25 08:33:12'
            ],
            [
                'website_about_id' => 2,
                'lang' => 'bn',
                'title' => 'দেশীয় সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য কানেক্টিভিটি সার্ভিস',
                'created_at' => '2024-06-25 08:34:10',
                'updated_at' => '2024-06-25 08:35:02'
            ],
            [
                'website_about_id' => 1,
                'lang' => 'bn',
                'title' => 'বৈশ্বিক সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য লজিস্টিক্স সেবা',
                'created_at' => '2024-06-25 08:35:46',
                'updated_at' => '2024-06-25 08:35:46'
            ]
        ];

        foreach ($aboutLanguages as $language) {
            DB::table('website_about_languages')->updateOrInsert(
                [
                    'website_about_id' => $language['website_about_id'],
                    'lang' => $language['lang']
                ],
                $language
            );
        }

        $testimonialLanguages = [
            [
                'testimonial_id' => 1,
                'lang' => 'en',
                'name' => 'Hilary Ouse',
                'title' => 'Fantastic service in and amazing support!',
                'designation' => 'E-commerce Entrepreneur',
                'description' => '“As a transportation coordinator, I appreciate the simplicity and power of SwiftShip\'s real-time features. Managing fleets, monitoring routes, and addressing issues proactively – all in one place. It\'s a game-changer for anyone in the logistics field.’’',
                'created_at' => '2024-06-24 14:23:05',
                'updated_at' => '2024-06-24 14:23:10'
            ],
            [
                'testimonial_id' => 2,
                'lang' => 'en',
                'name' => 'Hilary Ouse',
                'title' => 'Fantastic service in and amazing support!',
                'designation' => 'E-commerce Entrepreneur',
                'description' => '“As a transportation coordinator, I appreciate the simplicity and power of SwiftShip\'s real-time features. Managing fleets, monitoring routes, and addressing issues proactively – all in one place. It\'s a game-changer for anyone in the logistics field.’’',
                'created_at' => '2024-06-24 14:24:15',
                'updated_at' => '2024-06-24 14:24:15'
            ],
            [
                'testimonial_id' => 2,
                'lang' => 'bn',
                'name' => 'হাসান মাহমুদ',
                'title' => 'চমৎকার সেবা এবং দুর্দান্ত সমর্থন!',
                'designation' => 'ই-কমার্স উদ্যোক্তা',
                'description' => '“একজন পরিবহন সমন্বয়কারী হিসেবে, আমি সুইফ্টশিপের রিয়েল-টাইম সুবিধাগুলির সরলতা এবং কার্যকারিতা পছন্দ করি। গাড়িবহর পরিচালনা, রুট পর্যবেক্ষণ এবং সমস্যাগুলি প্রাক-সক্রিয়ভাবে মোকাবেলা করা - সবই এক জায়গায়। এটি লজিস্টিক ক্ষেত্রের যে কারো জন্যই একটা গেম চেঞ্জার"',
                'created_at' => '2024-06-25 11:00:51',
                'updated_at' => '2024-06-25 11:00:51'
            ],
            [
                'testimonial_id' => 1,
                'lang' => 'bn',
                'name' => 'শর্মিলা ইসলাম',
                'title' => 'চমৎকার সেবা এবং দুর্দান্ত সমর্থন!',
                'designation' => 'ই-কমার্স উদ্যোক্তা',
                'description' => '“একজন পরিবহন সমন্বয়কারী হিসেবে, আমি সুইফ্টশিপের রিয়েল-টাইম সুবিধাগুলির সরলতা এবং কার্যকারিতা পছন্দ করি। গাড়িবহর পরিচালনা, রুট পর্যবেক্ষণ এবং সমস্যাগুলি প্রাক-সক্রিয়ভাবে মোকাবেলা করা - সবই এক জায়গায়। এটি লজিস্টিক ক্ষেত্রের যে কারো জন্যই একটা গেম চেঞ্জার"',
                'created_at' => '2024-06-25 11:04:26',
                'updated_at' => '2024-10-09 15:18:24'
            ]
        ];

        foreach ($testimonialLanguages as $language) {
            DB::table('testimonial_languages')->updateOrInsert(
                [
                    'testimonial_id' => $language['testimonial_id'],
                    'lang' => $language['lang']
                ],
                $language
            );
        }

        $partnerLogos = [
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122510_original_partner_logo132.png","image_80X31":"images\/20241223122510image_80X31partner_logo388.png","image_80x80":"images\/20241223122510image_80x80partner_logo58.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:10',
                'updated_at' => '2024-12-23 12:25:10'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122516_original_partner_logo339.png","image_80X31":"images\/20241223122516image_80X31partner_logo455.png","image_80x80":"images\/20241223122516image_80x80partner_logo68.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:16',
                'updated_at' => '2024-12-23 12:25:16'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122522_original_partner_logo51.png","image_80X31":"images\/20241223122522image_80X31partner_logo37.png","image_80x80":"images\/20241223122522image_80x80partner_logo295.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:22',
                'updated_at' => '2024-12-23 12:25:22'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122524_original_partner_logo221.png","image_80X31":"images\/20241223122534image_80X31partner_logo428.png","image_80x80":"images\/20241223122534image_80x80partner_logo121.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:24',
                'updated_at' => '2024-12-23 12:25:34'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122542_original_partner_logo5.png","image_80X31":"images\/20241223122542image_80X31partner_logo285.png","image_80x80":"images\/20241223122542image_80x80partner_logo421.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:42',
                'updated_at' => '2024-12-23 12:25:42'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122550_original_partner_logo106.png","image_80X31":"images\/20241223122550image_80X31partner_logo174.png","image_80x80":"images\/20241223122550image_80x80partner_logo41.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:25:50',
                'updated_at' => '2024-12-23 12:25:50'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20241223122600_original_partner_logo63.png","image_80X31":"images\/20241223122600image_80X31partner_logo273.png","image_80x80":"images\/20241223122600image_80x80partner_logo59.png"}',
                'status' => '1',
                'created_at' => '2024-12-23 12:26:00',
                'updated_at' => '2024-12-23 12:26:00'
            ]
        ];

        foreach ($partnerLogos as $logo) {
            DB::table('website_partner_logos')->updateOrInsert(
                [
                    'image' => $logo['image']
                ],
                $logo
            );
        }

        $newsAndEventLanguages = [
            [
                'website_news_and_event_id' => 1,
                'lang' => 'en',
                'title' => 'Tech-Ops',
                'description' => 'Our operations are fully transparent and designed to be tech-friendly.',
                'created_at' => '2024-06-24 13:37:16',
                'updated_at' => '2024-06-24 13:37:16'
            ],
            [
                'website_news_and_event_id' => 2,
                'lang' => 'en',
                'title' => 'Verified Returns',
                'description' => 'We will confirm the status of each of your returns through Return Confirmation.',
                'created_at' => '2024-06-24 13:37:54',
                'updated_at' => '2024-06-24 13:37:54'
            ],
            [
                'website_news_and_event_id' => 3,
                'lang' => 'en',
                'title' => 'Live Tracking & Support',
                'description' => 'Keep track of your parcels in real-time as they move between cities, no matter where you are.',
                'created_at' => '2024-06-24 13:38:23',
                'updated_at' => '2024-06-24 13:38:23'
            ],
            [
                'website_news_and_event_id' => 4,
                'lang' => 'en',
                'title' => 'Daily Payment Processing',
                'description' => 'Receive your payments within 24 hours or less.',
                'created_at' => '2024-06-24 13:38:50',
                'updated_at' => '2024-06-24 13:38:50'
            ],
            [
                'website_news_and_event_id' => 4,
                'lang' => 'bn',
                'title' => 'ডেইলি পেমেন্ট প্রসেসিং',
                'description' => '২৪ ঘণ্টার মধ্যে বা তার কম সময়ে আপনার পেমেন্ট গ্রহণ করুন।',
                'created_at' => '2024-06-25 08:30:42',
                'updated_at' => '2024-06-25 08:30:42'
            ],
            [
                'website_news_and_event_id' => 3,
                'lang' => 'bn',
                'title' => 'লাইভ ট্র্যাকিং & সাপোর্ট',
                'description' => 'আপনার পার্সেলগুলো এক শহর থেকে অন্য শহরে চলার সময় রিয়েল-টাইমে ট্র্যাক করুন, আপনি যেখানেই থাকুন না কেন।',
                'created_at' => '2024-06-25 08:31:16',
                'updated_at' => '2024-06-25 08:31:16'
            ],
            [
                'website_news_and_event_id' => 2,
                'lang' => 'bn',
                'title' => 'অনুমোদিত রিটার্ন পলিসি',
                'description' => 'আমরা প্রত্যাবর্তন নিশ্চিতকরণের মাধ্যমে আপনার প্রত্যাবর্তনের প্রতিটি অবস্থান নিশ্চিত করব',
                'created_at' => '2024-06-25 08:31:35',
                'updated_at' => '2024-06-25 08:31:35'
            ],
            [
                'website_news_and_event_id' => 1,
                'lang' => 'bn',
                'title' => 'টেক অপারেশন',
                'description' => 'আমাদের কার্যক্রম সম্পূর্ণ স্বচ্ছ এবং প্রযুক্তি-বান্ধবভাবে ডিজাইন করা হয়েছে।',
                'created_at' => '2024-06-25 08:31:57',
                'updated_at' => '2024-06-25 08:31:57'
            ]
        ];

        foreach ($newsAndEventLanguages as $language) {
            DB::table('website_news_and_event_languages')->updateOrInsert(
                [
                    'website_news_and_event_id' => $language['website_news_and_event_id'],
                    'lang' => $language['lang']
                ],
                $language
            );
        }

        $newsAndEvents = [
            [
                'image' => '{"storage":"local","original_image":"images\/20240624133716_original_news_event_image317.jpg","image_280X190":"images\/20240624133716image_280X190news_event_image233.jpg","image_80x80":"images\/20240624133716image_80x80news_event_image41.jpg"}',
                'title' => 'টেক অপারেশন',
                'description' => 'আমাদের কার্যক্রম সম্পূর্ণ স্বচ্ছ এবং প্রযুক্তি-বান্ধবভাবে ডিজাইন করা হয়েছে।',
                'status' => 1,
                'created_at' => '2024-06-24 13:37:16',
                'updated_at' => '2024-06-25 08:31:57'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624133754_original_news_event_image363.jpg","image_280X190":"images\/20240624133754image_280X190news_event_image483.jpg","image_80x80":"images\/20240624133754image_80x80news_event_image470.jpg"}',
                'title' => 'অনুমোদিত রিটার্ন পলিসি',
                'description' => 'আমরা প্রত্যাবর্তন নিশ্চিতকরণের মাধ্যমে আপনার প্রত্যাবর্তনের প্রতিটি অবস্থান নিশ্চিত করব',
                'status' => 1,
                'created_at' => '2024-06-24 13:37:54',
                'updated_at' => '2024-06-25 08:31:35'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624133823_original_news_event_image408.jpg","image_280X190":"images\/20240624133823image_280X190news_event_image291.jpg","image_80x80":"images\/20240624133823image_80x80news_event_image210.jpg"}',
                'title' => 'লাইভ ট্র্যাকিং & সাপোর্ট',
                'description' => 'আপনার পার্সেলগুলো এক শহর থেকে অন্য শহরে চলার সময় রিয়েল-টাইমে ট্র্যাক করুন, আপনি যেখানেই থাকুন না কেন।',
                'status' => 1,
                'created_at' => '2024-06-24 13:38:23',
                'updated_at' => '2024-06-25 08:31:16'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624133850_original_news_event_image48.jpg","image_280X190":"images\/20240624133850image_280X190news_event_image299.jpg","image_80x80":"images\/20240624133850image_80x80news_event_image7.jpg"}',
                'title' => 'ডেইলি পেমেন্ট প্রসেসিং',
                'description' => '২৪ ঘণ্টার মধ্যে বা তার কম সময়ে আপনার পেমেন্ট গ্রহণ করুন।',
                'status' => 1,
                'created_at' => '2024-06-24 13:38:50',
                'updated_at' => '2024-06-25 08:30:42'
            ]
        ];

        foreach ($newsAndEvents as $event) {
            DB::table('website_news_and_events')->updateOrInsert(
                [
                    'title' => $event['title']
                ],
                $event
            );
        }

        $abouts = [
            [
                'icon' => '{"storage":"local","original_image":"images\/20240624140920_original_about_icon207.png","image_44X44":"images\/20240624140920image_44X44about_icon14.png","image_80x80":"images\/20240624140920image_80x80about_icon13.png"}',
                'title' => 'বৈশ্বিক সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য লজিস্টিক্স সেবা',
                'status' => 1,
                'created_at' => '2024-06-24 13:40:09',
                'updated_at' => '2024-06-25 08:35:46'
            ],
            [
                'icon' => '{"storage":"local","original_image":"images\/20240624140913_original_about_icon66.png","image_44X44":"images\/20240624140913image_44X44about_icon327.png","image_80x80":"images\/20240624140913image_80x80about_icon254.png"}',
                'title' => 'দেশীয় সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য কানেক্টিভিটি সার্ভিস',
                'status' => 1,
                'created_at' => '2024-06-24 13:40:34',
                'updated_at' => '2024-06-25 08:35:02'
            ],
            [
                'icon' => '{"storage":"local","original_image":"images\/20240624140903_original_about_icon411.png","image_44X44":"images\/20240624140903image_44X44about_icon418.png","image_80x80":"images\/20240624140903image_80x80about_icon47.png"}',
                'title' => 'স্থানীয় সেবা',
                'description' => 'সমস্ত অঞ্চল এবং দেশের জন্য স্থানীয় লজিস্টিক্স সেবা',
                'status' => 1,
                'created_at' => '2024-06-24 13:40:54',
                'updated_at' => '2024-06-25 08:33:12'
            ]
        ];

        foreach ($abouts as $about) {
            DB::table('website_abouts')->updateOrInsert(
                [
                    'title' => $about['title']
                ],
                $about
            );
        }

        $services = [
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134138_original_service_image120.jpg","image_358X270":"images\/20240624134138image_358X270service_image492.jpg","image_80x80":"images\/20240624134138image_80x80service_image119.jpg"}',
                'title' => 'ডকুমেন্ট ডেলিভারি',
                'description' => 'ডেলিক্স একটি নিরাপদ এবং ডিজিটাইজড প্রক্রিয়া প্রদান করে যেখানে সিম, ডেবিট এবং ক্রেডিট কার্ড সহ অত্যন্ত সংহ্রাদপূর্ণ পাঠানো হয়।',
                'status' => 1,
                'created_at' => '2024-06-24 13:41:39',
                'updated_at' => '2024-06-25 10:27:35'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134204_original_service_image369.jpg","image_358X270":"images\/20240624134204image_358X270service_image232.jpg","image_80x80":"images\/20240624134204image_80x80service_image61.jpg"}',
                'title' => 'বাল্ক মুভমেন্ট',
                'description' => 'আমরা ভারি পণ্য শিপমেন্ট বিশ্বব্যাপী করে থাকি, যা আপনাকে সহায়তা করবে',
                'status' => 1,
                'created_at' => '2024-06-24 13:42:04',
                'updated_at' => '2024-06-25 10:27:20'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134226_original_service_image399.jpg","image_358X270":"images\/20240624134226image_358X270service_image208.jpg","image_80x80":"images\/20240624134226image_80x80service_image58.jpg"}',
                'title' => 'আন্তর্জাতিক লজিস্টিক্স',
                'description' => 'আমাদের বিশ্বব্যাপী সেবাগুলি ২৩২ স্থানে ছড়িয়ে পড়ে, যা আপনার ব্যবসাকে নতুন উচ্চারণে পৌঁছানো এবং আপনার পণ্যগুলি বিশ্বব্যাপী প্রসার করতে সহায়ক।',
                'status' => 1,
                'created_at' => '2024-06-24 13:42:26',
                'updated_at' => '2024-06-25 10:26:41'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134254_original_service_image388.jpg","image_358X270":"images\/20240624134254image_358X270service_image372.jpg","image_80x80":"images\/20240624134254image_80x80service_image33.jpg"}',
                'title' => 'ওয়ারহাউজ সংরক্ষণ',
                'description' => 'আমরা সংগ্রহ, প্যাকিং, এবং প্রেরণসহ ইনভেন্টরি ব্যবস্থাপনার জন্য একটি ডিজিটাল সমাধান প্রদান করি।',
                'status' => 1,
                'created_at' => '2024-06-24 13:42:54',
                'updated_at' => '2024-06-25 10:26:13'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134329_original_service_image358.jpg","image_358X270":"images\/20240624134329image_358X270service_image31.jpg","image_80x80":"images\/20240624134329image_80x80service_image287.jpg"}',
                'title' => 'পরিবহন ও প্যাকিং',
                'description' => 'আমাদের বিশেষজ্ঞ দলের প্রয়োজনীয় জ্ঞান রয়েছে যাতে আপনার সমস্ত জিনিসপত্র নিরাপদে আপনার নতুন বাড়ি বা অফিসে প্যাক এবং পরিবহন করা হয়।',
                'status' => 1,
                'created_at' => '2024-06-24 13:43:29',
                'updated_at' => '2024-06-25 09:03:02'
            ],
            [
                'image' => '{"storage":"local","original_image":"images\/20240624134353_original_service_image449.jpg","image_358X270":"images\/20240624134353image_358X270service_image107.jpg","image_80x80":"images\/20240624134353image_80x80service_image347.jpg"}',
                'title' => 'ই-কমার্স লজিস্টিক্স',
                'description' => 'ই-কমার্স লজিস্টিক্স নিজেরাই পরিচালনা করা , সাপ্লাই চেইন তত্ত্বাবধান করা, সরবরাহকারী থেকে ওয়ারহাউজ পর্যন্ত চালান ট্র্যাক করা',
                'status' => 1,
                'created_at' => '2024-06-24 13:43:53',
                'updated_at' => '2024-06-25 08:58:03'
            ]
        ];

        foreach ($services as $service) {
            DB::table('website_services')->updateOrInsert(
                [
                    'title' => $service['title']
                ],
                $service
            );
        }

        // First, get the service IDs that were inserted
        $services = DB::table('website_services')->get()->keyBy('title');
        
        $serviceLanguages = [
            [
                'service_title' => 'ডকুমেন্ট ডেলিভারি',
                'lang' => 'en',
                'title' => 'Document Deliveries',
                'description' => 'Trax offers a secure and digitized process to send highly sensitive shipments like SIMs, debit and credit cards.',
                'created_at' => '2024-06-24 13:41:39',
                'updated_at' => '2024-06-24 13:41:39'
            ],
            [
                'service_title' => 'বাল্ক মুভমেন্ট',
                'lang' => 'en',
                'title' => 'Bulk Movements',
                'description' => 'We can help you with moving heavy shipments across USA, which will take the weight off your shoulders.',
                'created_at' => '2024-06-24 13:42:04',
                'updated_at' => '2024-06-24 13:42:04'
            ],
            [
                'service_title' => 'আন্তর্জাতিক লজিস্টিক্স',
                'lang' => 'en',
                'title' => 'International Logistics',
                'description' => 'Our global services span across 232 locations, enabling your business to reach new heights and expand your products worldwide.',
                'created_at' => '2024-06-24 13:42:26',
                'updated_at' => '2024-06-24 13:42:26'
            ],
            [
                'service_title' => 'ওয়ারহাউজ সংরক্ষণ',
                'lang' => 'en',
                'title' => 'Warehousing & Fulfillment',
                'description' => 'We provide a digital solution for managing inventory that includes pick-up, packing, and dispatch.',
                'created_at' => '2024-06-24 13:42:54',
                'updated_at' => '2024-06-24 13:42:54'
            ],
            [
                'service_title' => 'পরিবহন ও প্যাকিং',
                'lang' => 'en',
                'title' => 'MOVIT/Moving & Packing',
                'description' => 'Our team of experts has the necessary knowledge to ensure that all your belongings are packed and transported safely to your new home or office.',
                'created_at' => '2024-06-24 13:43:29',
                'updated_at' => '2024-06-24 13:43:29'
            ],
            [
                'service_title' => 'ই-কমার্স লজিস্টিক্স',
                'lang' => 'en',
                'title' => 'E-Commerce Logistics',
                'description' => 'Managing eCommerce logistics in-house involves overseeing the supply chain, tracking shipments from the supplier to the warehouse',
                'created_at' => '2024-06-24 13:43:53',
                'updated_at' => '2024-06-24 13:43:53'
            ],
            [
                'service_title' => 'ই-কমার্স লজিস্টিক্স',
                'lang' => 'bn',
                'title' => 'ই-কমার্স লজিস্টিক্স',
                'description' => 'ই-কমার্স লজিস্টিক্স নিজেরাই পরিচালনা করা , সাপ্লাই চেইন তত্ত্বাবধান করা, সরবরাহকারী থেকে ওয়ারহাউজ পর্যন্ত চালান ট্র্যাক করা',
                'created_at' => '2024-06-25 08:58:03',
                'updated_at' => '2024-06-25 08:58:03'
            ],
            [
                'service_title' => 'পরিবহন ও প্যাকিং',
                'lang' => 'bn',
                'title' => 'পরিবহন ও প্যাকিং',
                'description' => 'আমাদের বিশেষজ্ঞ দলের প্রয়োজনীয় জ্ঞান রয়েছে যাতে আপনার সমস্ত জিনিসপত্র নিরাপদে আপনার নতুন বাড়ি বা অফিসে প্যাক এবং পরিবহন করা হয়।',
                'created_at' => '2024-06-25 09:03:02',
                'updated_at' => '2024-06-25 09:03:02'
            ],
            [
                'service_title' => 'ওয়ারহাউজ সংরক্ষণ',
                'lang' => 'bn',
                'title' => 'ওয়ারহাউজ সংরক্ষণ',
                'description' => 'আমরা সংগ্রহ, প্যাকিং, এবং প্রেরণসহ ইনভেন্টরি ব্যবস্থাপনার জন্য একটি ডিজিটাল সমাধান প্রদান করি।',
                'created_at' => '2024-06-25 10:26:13',
                'updated_at' => '2024-06-25 10:26:13'
            ],
            [
                'service_title' => 'আন্তর্জাতিক লজিস্টিক্স',
                'lang' => 'bn',
                'title' => 'আন্তর্জাতিক লজিস্টিক্স',
                'description' => 'আমাদের বিশ্বব্যাপী সেবাগুলি ২৩২ স্থানে ছড়িয়ে পড়ে, যা আপনার ব্যবসাকে নতুন উচ্চারণে পৌঁছানো এবং আপনার পণ্যগুলি বিশ্বব্যাপী প্রসার করতে সহায়ক।',
                'created_at' => '2024-06-25 10:26:41',
                'updated_at' => '2024-06-25 10:26:41'
            ],
            [
                'service_title' => 'বাল্ক মুভমেন্ট',
                'lang' => 'bn',
                'title' => 'বাল্ক মুভমেন্ট',
                'description' => 'আমরা ভারি পণ্য শিপমেন্ট বিশ্বব্যাপী করে থাকি, যা আপনাকে সহায়তা করবে',
                'created_at' => '2024-06-25 10:27:20',
                'updated_at' => '2024-06-25 10:27:20'
            ],
            [
                'service_title' => 'ডকুমেন্ট ডেলিভারি',
                'lang' => 'bn',
                'title' => 'ডকুমেন্ট ডেলিভারি',
                'description' => 'ডেলিক্স একটি নিরাপদ এবং ডিজিটাইজড প্রক্রিয়া প্রদান করে যেখানে সিম, ডেবিট এবং ক্রেডিট কার্ড সহ অত্যন্ত সংহ্রাদপূর্ণ পাঠানো হয়।',
                'created_at' => '2024-06-25 10:27:35',
                'updated_at' => '2024-06-25 10:27:35'
            ]
        ];

        foreach ($serviceLanguages as $language) {
            $service = $services[$language['service_title']] ?? null;
            
            if ($service) {
                DB::table('website_service_languages')->updateOrInsert(
                    [
                        'website_service_id' => $service->id,
                        'lang' => $language['lang']
                    ],
                    [
                        'website_service_id' => $service->id,
                        'lang' => $language['lang'],
                        'title' => $language['title'],
                        'description' => $language['description'],
                        'created_at' => $language['created_at'],
                        'updated_at' => $language['updated_at']
                    ]
                );
            }
        }
    }
}
