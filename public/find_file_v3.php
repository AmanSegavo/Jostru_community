<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$path = public_path('feed');
echo "public_path('feed') returns: " . $path . "\n";
if (file_exists($path)) {
    echo "Path exists. Type: " . (is_link($path) ? "LINK" : "DIR") . "\n";
} else {
    echo "Path does not exist.\n";
}
