<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - Jostru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .container { max-width: 900px; margin-top: 40px; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table-accounting th {
            background: rgba(34, 197, 94, 0.1);
            color: #16a34a;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        .glass-header {
            background: linear-gradient(135deg, #10b981, #3b82f6);
            color: white;
            padding: 20px;
            border-radius: 16px 16px 0 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-3">
            <h2 style="font-weight: 800; color: #1e293b;">Jostru Holding Company</h2>
            <p class="text-muted">Dokumen Resmi Laporan Keuangan</p>
        </div>
        
        <div class="card mb-4">
            <div class="glass-header">
                <h4 class="mb-0 fw-bold">Buku Kas Umum</h4>
                <p class="mb-0" style="opacity: 0.9;">Periode: {{ request('start_date') ?: 'Awal' }} s.d {{ request('end_date') ?: 'Sekarang' }}</p>
            </div>
            <div class="card-body p-4">
                <div class="row text-center mb-4">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="p-3 bg-light rounded" style="border-left: 4px solid #3b82f6;">
                            <small class="text-muted fw-bold d-block">SALDO TERSEDIA</small>
                            <span class="fs-4 fw-bold text-primary">Rp {{ number_format($saldo, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <div class="p-3 bg-light rounded" style="border-left: 4px solid #22c55e;">
                            <small class="text-muted fw-bold d-block">TOTAL PEMASUKAN</small>
                            <span class="fs-5 fw-bold text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded" style="border-left: 4px solid #ef4444;">
                            <small class="text-muted fw-bold d-block">TOTAL PENGELUARAN</small>
                            <span class="fs-5 fw-bold text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover table-accounting mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Uraian Transaksi</th>
                                <th class="text-end">Debet (Rp)</th>
                                <th class="text-end">Kredit (Rp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($finances as $f)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($f->transaction_date)->format('d M Y') }}</td>
                                <td>
                                    <div class="fw-bold">{{ $f->kategori }}</div>
                                    <small class="text-muted">{{ $f->description }}</small>
                                </td>
                                <td class="text-end text-success fw-bold">
                                    {{ $f->type === 'PEMASUKAN' ? number_format($f->amount, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-end text-danger fw-bold">
                                    {{ $f->type === 'PENGELUARAN' ? number_format($f->amount, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">Tidak ada transaksi pada periode ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="text-center text-muted small pb-4">
            Ini adalah tautan laporan yang diamankan menggunakan Signed URL Token. Valid selama 7 Hari. <br>
            &copy; 2026 Jostru Command Center.
        </div>
    </div>
</body>
</html>
