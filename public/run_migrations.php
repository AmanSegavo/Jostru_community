<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

try {
    Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo '<pre>' . Illuminate\Support\Facades\Artisan::output() . '</pre>';
    echo '<h3>MIGRATION SUCCESSFUL</h3>';
} catch (\Exception $e) {
    echo '<h3>ERROR:</h3><pre>' . $e->getMessage() . '</pre>';
}
?>
