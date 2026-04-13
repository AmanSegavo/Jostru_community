@extends('layouts.admin')
@section('admin_content')
    <div class="animate-fade-in">
        <h2 class="mb-4">Pesan Masuk (Kontak)</h2>
        <div class="card p-4 glass">
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <th class="py-2">Tanggal</th>
                    <th class="py-2">Pengirim</th>
                    <th class="py-2">Email</th>
                    <th class="py-2">Pesan</th>
                </tr>
                @forelse($messages as $msg)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td class="py-2">{{ $msg->created_at->format('d M Y') }}</td>
                        <td class="py-2">{{ $msg->name }}</td>
                        <td class="py-2">{{ $msg->email }}</td>
                        <td class="py-2">{{ \Illuminate\Support\Str::limit($msg->message, 50) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-muted">Belum ada pesan masuk.</td>
                    </tr>
                @endforelse
            </table>
            <div class="mt-4">{{ $messages->links() }}</div>
        </div>
    </div>
@endsection