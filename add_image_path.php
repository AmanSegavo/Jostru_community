<?php
require __DIR__.'/vendor/autoload.php';
\ = require_once __DIR__.'/bootstrap/app.php';
\ = \->make(Illuminate\Contracts\Console\Kernel::class);
\->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('waste_categories', 'image_path')) {
    Schema::table('waste_categories', function (Blueprint \) {
        \->string('image_path')->nullable()->after('description');
    });
    echo 'Column image_path added to waste_categories.';
} else {
    echo 'Column image_path already exists.';
}
