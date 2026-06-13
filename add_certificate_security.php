<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Memeriksa tabel shareholders...\n";

if (Schema::hasTable('shareholders')) {
    Schema::table('shareholders', function (Blueprint $table) {
        if (!Schema::hasColumn('shareholders', 'user_id')) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            echo "Kolom user_id berhasil ditambahkan.\n";
        } else {
            echo "Kolom user_id sudah ada.\n";
        }

        if (!Schema::hasColumn('shareholders', 'secret_pin')) {
            $table->string('secret_pin', 20)->nullable()->after('certificate_id');
            echo "Kolom secret_pin berhasil ditambahkan.\n";
        } else {
            echo "Kolom secret_pin sudah ada.\n";
        }
    });
} else {
    echo "Tabel shareholders tidak ditemukan!\n";
}

echo "Proses selesai.\n";
