<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

$files = File::files(database_path('migrations'));
$batch = DB::table('migrations')->max('batch') + 1;

foreach ($files as $file) {
    $name = $file->getFilenameWithoutExtension();
    $ran = DB::table('migrations')->where('migration', $name)->exists();
    if (!$ran) {
        echo "Running: $name\n";
        try {
            Artisan::call('migrate', ['--path' => 'database/migrations/'.$file->getFilename(), '--force' => true]);
            echo Artisan::output();
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'already exists') || str_contains($msg, 'Duplicate column')) {
                echo "Table/Column exists! Marking as migrated.\n";
                DB::table('migrations')->insert(['migration' => $name, 'batch' => $batch]);
            } else {
                echo "ERROR: " . $msg . "\n";
            }
        }
    }
}
echo "Done!\n";
