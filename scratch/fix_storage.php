<?php
// fix_storage.php - Versi untuk cPanel (taruh di dalam public_html)

echo "<h2>🔧 Fix Storage untuk cPanel - Jostru</h2>";

// Path ke storage Laravel (naik 1 folder dari public_html)
$laravelStorage = __DIR__ . '/../storage/app/public';

// Path tujuan (folder storage di public_html)
$publicStorage = __DIR__ . '/storage';

echo "<p><strong>Laravel Storage Path:</strong> $laravelStorage</p>";
echo "<p><strong>Public Storage Path:</strong> $publicStorage</p><hr>";

// Buat folder storage di public_html jika belum ada
if (!file_exists($publicStorage)) {
    if (mkdir($publicStorage, 0755, true)) {
        echo "<p style='color:lime;'>✅ Folder <b>storage</b> berhasil dibuat di public_html</p>";
    } else {
        echo "<p style='color:red;'>❌ Gagal membuat folder storage. Buat manual.</p>";
    }
} else {
    echo "<p style='color:yellow;'>⚠️ Folder storage sudah ada.</p>";
}

// Buat folder posts jika belum ada
$postsPath = $publicStorage . '/posts';
if (!file_exists($postsPath)) {
    mkdir($postsPath, 0755, true);
    echo "<p style='color:lime;'>✅ Folder <b>posts</b> berhasil dibuat.</p>";
}

// Coba buat symbolic link
if (function_exists('symlink')) {
    if (@symlink($laravelStorage, $publicStorage)) {
        echo "<p style='color:lime;'><strong>✅ Symbolic link berhasil dibuat!</strong></p>";
    } else {
        echo "<p style='color:orange;'>❌ Symbolic link gagal dibuat (biasa di cPanel).</p>";
    }
}

echo "<hr>";
echo "<h3>Langkah Selanjutnya:</h3>";
echo "<ol>";
echo "<li>Copy semua file dari folder <code>../storage/app/public/posts</code> ke <code>public_html/storage/posts</code></li>";
echo "<li>Setelah selesai, <strong>hapus file ini</strong> (fix_storage.php)</li>";
echo "</ol>";