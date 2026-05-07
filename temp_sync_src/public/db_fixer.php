<?php
/**
 * JOSTRU COMMUNITY - MASTER DB FIXER
 * Script ini aman dijalankan berkali-kali (idempotent).
 * Akan membuat semua tabel/kolom yang hilang tanpa error duplikat.
 */

$host    = 'sql100.infinityfree.com';
$db      = 'if0_41649436_jostru';
$user    = 'if0_41649436';
$pass    = 'djafu12345';
$charset = 'utf8mb4';

$dsn     = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

$results = [];
$errors  = [];

function runSQL(PDO $pdo, string $label, string $sql, array &$results, array &$errors): void
{
    try {
        $pdo->exec($sql);
        $results[] = "✅ $label";
    } catch (PDOException $e) {
        $errors[] = "❌ $label: " . $e->getMessage();
    }
}

function columnExists(PDO $pdo, string $table, string $column): bool
{
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
    $stmt->execute([$column]);
    return $stmt->rowCount() > 0;
}

function tableExists(PDO $pdo, string $table, string $db): bool
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?");
    $stmt->execute([$db, $table]);
    return $stmt->fetchColumn() > 0;
}

try {
    $pdo = new PDO($dsn, $user, $pass, $options);

    // =====================================================
    // 1. TABEL: activity_logs
    // =====================================================
    runSQL($pdo, 'CREATE TABLE activity_logs', "
        CREATE TABLE IF NOT EXISTS `activity_logs` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `user_id` bigint(20) UNSIGNED DEFAULT NULL,
            `action` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `activity_logs_user_id_foreign` (`user_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ", $results, $errors);

    // =====================================================
    // 2. TABEL: chats
    // =====================================================
    runSQL($pdo, 'CREATE TABLE chats', "
        CREATE TABLE IF NOT EXISTS `chats` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `sender_id` bigint(20) UNSIGNED NOT NULL,
            `receiver_id` bigint(20) UNSIGNED NOT NULL,
            `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
            `is_read` tinyint(1) NOT NULL DEFAULT 0,
            `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `chats_sender_id_foreign` (`sender_id`),
            KEY `chats_receiver_id_foreign` (`receiver_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ", $results, $errors);

    // =====================================================
    // 3. TABEL: production_batches
    // =====================================================
    runSQL($pdo, 'CREATE TABLE production_batches', "
        CREATE TABLE IF NOT EXISTS `production_batches` (
            `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `product_sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
            `quantity_produced` double(8,2) NOT NULL,
            `source_waste_id` bigint(20) UNSIGNED DEFAULT NULL,
            `produced_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `production_batches_source_waste_id_foreign` (`source_waste_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ", $results, $errors);

    // =====================================================
    // 4. KOLOM TAMBAHAN di tabel USERS (aman, cek dulu)
    // =====================================================
    $userCols = [
        'status'              => "ALTER TABLE `users` ADD COLUMN `status` varchar(255) NOT NULL DEFAULT 'AKTIF'",
        'jabatan'             => "ALTER TABLE `users` ADD COLUMN `jabatan` varchar(255) NOT NULL DEFAULT 'Anggota'",
        'tanggal_lahir'       => "ALTER TABLE `users` ADD COLUMN `tanggal_lahir` date DEFAULT NULL",
        'alamat'              => "ALTER TABLE `users` ADD COLUMN `alamat` text DEFAULT NULL",
        'latitude'            => "ALTER TABLE `users` ADD COLUMN `latitude` varchar(50) DEFAULT NULL",
        'longitude'           => "ALTER TABLE `users` ADD COLUMN `longitude` varchar(50) DEFAULT NULL",
        'google_id'           => "ALTER TABLE `users` ADD COLUMN `google_id` varchar(255) DEFAULT NULL",
        'can_chat'            => "ALTER TABLE `users` ADD COLUMN `can_chat` tinyint(1) NOT NULL DEFAULT 1",
        'onesignal_player_id' => "ALTER TABLE `users` ADD COLUMN `onesignal_player_id` varchar(255) DEFAULT NULL",
    ];

    foreach ($userCols as $col => $sql) {
        if (!columnExists($pdo, 'users', $col)) {
            runSQL($pdo, "ADD COLUMN users.$col", $sql, $results, $errors);
        } else {
            $results[] = "⏭️  Kolom users.$col sudah ada, dilewati.";
        }
    }

    // =====================================================
    // 5. BERSIHKAN VIEW CACHE (hapus file compiled blade)
    // =====================================================
    $viewsPath = __DIR__ . '/../storage/framework/views';
    $cleared = 0;
    if (is_dir($viewsPath)) {
        foreach (glob($viewsPath . '/*.php') as $file) {
            unlink($file);
            $cleared++;
        }
    }
    $results[] = "🧹 Blade cache dibersihkan ($cleared file dihapus)";

} catch (PDOException $e) {
    $errors[] = "❌ KONEKSI DB GAGAL: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Jostru DB Fixer</title>
    <style>
        body { font-family: monospace; background: #0f172a; color: #e2e8f0; padding: 30px; }
        h1 { color: #22c55e; }
        .ok { color: #4ade80; }
        .err { color: #f87171; }
        .skip { color: #94a3b8; }
        li { margin: 5px 0; font-size: 15px; }
    </style>
</head>
<body>
    <h1>🚀 Jostru DB Fixer - Hasil</h1>
    <?php if (!empty($results)): ?>
    <h3 style="color:#38bdf8">Berhasil (<?= count($results) ?> item):</h3>
    <ul>
        <?php foreach ($results as $r): ?>
            <li class="<?= strpos($r,'⏭️') !== false ? 'skip' : 'ok' ?>"><?= htmlspecialchars($r) ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
    <h3 style="color:#f87171">Error (<?= count($errors) ?> item):</h3>
    <ul>
        <?php foreach ($errors as $e): ?>
            <li class="err"><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>

    <?php if (empty($errors)): ?>
    <p style="background:#15803d; padding:15px; border-radius:10px; font-size:18px;">
        ✅ <strong>SEMUA SELESAI!</strong> Semua tabel dan kolom sudah siap. Coba login sekarang!
    </p>
    <?php else: ?>
    <p style="background:#7f1d1d; padding:15px; border-radius:10px;">
        ⚠️ Ada beberapa error. Periksa pesan di atas.
    </p>
    <?php endif; ?>
</body>
</html>
