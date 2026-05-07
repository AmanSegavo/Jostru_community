<?php
$source = __DIR__.'/../storage/app/public/posts';
$dest = __DIR__.'/storage/posts';

if (!file_exists($dest)) {
    mkdir($dest, 0755, true);
}

$files = glob($source.'/*');
foreach ($files as $file) {
    if (is_file($file)) {
        $filename = basename($file);
        if (copy($file, $dest.'/'.$filename)) {
            echo "Copied: $filename\n";
        } else {
            echo "Failed: $filename\n";
        }
    }
}
echo "Migration complete.\n";
