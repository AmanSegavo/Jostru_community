<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$migrator = app('migrator');
// Pass the specific file paths in the first argument
$migrator->run([
    database_path('migrations/2026_06_07_120000_create_debts_table.php'),
    database_path('migrations/2026_06_07_120001_create_permission_delegations_table.php'),
    database_path('migrations/2026_06_07_133000_create_data_lake_records_table.php'),
    database_path('migrations/2026_06_07_140000_create_settings_table.php'),
]);
echo "Data Lake Migration completed.\n";
