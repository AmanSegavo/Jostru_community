<?php
$source = "/home/u380603901/domains/jostru.site/public_html/public/feed_conflicted";
$dest = "/home/u380603901/domains/jostru.site/public_html/public/feed";

if (!file_exists($dest)) {
    mkdir($dest, 0755, true);
}

if (file_exists($source)) {
    $files = scandir($source);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $sourceFile = $source . '/' . $file;
        $destFile = $dest . '/' . $file;
        if (copy($sourceFile, $destFile)) {
            echo "Moved: $file\n";
        } else {
            echo "Failed to move: $file\n";
        }
    }
} else {
    echo "Source directory not found.\n";
}
