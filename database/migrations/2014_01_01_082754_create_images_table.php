<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('original_image')->nullable();
            $table->string('image_small_one')->nullable()->comment('32x32');
            $table->string('image_small_two')->nullable()->comment('40x40');
            $table->string('image_small_three')->nullable()->comment('80X80');
            $table->timestamps();
        });

        \DB::statement("INSERT INTO `images` (`id`, `original_image`, `image_small_one`, `image_small_two`, `image_small_three`, `created_at`, `updated_at`) VALUES
        (2, 'admin/profile-images/20231116095840_original_23.PNG', 'admin/profile-images/20231116095840image_small_one28.PNG', 'admin/profile-images/20231116095840image_small_two23.PNG', 'admin/profile-images/20231116095840image_small_three18.PNG', '2023-11-16 03:58:41', '2023-11-16 03:58:41'),
        (3, 'admin/profile-images/20231213111031_original_29.PNG', 'admin/profile-images/20231213111031image_small_one8.PNG', 'admin/profile-images/20231213111031image_small_two16.PNG', 'admin/profile-images/20231213111031image_small_three34.PNG', '2023-11-16 04:29:56', '2023-12-13 05:10:31'),
        (5, 'admin/profile-images/20240122111426_original_4.png', 'admin/profile-images/20240122111426image_small_one3.png', 'admin/profile-images/20240122111426image_small_two10.png', 'admin/profile-images/20240122111426image_small_three20.png', '2023-12-19 11:27:10', '2024-01-22 05:14:26'),
        (6, 'admin/profile-images/20231220120438_original_47.PNG', 'admin/profile-images/20231220120438image_small_one3.PNG', 'admin/profile-images/20231220120438image_small_two26.PNG', 'admin/profile-images/20231220120438image_small_three11.PNG', '2023-12-20 06:04:39', '2023-12-20 06:04:39'),
        (7, 'admin/profile-images/20231220122159_original_36.PNG', 'admin/profile-images/20231220122159image_small_one36.PNG', 'admin/profile-images/20231220122159image_small_two24.PNG', 'admin/profile-images/20231220122159image_small_three23.PNG', '2023-12-20 06:22:00', '2023-12-20 06:22:00'),
        (8, 'admin/profile-images/20240102151627_original_18.png', 'admin/profile-images/20240102151627image_small_one43.png', 'admin/profile-images/20240102151627image_small_two10.png', 'admin/profile-images/20240102151627image_small_three29.png', '2023-12-20 07:12:25', '2024-01-02 09:16:27'),
        (9, 'admin/profile-images/20231221104812_original_21.PNG', 'admin/profile-images/20231221104812image_small_one17.PNG', 'admin/profile-images/20231221104812image_small_two31.PNG', 'admin/profile-images/20231221104812image_small_three31.PNG', '2023-12-21 04:48:13', '2023-12-21 04:48:13'),
        (10, 'admin/profile-images/20231221105952_original_17.PNG', 'admin/profile-images/20231221105952image_small_one22.PNG', 'admin/profile-images/20231221105952image_small_two1.PNG', 'admin/profile-images/20231221105952image_small_three34.PNG', '2023-12-21 04:59:53', '2023-12-21 04:59:53'),
        (11, 'admin/profile-images/20231221111244_original_7.PNG', 'admin/profile-images/20231221111244image_small_one14.PNG', 'admin/profile-images/20231221111244image_small_two25.PNG', 'admin/profile-images/20231221111244image_small_three16.PNG', '2023-12-21 05:12:44', '2023-12-21 05:12:44'),
        (12, 'admin/profile-images/20231224171337_original_42.jpg', 'admin/profile-images/20231224171337image_small_one2.jpg', 'admin/profile-images/20231224171337image_small_two10.jpg', 'admin/profile-images/20231224171337image_small_three43.jpg', '2023-12-24 11:13:38', '2023-12-24 11:13:38'),
        (13, 'admin/profile-images/20231224172059_original_30.jpg', 'admin/profile-images/20231224172059image_small_one35.jpg', 'admin/profile-images/20231224172059image_small_two9.jpg', 'admin/profile-images/20231224172059image_small_three45.jpg', '2023-12-24 11:21:00', '2023-12-24 11:21:00'),
        (14, 'admin/profile-images/20231226111227_original_42.jpg', 'admin/profile-images/20231226111227image_small_one33.jpg', 'admin/profile-images/20231226111227image_small_two14.jpg', 'admin/profile-images/20231226111227image_small_three5.jpg', '2023-12-26 05:12:27', '2023-12-26 05:12:27'),
        (15, 'admin/profile-images/20240101182823_original_28.png', 'admin/profile-images/20240101182823image_small_one8.png', 'admin/profile-images/20240101182823image_small_two5.png', 'admin/profile-images/20240101182823image_small_three30.png', '2024-01-01 12:28:23', '2024-01-01 12:28:23'),
        (16, 'admin/profile-images/20240102150946_original_26.png', 'admin/profile-images/20240102150946image_small_one39.png', 'admin/profile-images/20240102150946image_small_two43.png', 'admin/profile-images/20240102150946image_small_three49.png', '2024-01-02 09:09:47', '2024-01-02 09:09:47'),
        (17, 'admin/profile-images/20240102151822_original_18.png', 'admin/profile-images/20240102151822image_small_one22.png', 'admin/profile-images/20240102151822image_small_two22.png', 'admin/profile-images/20240102151822image_small_three29.png', '2024-01-02 09:18:22', '2024-01-02 09:18:22'),
        (18, 'admin/profile-images/20240110173312_original_9.png', 'admin/profile-images/20240110173312image_small_one14.png', 'admin/profile-images/20240110173312image_small_two28.png', 'admin/profile-images/20240110173312image_small_three46.png', '2024-01-10 11:33:12', '2024-01-10 11:33:12'),
        (19, 'admin/profile-images/20240110182712_original_12.png', 'admin/profile-images/20240110182712image_small_one29.png', 'admin/profile-images/20240110182712image_small_two15.png', 'admin/profile-images/20240110182712image_small_three16.png', '2024-01-10 12:27:12', '2024-01-10 12:27:12'),
        (20, 'admin/profile-images/20240111152120_original_22.png', 'admin/profile-images/20240111152120image_small_one7.png', 'admin/profile-images/20240111152120image_small_two49.png', 'admin/profile-images/20240111152120image_small_three14.png', '2024-01-11 09:21:21', '2024-01-11 09:21:21'),
        (21, 'admin/profile-images/20240115144908_original_20.png', 'admin/profile-images/20240115144908image_small_one14.png', 'admin/profile-images/20240115144908image_small_two6.png', 'admin/profile-images/20240115144908image_small_three13.png', '2024-01-13 09:21:41', '2024-01-15 08:49:09'),
        (22, 'admin/profile-images/20240118173933_original_20.png', 'admin/profile-images/20240118173933image_small_one18.png', 'admin/profile-images/20240118173933image_small_two4.png', 'admin/profile-images/20240118173933image_small_three46.png', '2024-01-14 09:48:39', '2024-01-18 11:39:33'),
        (23, 'admin/profile-images/20240117102509_original_35.png', 'admin/profile-images/20240117102509image_small_one26.png', 'admin/profile-images/20240117102509image_small_two40.png', 'admin/profile-images/20240117102509image_small_three23.png', '2024-01-17 04:23:13', '2024-01-17 04:25:09'),
        (24, 'admin/profile-images/20240117115217_original_41.png', 'admin/profile-images/20240117115217image_small_one25.png', 'admin/profile-images/20240117115217image_small_two11.png', 'admin/profile-images/20240117115217image_small_three30.png', '2024-01-17 05:52:18', '2024-01-17 05:52:18'),
        (25, 'admin/profile-images/20240117143635_original_38.png', 'admin/profile-images/20240117143635image_small_one12.png', 'admin/profile-images/20240117143635image_small_two9.png', 'admin/profile-images/20240117143635image_small_three44.png', '2024-01-17 08:36:36', '2024-01-17 08:36:36'),
        (26, 'admin/profile-images/20240117155755_original_30.png', 'admin/profile-images/20240117155755image_small_one41.png', 'admin/profile-images/20240117155755image_small_two28.png', 'admin/profile-images/20240117155755image_small_three18.png', '2024-01-17 09:57:55', '2024-01-17 09:57:55'),
        (28, 'admin/profile-images/20240118155853_original_11.png', 'admin/profile-images/20240118155853image_small_one40.png', 'admin/profile-images/20240118155853image_small_two6.png', 'admin/profile-images/20240118155853image_small_three30.png', '2024-01-18 09:58:54', '2024-01-18 09:58:54'),
        (29, 'admin/profile-images/20240118160238_original_29.png', 'admin/profile-images/20240118160238image_small_one14.png', 'admin/profile-images/20240118160238image_small_two7.png', 'admin/profile-images/20240118160238image_small_three50.png', '2024-01-18 10:02:39', '2024-01-18 10:02:39'),
        (30, 'admin/profile-images/20240118163508_original_22.png', 'admin/profile-images/20240118163508image_small_one47.png', 'admin/profile-images/20240118163508image_small_two49.png', 'admin/profile-images/20240118163508image_small_three26.png', '2024-01-18 10:35:08', '2024-01-18 10:35:08'),
        (31, 'admin/profile-images/20240118182203_original_34.png', 'admin/profile-images/20240118182203image_small_one19.png', 'admin/profile-images/20240118182203image_small_two40.png', 'admin/profile-images/20240118182203image_small_three36.png', '2024-01-18 11:12:02', '2024-01-18 12:22:04');");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
