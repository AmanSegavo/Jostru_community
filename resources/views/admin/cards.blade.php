@extends('layouts.admin')
@section('admin_content')
    <div class="animate-fade-in">
        <h2 class="mb-4">Manajemen Kartu Digital</h2>
        <div class="card p-4 glass">
            <table style="width: 100%; text-align: left; border-collapse: collapse;">
                <tr style="border-bottom: 1px solid var(--border-color);">
                    <th class="py-2">Pemilik</th>
                    <th class="py-2">Nomor Kartu</th>
                    <th class="py-2">Status</th>
                    <th class="py-2">Berlaku Hingga</th>
                </tr>
                @forelse($cards as $card)
                    <tr style="border-bottom: 1px solid var(--border-color);">
                        <td class="py-2">{{ $card->user->name ?? '-' }}</td>
                        <td class="py-2">{{ $card->card_number }}</td>
                        <td class="py-2">{{ $card->status }}</td>
                        <td class="py-2">{{ $card->valid_until ? $card->valid_until->format('d M Y') : '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-muted">Belum ada kartu dicetak.</td>
                    </tr>
                @endforelse
            </table>
            <div class="mt-4">{{ $cards->links() }}</div>
        </div>
    </div>
@endsection