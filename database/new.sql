ALTER TABLE `role_users` DROP INDEX `role_users_role_id_foreign`;
ALTER TABLE `role_users` DROP PRIMARY KEY;
ALTER TABLE `role_users` ADD `id` BIGINT(20) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);

php artisan migrate --path=/database/migrations/2023_01_29_052118_create_email_templates_table.php

php artisan migrate --path=/database/migrations/2014_01_02_112613_create_timezones_table.php

INSERT INTO `settings` (`id`, `title`, `value`, `created_at`, `updated_at`) VALUES (NULL, 'time_zone', 'Asia/Dhaka', NULL, NULL);
ALTER TABLE `settings` ADD `lang` VARCHAR(20) NULL DEFAULT NULL AFTER `value`;

php artisan migrate --path=/database/migrations/2023_01_29_051134_create_languages_table.php

php artisan migrate --path=/database/migrations/2023_02_02_111739_create_countries_table.php

php artisan migrate --path=/database/migrations/2023_01_29_050544_create_currencies_table.php

php artisan migrate --path=/database/migrations/2023_03_02_113712_create_flag_icons_table.php

php artisan migrate --path=/database/migrations/2023_03_19_091107_create_language_configs_table.php

RENAME branches group TO  branchs;


INSERT INTO `permissions` (`id`, `attribute`, `create`, `read`, `update`, `delete`, `keywords`, `created_at`, `updated_at`) VALUES (NULL, 'email_template', NULL, NULL, NULL, NULL, '{\"read\":\"email_template_read\",\"create\":\"email_template_create\",\"update\":\"email_template_update\",\"delete\":\"email_template_delete\"}', '2021-12-15 22:59:56', '2021-12-15 22:59:56');
UPDATE `users` SET `permissions` = '[]' WHERE `users`.`id` = 1;

            "email_template_read",
            "email_template_create",
            "email_template_update",
            "email_template_delete"
            "server_configuration_update",
            "currency_read",
            "currency_create",
            "currency_update",
            "currency_delete",
            "default_currency",
            "currency_format",
            "language_read",
            "language_create",
            "language_update",
            "language_delete",

            "country_read",
            "country_create",
            "country_update",
            "country_delete",
            "panel_setting",

UPDATE `permissions` SET `attribute` = 'branch' WHERE `permissions`.`id` = 15;
UPDATE `permissions` SET `keywords` = '{\"read\":\"hub_read\",\"create\":\"hub_create\",\"update\":\"hub_update\",\"delete\":\"branch_delete\"}' WHERE `permissions`.`id` = 15;
UPDATE `permissions` SET `keywords` = '{\"read\":\"branch_read\",\"create\":\"hub_create\",\"update\":\"hub_update\",\"delete\":\"branch_delete\"}' WHERE `permissions`.`id` = 15;
UPDATE `permissions` SET `keywords` = '{\"read\":\"branch_read\",\"create\":\"branch_create\",\"update\":\"hub_update\",\"delete\":\"branch_delete\"}' WHERE `permissions`.`id` = 15;
UPDATE `permissions` SET `keywords` = '{\"read\":\"branch_read\",\"create\":\"branch_create\",\"update\":\"branch_update\",\"delete\":\"branch_delete\"}' WHERE `permissions`.`id` = 15;
ALTER TABLE `users` CHANGE `branch_id` `user_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL;
"branch_read","branch_create","branch_update","branch_delete"

merchant_payment_methods table create command hobe field hosce id, name, type;




php artisan db:seed --class=BranchSeeder
payment_methods table create command hobe field hosce id, name, type;

INSERT INTO `permissions` (`id`, `attribute`, `create`, `read`, `update`, `delete`, `keywords`, `created_at`, `updated_at`) VALUES (NULL, 'payment_method', NULL, NULL, NULL, NULL, '{\"read\":\"payment_method_read\",\"create\":\"payment_method_create\",\"update\":\"payment_method_update\",\"delete\":\"payment_method_delete\"}', NULL, NULL);
UPDATE `users` SET `permissions` = '[\"payment_method_create\",\"payment_method_read\",\"payment_method_update\",\"user_create\",\"branch_read\",\"user_read\",\"user_update\",\"send_to_paperfly\",\"user_delete\",\"user_account_activity_read\",\"user_payment_logs_read\",\"user_logout_from_devices\",\"role_create\",\"role_read\",\"role_update\",\"role_delete\",\"permission_read\",\"permission_create\",\"permission_update\",\"permission_delete\",\"merchant_create\",\"merchant_read\",\"use_all_merchant\",\"read_all_merchant\",\"merchant_update\",\"merchant_delete\",\"merchant_shop_read\",\"branch_create\",\"branch_update\",\"branch_delete\",\"merchant_shop_create\",\"merchant_shop_delete\",\"merchant_shop_update\",\"merchant_payment_account_read\",\"merchant_payment_account_update\",\"merchant_account_activity_read\",\"merchant_cod_charge_read\",\"merchant_charge_read\",\"merchant_payment_logs_read\",\"merchant_api_credentials_read\",\"merchant_api_credentials_update\",\"merchant_staff_rea[...]
