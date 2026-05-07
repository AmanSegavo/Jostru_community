<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
echo "Base Path: " . base_path() . "\n";
echo "Public Path: " . public_path() . "\n";
echo "Storage Path: " . storage_path() . "\n";
