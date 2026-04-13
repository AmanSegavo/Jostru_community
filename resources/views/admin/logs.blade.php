@extends('layouts.admin')
@section('admin_content')
    <div class="animate-fade-in">
        <h2 class="mb-4">Log Aktivitas</h2>
        <div class="card p-4 glass">
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <th class="py-2">Waktu</th>
                    <th class="py-2">User</th>
                    <th class="py-2">Aktivitas</th>
                </tr>
                @forelse($logs as $log)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td class="py-2">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="py-2">{{ $log->user->name ?? 'Sistem' }}</td>
                        <td class="py-2">{{ $log->action ?? $log->description }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-muted">Belum ada log aktivitas.</td>
                    </tr>
                @endforelse
            </table>
            <div class="mt-4">{{ $logs->links() }}</div>
        </div>
    </div>
@endsection