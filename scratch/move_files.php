<?php
$source = "/home/u380603901/domains/jostru.site/public_html/public/feed_conflicted";
$dest = "/home/u380603901/domains/jostru.site/public_html/public/feed";

if (!file_exists($dest)) {
    mkdir($dest, 0755, true);
}

$files = scandir($source);
foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    $sourcePath = $source . '/' . $file;
    $destPath = $dest . '/' . $file;
    if (copy($sourcePath, $destPath)) {
        echo "Moved: $file\n";
    } else {
        echo "Failed: $file\n";
    }
}
echo "Done.\n";
