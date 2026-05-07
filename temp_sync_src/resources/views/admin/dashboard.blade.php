@extends('layouts.admin')

@section('admin_content')
<div class="animate-fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>Overview Sistem</h2>
            <p class="text-muted">Informasi utama manajemen Jostru Community.</p>
        </div>
        <div>
            @if(Auth::user()->google_id)
                <span class="badge bg-success d-flex align-items-center gap-2 py-2 px-3" style="border-radius: 8px; font-weight: 500;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                    Google Tertaut
                </span>
            @else
                <a href="{{ route('auth.google') }}" class="btn glass d-flex align-items-center gap-2" style="border: 1px solid var(--border-color); font-size: 13px;">
                    <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 12-4.53z" fill="#EA4335"/></svg>
                    Tautkan Google
                </a>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card p-4 glass">
            <h3 class="text-sm uppercase text-muted">Total Anggota</h3>
            <p class="text-4xl mt-4" style="color: var(--primary);">{{\App\Models\User::where('role', 'member')->count()}}</p>
        </div>
        <div class="card p-4 glass" style="border-left: 4px solid #22c55e;">
            <h3 class="text-sm uppercase text-muted">Kontribusi Limbah</h3>
            <p class="text-4xl mt-4" style="color: #22c55e;">{{ number_format($totalWaste ?? 0, 1) }} <small style="font-size: 1.2rem;">kg</small></p>
        </div>
        <div class="card p-4 glass" style="border-left: 4px solid #f59e0b;">
            <h3 class="text-sm uppercase text-muted">Laporan Pending</h3>
            <p class="text-4xl mt-4" style="color: #f59e0b;">{{ $pendingWaste ?? 0 }}</p>
        </div>
        <div class="card p-4 glass" style="border-left: 4px solid #0ea5e9;">
            <h3 class="text-sm uppercase text-muted">Community Feed</h3>
            <p class="text-4xl mt-4" style="color: #0ea5e9;">{{ $totalPosts ?? 0 }}</p>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card p-4 glass">
            <h3 class="mb-4">Arus Kas 6 Bulan Terakhir</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="financeChart"></canvas>
            </div>
        </div>

        <div class="card p-4 glass">
            <h3 class="mb-4">Aktivitas Terakhir</h3>
            <div style="max-height: 300px; overflow-y: auto; padding-right: 10px;">
                @forelse($activities as $log)
                    <div style="border-bottom: 1px solid var(--border-color); padding: 10px 0;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                            <strong>{{ $log->action }}</strong>
                            <span class="text-muted text-sm">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-muted text-sm" style="margin: 0;">{{ $log->user->name ?? 'Sistem' }} - {{ $log->description }}</p>
                    </div>
                @empty
                    <p class="text-muted">Belum ada aktivitas yang tercatat untuk saat ini.</p>
                @endforelse
                @if($activities->count() > 0)
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="{{ route('admin.logs') }}" style="font-size: 14px; text-decoration: underline;">Lihat Semua Aktivitas</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financeChart').getContext('2d');
        const months = {!! json_encode($months) !!};
        const pemasukanData = {!! json_encode($pemasukanData) !!};
        const pengeluaranData = {!! json_encode($pengeluaranData) !!};

        // Get theme colors
        const isDark = document.body.getAttribute('data-theme') === 'dark';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        const textColor = isDark ? '#94a3b8' : '#475569';

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: months,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: pemasukanData,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: '#22c55e',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Pengeluaran',
                        data: pengeluaranData,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: gridColor },
                        ticks: {
                            color: textColor,
                            callback: function(value) {
                                if (value >= 1000000) return 'Rp ' + (value / 1000000).toFixed(1) + ' Jt';
                                if (value >= 1000) return 'Rp ' + (value / 1000).toFixed(0) + ' Rb';
                                return 'Rp ' + value;
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: textColor }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: textColor }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
