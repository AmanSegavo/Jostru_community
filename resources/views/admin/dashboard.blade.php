@extends('layouts.admin')

@section('admin_content')
<div class="animate-fade-in">
    <h2>Overview Sistem</h2>
    <p class="text-muted mb-4">Informasi utama manajemen Jostru Community.</p>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card p-4 glass">
            <h3 class="text-sm uppercase text-muted">Total Anggota</h3>
            <p class="text-4xl mt-4" style="color: var(--primary);">{{\App\Models\User::where('role', 'member')->count()}}</p>
        </div>
        <div class="card p-4 glass">
            <h3 class="text-sm uppercase text-muted">Pesan Baru</h3>
            <p class="text-4xl mt-4" style="color: #0ea5e9;">{{\App\Models\Contact::where('is_read', false)->count()}}</p>
        </div>
        <div class="card p-4 glass">
            <h3 class="text-sm uppercase text-muted">Kartu Dicetak</h3>
            <p class="text-4xl mt-4" style="color: #22c55e;">{{\App\Models\MembershipCard::where('status', 'active')->count()}}</p>
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
