<?php
$host = 'sql100.infinityfree.com';
$db   = 'if0_41649436_jostru';
$user = 'if0_41649436';
$pass = 'djafu12345';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    $sql = "CREATE TABLE IF NOT EXISTS `production_batches` (
      `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      `product_sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `quantity_produced` double(8,2) NOT NULL,
      `source_waste_id` bigint(20) UNSIGNED DEFAULT NULL,
      `produced_at` timestamp NOT NULL DEFAULT current_timestamp(),
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `production_batches_source_waste_id_foreign` (`source_waste_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $pdo->exec($sql);

    $sql2 = "CREATE TABLE IF NOT EXISTS `activity_logs` (
      `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` bigint(20) UNSIGNED DEFAULT NULL,
      `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
      `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
      `created_at` timestamp NULL DEFAULT NULL,
      `updated_at` timestamp NULL DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `activity_logs_user_id_foreign` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    $pdo->exec($sql2);
    
    echo "SUCCESS: Tabel production_batches & activity_logs berhasil dibuat.";
} catch (\PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
