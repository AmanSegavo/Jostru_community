<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
try {
    $tables = DB::select("SHOW TABLES LIKE 'division_user'");
    if (count($tables) > 0) {
        echo 'Table division_user exists.';
    } else {
        echo 'Table division_user DOES NOT exist. Running manual creation...';
        DB::statement("
        CREATE TABLE `division_user` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `user_id` bigint unsigned NOT NULL,
            `division_id` bigint unsigned NOT NULL,
            `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Jabatan spesifik di divisi ini',
            `is_admin` tinyint(1) NOT NULL DEFAULT '0',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `division_user_user_id_division_id_unique` (`user_id`,`division_id`),
            KEY `division_user_division_id_foreign` (`division_id`),
            CONSTRAINT `division_user_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`) ON DELETE CASCADE,
            CONSTRAINT `division_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
        echo " Created successfully.";
    }
} catch (Exception $e) {
    echo 'DB Error: ' . $e->getMessage();
}
