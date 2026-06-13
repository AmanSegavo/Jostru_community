<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Illuminate\Support\Facades\DB::statement('ALTER TABLE waste_deposits ADD COLUMN latitude DECIMAL(10,8) NULL AFTER file_size, ADD COLUMN longitude DECIMAL(11,8) NULL AFTER latitude;');
    echo "Waste deposit cols added.\\n";
} catch (\Exception $e) {
    echo "Waste deposit cols exist or error: " . $e->getMessage() . "\\n";
}

try {
    Illuminate\Support\Facades\Schema::create('notifications', function ($table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->text('message');
        $table->string('url')->nullable();
        $table->boolean('is_read')->default(false);
        $table->timestamps();
    });
    echo "Notifications table created.\\n";
} catch (\Exception $e) {
    echo "Notifications table exist or error: " . $e->getMessage() . "\\n";
}
