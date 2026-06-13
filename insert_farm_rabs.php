<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Division;
use App\Models\Rab;
use App\Models\RabItem;
use App\Models\Budget;

$farmDivision = Division::where('name', 'like', '%Farm%')->first();
if (!$farmDivision) {
    echo "Divisi Farm tidak ditemukan!\n";
    exit;
}

// RAB 1: 5 Juni 2026 - 5 Juli 2026
$rab1 = Rab::create([
    'title' => 'RAB Jostru Farm (5 Juni 2026 - 5 Juli 2026)',
    'division_id' => $farmDivision->id,
    'description' => 'Operasional Jostru Farm Bulan ke-1',
    'status' => 'APPROVED',
    'total_amount' => 2300000
]);
RabItem::create(['rab_id' => $rab1->id, 'name' => 'OPS Jostru Farm', 'qty' => 1, 'unit_price' => 1500000, 'subtotal' => 1500000]);
RabItem::create(['rab_id' => $rab1->id, 'name' => 'Biaya Sewa', 'qty' => 1, 'unit_price' => 500000, 'subtotal' => 500000]);
RabItem::create(['rab_id' => $rab1->id, 'name' => 'Biaya Listrik dan Air', 'qty' => 1, 'unit_price' => 300000, 'subtotal' => 300000]);
Budget::create([
    'division_id' => $farmDivision->id,
    'allocated_amount' => 2300000,
    'period' => '2026-06',
    'description' => 'Alokasi otomatis dari persetujuan RAB: ' . $rab1->title
]);

// RAB 2: 5 Juli 2026 - 5 Agustus 2026
$rab2 = Rab::create([
    'title' => 'RAB Jostru Farm (5 Juli 2026 - 5 Agustus 2026)',
    'division_id' => $farmDivision->id,
    'description' => 'Operasional Jostru Farm Bulan ke-2',
    'status' => 'APPROVED',
    'total_amount' => 5300000
]);
RabItem::create(['rab_id' => $rab2->id, 'name' => 'OPS Jostru Farm', 'qty' => 1, 'unit_price' => 1500000, 'subtotal' => 1500000]);
RabItem::create(['rab_id' => $rab2->id, 'name' => 'Gaji Tim Jostru Farm (2 orang)', 'qty' => 1, 'unit_price' => 3000000, 'subtotal' => 3000000]);
RabItem::create(['rab_id' => $rab2->id, 'name' => 'Biaya Sewa Lahan', 'qty' => 1, 'unit_price' => 500000, 'subtotal' => 500000]);
RabItem::create(['rab_id' => $rab2->id, 'name' => 'Biaya Listrik dan Air', 'qty' => 1, 'unit_price' => 300000, 'subtotal' => 300000]);
Budget::create([
    'division_id' => $farmDivision->id,
    'allocated_amount' => 5300000,
    'period' => '2026-07',
    'description' => 'Alokasi otomatis dari persetujuan RAB: ' . $rab2->title
]);

// RAB 3: 5 Agustus 2026 - 5 September 2026
$rab3 = Rab::create([
    'title' => 'RAB Jostru Farm (5 Agustus 2026 - 5 September 2026)',
    'division_id' => $farmDivision->id,
    'description' => 'Operasional Jostru Farm Bulan ke-3',
    'status' => 'APPROVED',
    'total_amount' => 5300000
]);
RabItem::create(['rab_id' => $rab3->id, 'name' => 'OPS Jostru Farm', 'qty' => 1, 'unit_price' => 1500000, 'subtotal' => 1500000]);
RabItem::create(['rab_id' => $rab3->id, 'name' => 'Gaji Tim Jostru Farm (2 orang)', 'qty' => 1, 'unit_price' => 3000000, 'subtotal' => 3000000]);
RabItem::create(['rab_id' => $rab3->id, 'name' => 'Biaya Sewa Lahan', 'qty' => 1, 'unit_price' => 500000, 'subtotal' => 500000]);
RabItem::create(['rab_id' => $rab3->id, 'name' => 'Biaya Listrik dan Air', 'qty' => 1, 'unit_price' => 300000, 'subtotal' => 300000]);
Budget::create([
    'division_id' => $farmDivision->id,
    'allocated_amount' => 5300000,
    'period' => '2026-08',
    'description' => 'Alokasi otomatis dari persetujuan RAB: ' . $rab3->title
]);

echo "Berhasil memasukkan 3 RAB Jostru Farm ke database beserta anggarannya.\n";
