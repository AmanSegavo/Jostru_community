<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
$columns = DB::select('DESCRIBE posts');
echo "Columns in posts table:<br>";
foreach ($columns as $column) {
    echo "- " . $column->Field . " (" . $column->Type . ")<br>";
}
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
