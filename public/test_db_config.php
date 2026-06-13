<?php
require __DIR__ . '/../vendor/autoload.php';
\ = require_once __DIR__ . '/../bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Http\Kernel::class);
\ = \->handle(\ = Illuminate\Http\Request::capture());
echo 'DB_HOST from config: ' . config('database.connections.mysql.host');
echo '<br>';
echo 'DB_HOST from env: ' . env('DB_HOST');
?>