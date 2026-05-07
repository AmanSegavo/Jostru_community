<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$path = "/home/u380603901/domains/jostru.site/public_html/public/feed";
if (is_link($path)) {
    echo "Path is a LINK.\n";
    echo "Link target: " . readlink($path) . "\n";
    echo "Target exists: " . (file_exists(readlink($path)) ? "YES" : "NO") . "\n";
} else {
    echo "Path is NOT a link.\n";
    if (file_exists($path)) {
        echo "Path is a DIR.\n";
    } else {
        echo "Path DOES NOT EXIST at all.\n";
    }
}
