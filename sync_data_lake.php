<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DataLakeRecord;
use App\Models\User;
use App\Models\Finance;
use App\Models\Debt;
use App\Models\WasteDeposit;
use App\Models\Event;

echo "Memulai proses sinkronisasi Data Lake...\n";

// Clear existing synced data to avoid duplicates if run multiple times (optional, but let's clear only specific categories to be safe)
DataLakeRecord::whereIn('category', ['USER_DUMP', 'FINANCE_DUMP', 'DEBT_DUMP', 'WASTE_DUMP', 'EVENT_DUMP'])->delete();

// 1. Sinkronisasi Users
$users = User::all();
$userCount = 0;
foreach ($users as $u) {
    $payload = $u->toArray();
    // Jika user punya latitude/longitude, bisa masuk ke map!
    if (!empty($u->geolocation_lat) && !empty($u->geolocation_lng)) {
        $payload['latitude'] = $u->geolocation_lat;
        $payload['longitude'] = $u->geolocation_lng;
        $payload['company_name'] = "Anggota: " . $u->name;
    }
    
    DataLakeRecord::create([
        'division_id' => null, // Global
        'category' => 'USER_DUMP',
        'status' => 'PROCESSED',
        'payload' => $payload,
        'created_by' => 1,
    ]);
    $userCount++;
}
echo "Tersinkronisasi $userCount Data Anggota (Users).\n";

// 2. Sinkronisasi Finances
$finances = Finance::all();
$financeCount = 0;
foreach ($finances as $f) {
    DataLakeRecord::create([
        'division_id' => $f->division_id,
        'category' => 'FINANCE_DUMP',
        'status' => 'PROCESSED',
        'payload' => $f->toArray(),
        'created_by' => 1,
    ]);
    $financeCount++;
}
echo "Tersinkronisasi $financeCount Data Keuangan (Finances).\n";

// 3. Sinkronisasi Debts
$debts = Debt::all();
$debtCount = 0;
foreach ($debts as $d) {
    DataLakeRecord::create([
        'division_id' => null,
        'category' => 'DEBT_DUMP',
        'status' => 'PROCESSED',
        'payload' => $d->toArray(),
        'created_by' => 1,
    ]);
    $debtCount++;
}
echo "Tersinkronisasi $debtCount Data Hutang/Piutang (Debts).\n";

// 4. Sinkronisasi WasteDeposits (jika ada)
$wastes = WasteDeposit::all();
$wasteCount = 0;
foreach ($wastes as $w) {
    DataLakeRecord::create([
        'division_id' => null,
        'category' => 'WASTE_DUMP',
        'status' => 'PROCESSED',
        'payload' => $w->toArray(),
        'created_by' => 1,
    ]);
    $wasteCount++;
}
echo "Tersinkronisasi $wasteCount Data Setoran Sampah.\n";

// 5. Sinkronisasi Events (jika ada)
$events = Event::all();
$eventCount = 0;
foreach ($events as $e) {
    DataLakeRecord::create([
        'division_id' => $e->division_id ?? null,
        'category' => 'EVENT_DUMP',
        'status' => 'PROCESSED',
        'payload' => $e->toArray(),
        'created_by' => 1,
    ]);
    $eventCount++;
}
echo "Tersinkronisasi $eventCount Data Kegiatan (Events).\n";

$total = $userCount + $financeCount + $debtCount + $wasteCount + $eventCount;
echo "\nSinkronisasi selesai! Total $total records berhasil dimasukkan ke Data Lake.\n";
