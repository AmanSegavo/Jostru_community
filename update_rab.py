import paramiko
import sys

try:
    client=paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    client.connect('145.223.108.47', port=65002, username='u380603901', password='Farm12345@#', timeout=10)
    sftp = client.open_sftp()
    with sftp.open('/home/u380603901/domains/jostru.site/public_html/test_rab.php', 'w') as f:
        f.write('''<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Ensure Division Exists
$div = \App\Models\Division::firstOrCreate(
    ['name' => 'Jostru Farm', 'type' => 'FARM'],
    ['description' => 'Divisi Manajemen dan Peternakan Jostru Farm.', 'slug' => 'jostru-farm']
);

// We need to check if Rab model exists, wait, is it Rab? Yes, rabs table.
// If it fails, we will catch it.
if (class_exists(\App\Models\Rab::class)) {
    $rab = \App\Models\Rab::firstOrCreate(
        ['title' => 'RAB Jostru Farm Siklus 1 (90 Hari)', 'division_id' => $div->id],
        ['total_amount' => 15000000, 'status' => 'APPROVED', 'description' => 'Target Revenue: Rp 20.000.000 (Aman). Bulan 1: 2.3jt, Bulan 2: 5.3jt, Bulan 3: 5.3jt']
    );
    echo "RAB ID: {$rab->id}\\n";
}

if (class_exists(\App\Models\Budget::class)) {
    $budget = \App\Models\Budget::firstOrCreate(
        ['division_id' => $div->id, 'period' => 'Siklus 1 (90 Hari)'],
        ['allocated_amount' => 15000000, 'description' => 'Budget Utama Jostru Farm Siklus 1 (3 Bulan)']
    );
    echo "Budget ID: {$budget->id}\\n";
}

echo "Division ID: {$div->id}\\n";
''')
    sftp.close()
    
    print("Executing PHP script on remote server...")
    stdin, stdout, stderr = client.exec_command('php /home/u380603901/domains/jostru.site/public_html/test_rab.php')
    print("STDOUT:", stdout.read().decode())
    print("STDERR:", stderr.read().decode())
    
    client.close()
except Exception as e:
    print(f"Error: {e}")
