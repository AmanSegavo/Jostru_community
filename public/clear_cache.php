<?php
// Script sementara untuk clear cache di hosting
// HAPUS FILE INI SETELAH SELESAI!

// Clear config cache
if (file_exists(__DIR__ . '/bootstrap/cache/config.php')) {
    unlink(__DIR__ . '/bootstrap/cache/config.php');
    echo "Config cache cleared.\n";
}

// Clear route cache
if (file_exists(__DIR__ . '/bootstrap/cache/routes-v7.php')) {
    unlink(__DIR__ . '/bootstrap/cache/routes-v7.php');
    echo "Route cache cleared.\n";
}

// Clear views cache
$viewsPath = __DIR__ . '/storage/framework/views';
if (is_dir($viewsPath)) {
    $files = glob($viewsPath . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "Views cache cleared (" . count($files) . " files).\n";
}

echo "\nDone! HAPUS FILE clear_cache.php INI SEKARANG!\n";
