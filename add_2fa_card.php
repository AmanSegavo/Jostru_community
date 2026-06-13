<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

try {
    echo "Memeriksa tabel users...\n";

    if (!Schema::hasColumn('users', 'card_2fa_enabled')) {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('card_2fa_enabled')->default(false)->after('password');
        });
        echo "Kolom card_2fa_enabled berhasil ditambahkan.\n";
    } else {
        echo "Kolom card_2fa_enabled sudah ada.\n";
    }

    echo "Proses selesai.\n";

} catch (\Exception $e) {
    echo "Terjadi kesalahan: " . $e->getMessage() . "\n";
}
