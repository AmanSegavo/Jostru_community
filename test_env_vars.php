<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

echo "APP_URL: " . env('APP_URL') . "<br>";
echo "DB_DATABASE: " . env('DB_DATABASE') . "<br>";
echo "GOOGLE_REDIRECT_URL: " . env('GOOGLE_REDIRECT_URL') . "<br>";
echo "APP_DEBUG: " . (env('APP_DEBUG') ? 'true' : 'false') . "<br>";
?>
