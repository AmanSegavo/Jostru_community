<?php
require dirname(__DIR__) . '/vendor/autoload.php';
\ = require_once dirname(__DIR__) . '/bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Http\Kernel::class);
\ = \->handle(\ = Illuminate\Http\Request::capture());

// Create feed dir
\ = dirname(__DIR__) . '/public/feed';
if (!is_dir(\)) {
    mkdir(\, 0777, true);
    echo 'Created: ' . \ . '<br>';
} else {
    chmod(\, 0777);
    echo 'Exists + chmod 777: ' . \ . '<br>';
}

\Artisan::call('migrate', ['--force' => true]);
echo nl2br(\Artisan::output());
echo 'Done!';
