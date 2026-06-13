<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

echo "Starting database update...\n";

// 1. Add permissions to users table
if (!Schema::hasColumn('users', 'can_manage_members')) {
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('can_manage_members')->default(false);
        $table->boolean('can_manage_finances')->default(false);
        $table->boolean('can_manage_waste')->default(false);
        $table->boolean('can_manage_posts')->default(false);
    });
    echo "Added permission columns to users table.\n";
} else {
    echo "Permission columns already exist in users table.\n";
}

// 2. Create shareholders table
if (!Schema::hasTable('shareholders')) {
    Schema::create('shareholders', function (Blueprint $table) {
        $table->id();
        $table->string('certificate_id')->unique();
        $table->string('name');
        $table->decimal('percentage', 5, 2);
        $table->string('percentage_text');
        $table->date('issue_date');
        $table->string('director_signature')->nullable();
        $table->string('commissioner_signature')->nullable();
        $table->timestamps();
    });
    echo "Created shareholders table.\n";
} else {
    echo "Shareholders table already exists.\n";
}

echo "Database update complete.\n";
