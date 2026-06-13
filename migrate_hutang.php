<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Finance;
use App\Models\Debt;

$finances = Finance::where('description', 'like', '%hutang%')
                   ->orWhere('kategori', 'like', '%hutang%')
                   ->orWhere('description', 'like', '%piutang%')
                   ->orWhere('kategori', 'like', '%piutang%')
                   ->get();

$count = 0;
foreach($finances as $finance) {
    if (strpos($finance->description, 'Otomatis dari') !== false || strpos($finance->description, 'Pembayaran') !== false) {
        continue;
    }
    
    $debtType = 'HUTANG';
    if (stripos($finance->description, 'piutang') !== false || stripos($finance->kategori, 'piutang') !== false) {
        $debtType = 'PIUTANG';
    }

    $creditorName = 'Diambil dari Kas Pusat';
    
    // Create Debt
    $debt = new Debt();
    $debt->creditor_name = $creditorName;
    $debt->amount = $finance->amount;
    $debt->remaining_amount = $finance->amount;
    $debt->type = $debtType;
    $debt->status = 'BELUM LUNAS';
    $debt->description = $finance->description;
    $debt->user_id = $finance->user_id ?? 1;
    $debt->created_at = $finance->transaction_date ? $finance->transaction_date . ' 00:00:00' : now();
    $debt->save();

    // Update Finance
    $finance->kategori = 'HUTANG/PIUTANG (Migrasi)';
    $finance->description = 'Otomatis dari input hutang: ' . $finance->description;
    $finance->save();
    
    $count++;
}

echo "Berhasil memigrasi $count data hutang/piutang dari Kas Pusat.\n";
