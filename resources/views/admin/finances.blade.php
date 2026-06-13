@extends('layouts.admin')
@section('admin_content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    /* CSS Print khusus Laporan Akuntansi */
    @media print {
        body * { visibility: hidden; }
        #exportable-area, #exportable-area * { visibility: visible; }
        #exportable-area { position: absolute; left: 0; top: 0; width: 100%; }
        .data-no-export { display: none !important; }
        .glass { background: white !important; border: 1px solid #ddd !important; box-shadow: none !important; }
        .table { border-collapse: collapse !important; width: 100% !important; }
        .table th, .table td { border: 1px solid #000 !important; color: #000 !important; padding: 4px !important; font-size: 11pt !important; }
        .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
    }
    
    .print-header { display: none; }
    
    .finance-card {
        border-radius: 16px;
        transition: transform 0.2s;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        background: var(--surface-color);
    }
    .finance-card:hover {
        transform: translateY(-5px);
    }
    
    .table-accounting th {
        background: rgba(34, 197, 94, 0.1) !important;
        color: var(--text-primary);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #22c55e;
    }
    
    .table-accounting td {
        vertical-align: middle;
        font-size: 0.95rem;
    }
    
    .filter-wrapper {
        background: var(--surface-color);
        border-radius: 12px;
        padding: 15px;
        border: 1px solid var(--border-color);
        box-shadow: 0 2px 10px rgba(0,0,0,0.02);
    }
</style>

<div class="animate-fade-in force-landscape">
    <!-- Overlay Landscape Enforcement -->
    <div class="landscape-overlay d-flex d-md-none flex-column justify-content-center align-items-center text-center p-4">
        <i class="bi bi-phone-landscape" style="font-size: 4rem; color: #fff; margin-bottom: 20px;"></i>
        <h3 class="text-white fw-bold">Wajib Layar Horizontal</h3>
        <p class="text-white-50">Halaman Laporan Keuangan ini memuat tabel yang lebar. Silakan putar HP Anda ke mode Horizontal (Landscape) atau buka melalui PC/Laptop untuk pengalaman terbaik.</p>
    </div>

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 style="font-weight:800; background: linear-gradient(135deg, var(--primary), #3b82f6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Buku Kas Umum (Laporan Keuangan)
            </h2>
            <p class="text-muted mb-0">Sistem pencatatan akuntansi untuk arus kas masuk (Debet) dan keluar (Kredit).</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <div class="dropdown">
                <button class="btn hover-lift" style="background:rgba(34,197,94,0.1); color:var(--primary-accent); border:1px solid rgba(34,197,94,0.3); padding:0.5rem 1rem; border-radius:12px; font-weight:600;" data-bs-toggle="dropdown">
                    ⬇️ Export Laporan
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:12px;">
                    <li><a class="dropdown-item py-2" href="#" onclick="exportToExcel()"><i class="bi bi-file-earmark-excel text-success me-2"></i>Export Tabel (Excel)</a></li>
                    <li><a class="dropdown-item py-2" href="#" onclick="window.print()"><i class="bi bi-printer text-danger me-2"></i>Cetak Dokumen Resmi (Print/PDF)</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item py-2" href="#" onclick="shareFinanceToWA()"><i class="bi bi-whatsapp text-success me-2"></i>Bagikan Laporan (WhatsApp)</a></li>
                </ul>
            </div>
            
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#budgetModal" style="border-radius:12px; font-weight:600;">
                💸 Alokasi Budget
            </button>
            <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#debtModal" style="border-radius:12px; font-weight:600; border:none;">
                🤝 Entri Hutang/Piutang
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#financeModal" style="border-radius:12px; font-weight:600; border:none;">
                + Entri Jurnal Kas
            </button>
        </div>
    </div>

    <!-- Nav Tabs -->
    <ul class="nav nav-pills mb-4" id="financeTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="kas-tab" data-bs-toggle="pill" data-bs-target="#kas" type="button" role="tab" style="border-radius: 12px; font-weight: 600;">Buku Kas Umum</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="hutang-tab" data-bs-toggle="pill" data-bs-target="#hutang" type="button" role="tab" style="border-radius: 12px; font-weight: 600; margin-left: 10px;">Buku Hutang & Piutang</button>
        </li>
    </ul>

    <div class="tab-content" id="financeTabsContent">
        <!-- TAB KAS UMUM -->
        <div class="tab-pane fade show active" id="kas" role="tabpanel">

    <!-- Exportable Area -->
    <div id="exportable-area">
        
        <div class="print-header">
            <h2>BUKU KAS UMUM - JOSTRU COMMUNITY</h2>
            <p>Periode: {{ request('start_date') ? date('d M Y', strtotime(request('start_date'))) : 'Awal' }} s.d {{ request('end_date') ? date('d M Y', strtotime(request('end_date'))) : date('d M Y') }}</p>
            <hr>
        </div>

        <!-- Executive Summary Cards -->
        <div class="row g-3 mb-4 data-no-export">
            <div class="col-md-4">
                <div class="card finance-card p-4 h-100" style="border-left:5px solid #22c55e;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small fw-bold text-uppercase">Total Debet (Pemasukan)</div>
                        <div class="bg-success bg-opacity-10 text-success p-2 rounded-circle"><i class="bi bi-graph-up-arrow"></i></div>
                    </div>
                    <div class="fs-3 fw-bold text-success">Rp {{ number_format($totalPemasukan, 0, ',', '.') }}</div>
                    @php 
                        $growth = $lastMonthPemasukan > 0 ? (($thisMonthPemasukan - $lastMonthPemasukan) / $lastMonthPemasukan) * 100 : 100;
                        $growthColor = $growth >= 0 ? 'text-success' : 'text-danger';
                        $growthIcon = $growth >= 0 ? 'bi-arrow-up-right' : 'bi-arrow-down-right';
                    @endphp
                    <div class="mt-2 text-muted small">
                        <span class="{{ $growthColor }} fw-bold"><i class="bi {{ $growthIcon }}"></i> {{ number_format(abs($growth), 1) }}%</span> vs Bulan Lalu
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card finance-card p-4 h-100" style="border-left:5px solid #ef4444;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small fw-bold text-uppercase">Total Kredit (Pengeluaran)</div>
                        <div class="bg-danger bg-opacity-10 text-danger p-2 rounded-circle"><i class="bi bi-graph-down-arrow"></i></div>
                    </div>
                    <div class="fs-3 fw-bold text-danger">Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                    <div class="mt-2 text-muted small">Alokasi operasional & kegiatan</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card finance-card p-4 h-100" style="border-left:5px solid {{ $saldo >= 0 ? '#3b82f6' : '#ef4444' }}; background: {{ $saldo >= 0 ? 'linear-gradient(135deg, var(--surface-color), rgba(59,130,246,0.05))' : 'linear-gradient(135deg, var(--surface-color), rgba(239,68,68,0.05))' }};">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="text-muted small fw-bold text-uppercase">Saldo Kas Tersedia</div>
                        <div class="bg-primary bg-opacity-10 text-primary p-2 rounded-circle"><i class="bi bi-wallet2"></i></div>
                    </div>
                    <div class="fs-3 fw-bold {{ $saldo >= 0 ? 'text-primary' : 'text-danger' }}">
                        Rp {{ number_format($saldo, 0, ',', '.') }}
                    </div>
                    <div class="mt-2 fw-semibold" style="font-size:0.85rem; color: {{ $saldo >= 0 ? '#3b82f6' : '#ef4444' }};">
                        Posisi Keuangan: {{ $saldo >= 0 ? 'SURPLUS' : 'DEFISIT' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Analytics (Sistem Pakar) -->
        <div class="row mb-4 data-no-export">
            <div class="col-md-6">
                <div class="card glass p-3" style="border-radius:16px;">
                    <h6 class="fw-bold text-center mb-3" style="color: var(--text-primary);">Komposisi Pemasukan</h6>
                    <div style="height: 250px; position: relative;">
                        <canvas id="pemasukanChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card glass p-3" style="border-radius:16px;">
                    <h6 class="fw-bold text-center mb-3" style="color: var(--text-primary);">Komposisi Pengeluaran</h6>
                    <div style="height: 250px; position: relative;">
                        <canvas id="pengeluaranChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Jurnal / Buku Kas -->
        <div class="card glass p-0 overflow-hidden mb-4" style="border-radius:20px;">
            
            <!-- Filter & Toolbar -->
            <div class="card-header bg-transparent border-bottom p-4 data-no-export">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="m-0 fw-bold" style="color: var(--text-primary);">Buku Jurnal Umum</h5>
                    <span class="badge bg-success">Sistem Akuntansi Aktif</span>
                </div>
                
                <div class="filter-wrapper">
                    <form action="{{ route('admin.finances') }}" method="GET" class="row g-2 align-items-center" id="filterForm">
                        <div class="col-md-auto">
                            <label class="small text-muted mb-1">Dari Tanggal</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-auto">
                            <label class="small text-muted mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="small text-muted mb-1">Kas Divisi</label>
                            <select name="division_id" class="form-select form-select-sm">
                                <option value="">Semua Kas (Pusat & Divisi)</option>
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md">
                            <label class="small text-muted mb-1">Pencarian Uraian</label>
                            <div class="input-group input-group-sm">
                                <input type="text" name="search" class="form-control" placeholder="Cari urian / keterangan..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                            </div>
                        </div>
                        <div class="col-md-auto d-flex align-items-end mt-4">
                            @if(request('start_date') || request('end_date') || request('division_id') || request('search'))
                                <a href="{{ route('admin.finances') }}" class="btn btn-sm btn-outline-secondary w-100">Reset Filter</a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle table-accounting" id="financeTable">
                    <thead>
                        <tr>
                            <th class="px-4 py-3" style="width:12%">Tanggal</th>
                            <th class="px-4 py-3" style="width:15%">Referensi / Akun</th>
                            <th class="px-4 py-3" style="width:25%">Uraian Transaksi</th>
                            <th class="px-4 py-3 text-end" style="width:15%">Debet (Rp)</th>
                            <th class="px-4 py-3 text-end" style="width:15%">Kredit (Rp)</th>
                            <th class="px-4 py-3 text-end data-no-export" style="width:18%">Opsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Saldo Awal (Visualizer) -->
                        <tr class="bg-light data-no-export">
                            <td colspan="3" class="px-4 py-2 text-end fw-bold text-muted">SALDO BERJALAN:</td>
                            <td colspan="2" class="px-4 py-2 text-end fw-bold text-primary">
                                Rp {{ number_format($saldo, 0, ',', '.') }}
                            </td>
                            <td></td>
                        </tr>

                        @forelse($finances as $finance)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="fw-bold" style="color: var(--text-primary);">{{ $finance->transaction_date ? \Carbon\Carbon::parse($finance->transaction_date)->format('d/m/Y') : '-' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @if($finance->division)
                                    <span class="badge bg-secondary mb-1">{{ $finance->division->name }}</span><br>
                                @else
                                    <span class="badge bg-dark mb-1">KAS PUSAT</span><br>
                                @endif
                                <small class="text-muted fw-semibold">{{ $finance->kategori ?? 'Umum' }}</small>
                            </td>
                            <td class="px-4 py-3">
                                <div style="color: var(--text-primary);">{{ $finance->description }}</div>
                                @if($finance->budget_id)
                                    <span class="badge bg-info text-white mt-1" style="font-size:0.7rem;">#BUDGET</span>
                                @endif
                                <div class="print-header small text-muted">
                                    {{ $finance->type == 'PEMASUKAN' ? '(Penerimaan Kas)' : '(Pengeluaran Kas)' }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-end fw-bold text-success" style="font-family: monospace; font-size:1.05rem;">
                                {{ $finance->type == 'PEMASUKAN' ? number_format($finance->amount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-end fw-bold text-danger" style="font-family: monospace; font-size:1.05rem;">
                                {{ $finance->type == 'PENGELUARAN' ? number_format($finance->amount, 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-end data-no-export">
                                @if(is_array($finance->proofs) && count($finance->proofs) > 0)
                                    <div class="d-flex flex-wrap gap-1 justify-content-end mb-2">
                                        @foreach($finance->proofs as $index => $proof)
                                            <a href="{{ asset('public/storage/' . $proof) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill py-0 px-2" style="font-size:0.75rem;">B{{ $index + 1 }}</a>
                                        @endforeach
                                    </div>
                                @elseif($finance->proof_path)
                                    <div class="d-flex justify-content-end mb-2">
                                        <a href="{{ asset('public/storage/' . $finance->proof_path) }}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill py-0 px-2" style="font-size:0.75rem;">Lihat Bukti</a>
                                    </div>
                                @endif
                                
                                <div class="d-flex flex-wrap gap-2 justify-content-end">
                                    <button type="button" class="btn btn-sm btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#editFinanceModal{{ $finance->id }}" style="border-radius:8px; padding: 0.3rem 0.75rem;" title="Edit Jurnal">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg> Edit
                                    </button>
                                    <form action="{{ route('admin.finances.destroy', $finance->id) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurnal ini? Saldo berjalan akan otomatis terkoreksi.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger fw-bold shadow-sm" style="border-radius:8px; padding: 0.3rem 0.75rem;" title="Hapus Jurnal">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📭</div>
                                <h5 style="font-weight: 700; color: var(--text-secondary);">Buku Kas Kosong</h5>
                                <p class="text-muted small mb-0">Belum ada entri jurnal yang tercatat untuk filter ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 border-top data-no-export">
                {{ $finances->links() }}
            </div>
        </div>
    </div> <!-- End Exportable Area -->
        </div> <!-- End Tab Kas Umum -->

        <!-- TAB HUTANG PIUTANG -->
        <div class="tab-pane fade" id="hutang" role="tabpanel">
            <div class="card glass p-0 overflow-hidden mb-4" style="border-radius:20px;">
                <div class="card-header bg-transparent border-bottom p-4 data-no-export">
                    <h5 class="m-0 fw-bold" style="color: var(--text-primary);">Buku Hutang & Piutang</h5>
                    <p class="text-muted small mb-0">Catatan pinjaman dana organisasi atau pinjaman yang diberikan ke pihak lain.</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle table-accounting">
                        <thead>
                            <tr>
                                <th class="px-4 py-3" style="width:12%">Jatuh Tempo</th>
                                <th class="px-4 py-3" style="width:15%">Nama / Pihak</th>
                                <th class="px-4 py-3" style="width:20%">Keterangan</th>
                                <th class="px-4 py-3 text-center" style="width:10%">Jenis</th>
                                <th class="px-4 py-3 text-end" style="width:15%">Nominal (Rp)</th>
                                <th class="px-4 py-3 text-end" style="width:15%">Sisa Tagihan</th>
                                <th class="px-4 py-3 text-end data-no-export" style="width:13%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($debts ?? [] as $debt)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="fw-bold" style="color: var(--text-primary);">{{ $debt->due_date ? \Carbon\Carbon::parse($debt->due_date)->format('d/m/Y') : '-' }}</div>
                                    @if($debt->status == 'BELUM LUNAS' && $debt->due_date && \Carbon\Carbon::parse($debt->due_date)->isPast())
                                        <span class="badge bg-danger mt-1" style="font-size: 0.65rem;">JATUH TEMPO!</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="fw-bold" style="color: var(--text-primary);">{{ $debt->creditor_name }}</div>
                                    @if($debt->member)
                                        <span class="badge bg-primary mt-1" style="font-size: 0.65rem;">Anggota: {{ $debt->member->member_id }}</span>
                                    @else
                                        <span class="badge bg-secondary mt-1" style="font-size: 0.65rem;">Pihak Luar</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div style="color: var(--text-primary); font-size: 0.9rem;">{{ $debt->description }}</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($debt->type == 'HUTANG')
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">HUTANG</span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">PIUTANG</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end fw-bold" style="font-family: monospace; font-size:1.05rem; color: var(--text-primary);">
                                    {{ number_format($debt->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-end">
                                    @if($debt->status == 'LUNAS')
                                        <span class="badge bg-success mb-1">LUNAS</span><br>
                                    @else
                                        <div class="fw-bold text-danger" style="font-family: monospace; font-size:1.05rem;">
                                            {{ number_format($debt->remaining_amount, 0, ',', '.') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-end data-no-export">
                                    <div class="d-flex flex-wrap gap-2 justify-content-end">
                                        @if($debt->status != 'LUNAS')
                                            <button type="button" class="btn btn-sm btn-success fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#payDebtModal-{{ $debt->id }}" style="border-radius:8px; padding: 0.3rem 0.75rem;" title="Bayar">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg> Bayar
                                            </button>
                                        @endif
                                        <form action="/admin/finances/debts/{{ $debt->id }}" method="POST" class="m-0" onsubmit="return confirm('Hapus data ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger fw-bold shadow-sm" style="border-radius:8px; padding: 0.3rem 0.75rem;" title="Hapus">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg> Hapus
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Pay Modal -->
                                    <div class="modal fade" id="payDebtModal-{{ $debt->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered text-start">
                                            <form action="/admin/finances/debts/{{ $debt->id }}/pay" method="POST" class="w-100">
                                                @csrf
                                                <div class="modal-content glass" style="border-radius:24px;">
                                                    <div class="modal-header border-0 pb-0">
                                                        <h5 class="modal-title" style="font-weight:800; color: var(--text-primary);">Bayar Cicilan/Lunas</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body" style="color: var(--text-primary);">
                                                        <p class="mb-3">Pembayaran akan otomatis memotong/menambah Saldo Kas Umum.</p>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Nominal Pembayaran (Rp)</label>
                                                            <input type="number" name="pay_amount" class="form-control" max="{{ $debt->remaining_amount }}" required placeholder="Maks: {{ $debt->remaining_amount }}">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Tanggal Transaksi Kas</label>
                                                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 pt-0">
                                                        <button type="submit" class="btn btn-success w-100" style="border-radius:12px;">Konfirmasi Pembayaran</button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">🤝</div>
                                    <h5 style="font-weight: 700; color: var(--text-secondary);">Tidak Ada Data Hutang/Piutang</h5>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-3 border-top data-no-export">
                    @if(isset($debts))
                        {{ $debts->links() }}
                    @endif
                </div>
            </div>
        </div> <!-- End Tab Hutang -->
    </div> <!-- End Tab Content -->
</div> <!-- End Animate -->

<!-- Modal Entri Jurnal DIPINDAHKAN KELUAR AGAR TIDAK STUCK BANYANGAN -->
<div class="modal fade" id="financeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.finances.store') }}" method="POST" enctype="multipart/form-data" class="w-100">
            @csrf
            <div class="modal-content glass" style="border-radius:24px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight:800; color: var(--text-primary);">Tambah Entri Jurnal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="color: var(--text-primary);">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Jenis Jurnal</label>
                            <select name="type" class="form-select" required>
                                <option value="PEMASUKAN">Debet (Pemasukan)</option>
                                <option value="PENGELUARAN">Kredit (Pengeluaran)</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Tanggal</label>
                            <input type="date" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Kategori / Akun</label>
                        <input type="text" name="kategori" class="form-control" placeholder="Contoh: Kas Kecil, Biaya Operasional, dll" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Divisi (Opsional)</label>
                        <select name="division_id" class="form-select">
                            <option value="">-- Kas Pusat --</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nominal (Rp)</label>
                        <input type="number" name="amount" class="form-control" required placeholder="Contoh: 500000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Uraian Transaksi</label>
                        <textarea name="description" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bukti / Lampiran (Bisa > 1 foto)</label>
                        <input type="file" name="proofs[]" multiple class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius:12px;">Posting Jurnal</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Budget DIPINDAHKAN KELUAR -->
<div class="modal fade" id="budgetModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.budgets.store') }}" method="POST" class="w-100">
            @csrf
            <div class="modal-content glass" style="border-radius:24px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight:800; color: var(--text-primary);">Alokasi Budget Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="color: var(--text-primary);">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Divisi Penerima</label>
                        <select name="division_id" class="form-select" required>
                            <option value="">Pilih Divisi...</option>
                            @foreach($divisions as $div)
                                <option value="{{ $div->id }}">{{ $div->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bulan & Tahun</label>
                        <div class="row">
                            <div class="col-6">
                                <select name="month" class="form-select" required>
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>{{ date('F', mktime(0,0,0,$i,1)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="number" name="year" class="form-control" value="{{ date('Y') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Total Budget (Rp)</label>
                        <input type="number" name="total_budget" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan Khusus</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius:12px;">Simpan Budget</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hutang/Piutang Baru -->
<div class="modal fade" id="debtModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="/admin/finances/debts" method="POST" class="w-100">
            @csrf
            <div class="modal-content glass" style="border-radius:24px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight:800; color: var(--text-primary);">Entri Hutang / Piutang Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" style="color: var(--text-primary);">
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Jenis</label>
                            <select name="type" class="form-select" required>
                                <option value="HUTANG">HUTANG (Kita Pinjam)</option>
                                <option value="PIUTANG">PIUTANG (Kita Beri Pinjam)</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Jatuh Tempo</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Pihak (Kreditur/Debitur)</label>
                        <input type="text" name="creditor_name" class="form-control" placeholder="Nama orang / instansi" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Relasi Anggota (Opsional)</label>
                        <select name="member_id" class="form-select">
                            <option value="">-- Bukan Anggota --</option>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->jabatan ?? 'Anggota' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nominal Total (Rp)</label>
                        <input type="number" name="amount" class="form-control" required placeholder="Contoh: 1000000">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan / Tujuan</label>
                        <textarea name="description" class="form-control" rows="2" required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="submit" class="btn btn-warning text-white w-100" style="border-radius:12px;">Simpan Catatan</button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    /* Landscape Enforcement Overlay CSS */
    .landscape-overlay {
        display: none !important;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.95);
        backdrop-filter: blur(10px);
        z-index: 9999;
    }
    @media screen and (max-width: 768px) and (orientation: portrait) {
        .force-landscape > * {
            display: none !important;
        }
        .force-landscape > .landscape-overlay {
            display: flex !important;
        }
    }
</style>

<script>
    // Data untuk Chart dari Controller
    const inData = @json($pemasukanPerKategori);
    const outData = @json($pengeluaranPerKategori);

    // Format Data untuk Pemasukan Chart
    const inLabels = inData.map(item => item.kategori || 'Lainnya');
    const inValues = inData.map(item => item.total);
    
    // Format Data untuk Pengeluaran Chart
    const outLabels = outData.map(item => item.kategori || 'Lainnya');
    const outValues = outData.map(item => item.total);

    document.addEventListener('DOMContentLoaded', function() {
        if (inLabels.length > 0) {
            new Chart(document.getElementById('pemasukanChart'), {
                type: 'doughnut',
                data: {
                    labels: inLabels,
                    datasets: [{
                        data: inValues,
                        backgroundColor: ['#22c55e', '#3b82f6', '#14b8a6', '#f59e0b', '#8b5cf6'],
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });
        }
        
        if (outLabels.length > 0) {
            new Chart(document.getElementById('pengeluaranChart'), {
                type: 'doughnut',
                data: {
                    labels: outLabels,
                    datasets: [{
                        data: outValues,
                        backgroundColor: ['#ef4444', '#f97316', '#eab308', '#6366f1', '#ec4899'],
                        borderWidth: 0
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'right' } } }
            });
        }
    });

    function exportToExcel() {
        var table = document.getElementById("financeTable");
        var cloneTable = table.cloneNode(true);
        var noExportElems = cloneTable.querySelectorAll('.data-no-export');
        noExportElems.forEach(function(el) { el.remove(); });
        
        var wb = XLSX.utils.table_to_book(cloneTable, {sheet: "Buku Kas Umum"});
        XLSX.writeFile(wb, "Laporan_Keuangan_Jostru.xlsx");
    }

    function shareFinanceToWA() {
        let text = `*📊 LAPORAN KEUANGAN JOSTRU*\n*Buku Kas Umum & Sistem Akuntansi*\n--------------------------------\n`;
        text += `*Periode:* {{ request('start_date') ?: 'Awal' }} s.d {{ request('end_date') ?: 'Sekarang' }}\n`;
        text += `*Divisi:* {{ request('division_id') ? \App\Models\Division::find(request('division_id'))->name : 'GLOBAL (Semua Divisi)' }}\n\n`;
        
        text += `*💰 SALDO TERSEDIA:* Rp {{ number_format($saldo, 0, ',', '.') }} ({{ $saldo >= 0 ? 'SURPLUS' : 'DEFISIT' }})\n`;
        text += `*📈 Total Pemasukan:* Rp {{ number_format($totalPemasukan, 0, ',', '.') }}\n`;
        text += `*📉 Total Pengeluaran:* Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}\n\n`;
        
        text += `_Cetak Laporan Lengkap melalui Web Jostru_\n\n`;
        
        @php
            $signedUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute('shared.report.finance', now()->addDays(7), [
                'start_date' => request('start_date'),
                'end_date' => request('end_date'),
                'division_id' => request('division_id')
            ]);
        @endphp
        
        text += `🔗 *Akses Laporan Penuh:*\n{!! $signedUrl !!}\n_(Link ini bersifat aman dan eksklusif)_`;

        window.open(`https://api.whatsapp.com/send?text=${encodeURIComponent(text)}`, '_blank');
    }
</script>

<!-- Edit Finance Modals (Kas Pusat) -->
@foreach($finances as $finance)
<div class="modal fade" id="editFinanceModal{{ $finance->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.finances.update', $finance->id) }}" method="POST" enctype="multipart/form-data" class="w-100">
            @csrf
            @method('PUT')
            <div class="modal-content glass-panel" style="border:none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight:800;">Edit Transaksi Kas Pusat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Jenis Transaksi</label>
                        <select name="type" class="form-control" required style="border-radius:12px;">
                            <option value="PEMASUKAN" {{ $finance->type == 'PEMASUKAN' ? 'selected' : '' }}>Pemasukan</option>
                            <option value="PENGELUARAN" {{ $finance->type == 'PENGELUARAN' ? 'selected' : '' }}>Pengeluaran</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Tanggal</label>
                        <input type="date" name="transaction_date" class="form-control" required value="{{ \Carbon\Carbon::parse($finance->transaction_date)->format('Y-m-d') }}" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Kategori</label>
                        <input type="text" name="kategori" class="form-control" value="{{ $finance->kategori }}" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Tujuan RAB (Opsional)</label>
                        <select name="rab_id" class="form-control" style="border-radius:12px;">
                            <option value="">-- Pilih RAB (Jika Alokasi) --</option>
                            @foreach($approvedRabs as $arab)
                                <option value="{{ $arab->id }}" {{ $finance->rab_id == $arab->id ? 'selected' : '' }}>{{ $arab->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Keterangan</label>
                        <textarea name="description" class="form-control" rows="3" required style="border-radius:12px;">{{ $finance->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Nominal (Rp)</label>
                        <input type="number" name="amount" class="form-control" required min="0" value="{{ $finance->amount }}" style="border-radius:12px;">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:12px; font-weight:600;">Simpan Perubahan</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection