@extends('layouts.admin')

@section('title', 'Manajemen Hasil Produksi - Jostru')

@section('admin_content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold mb-1">Manajemen Hasil Produksi (V1.2)</h2>
            <p class="text-muted">Catat hasil olahan limbah menjadi produk siap jual.</p>
        </div>
        <!-- PERBAIKAN: Tombol sekarang menghapus 'hidden' dan menambahkan 'flex' agar modal muncul -->
        <button
            onclick="const m = document.getElementById('modal-tambah'); m.classList.remove('hidden'); m.classList.add('flex');"
            class="btn btn-primary">
            + Tambah Produksi
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="glass p-6 rounded-2xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-gray-200">
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600">ID</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600">Kode SKU</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600">Kuantitas</th>
                        <!-- Kolom kuantitas yang dobel sudah dihapus -->
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600">Harga Satuan</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600">Sumber Limbah</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600">Tanggal Produksi</th>
                        <th class="py-3 px-4 text-sm font-semibold text-gray-600 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($productions as $batch)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 font-medium text-gray-800">#{{ $batch->id }}</td>
                            <td class="py-3 px-4"><span
                                    class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-bold">{{ $batch->product_sku }}</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-gray-700">{{ $batch->quantity_produced }} Unit</td>
                            <!-- Data kuantitas yang dobel sudah dihapus -->
                            <td class="py-3 px-4 text-green-600 font-medium">Rp {{ number_format($batch->price, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">
                                @if($batch->sourceWaste)
                                    ID L-{{ $batch->sourceWaste->id }} ({{ $batch->sourceWaste->type }})<br>
                                    <small class="text-xs text-gray-400">Dari:
                                        {{ $batch->sourceWaste->user->name ?? 'Anonim' }}</small>
                                @else
                                    <span class="text-gray-400 italic">Bahan Baku Luar</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">{{ $batch->produced_at->format('d M Y') }}</td>
                            <td class="py-3 px-4 text-right">
                                <form action="{{ route('admin.productions.destroy', $batch->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Hapus catatan produksi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-gray-500">Belum ada data hasil produksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $productions->links() }}
        </div>
    </div>

    <!-- Modal Tambah Produksi -->
    <!-- PERBAIKAN: Class 'flex' dihilangkan dari bawaan agar 'hidden' berfungsi sempurna -->
    <div id="modal-tambah" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
        <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden shadow-2xl">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Catat Hasil Produksi Baru</h3>
                <!-- PERBAIKAN LOGIKA TOMBOL TUTUP -->
                <button type="button"
                    onclick="const m = document.getElementById('modal-tambah'); m.classList.add('hidden'); m.classList.remove('flex');"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.productions.store') }}" method="POST" class="p-6">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kode SKU Produk <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="product_sku" required
                        class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                        placeholder="Misal: PUPUK-KOMPOS-01">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kuantitas Dihasilkan (Unit/Kg) <span
                            class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="quantity_produced" required
                        class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                        placeholder="0.00">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan (Rp) <span
                            class="text-red-500">*</span></label>
                    <input type="number" name="price" required
                        class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50"
                        placeholder="Contoh: 15000">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sumber Limbah (Opsional)</label>
                    <select name="source_waste_id"
                        class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                        <option value="">-- Bukan dari limbah tersimpan --</option>
                        @foreach($approvedWastes as $waste)
                            <option value="{{ $waste->id }}">ID: L-{{ $waste->id }} | {{ $waste->type }}
                                ({{ $waste->weight }}Kg) - {{ $waste->user->name ?? 'Anonim' }}</option>
                        @endforeach
                    </select>
                    <small class="text-gray-500 mt-1 block">Jika dipilih, status limbah akan berubah menjadi
                        "PROCESSED".</small>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Produksi <span
                            class="text-red-500">*</span></label>
                    <input type="date" name="produced_at" required value="{{ date('Y-m-d') }}"
                        class="w-full rounded-lg border-gray-300 focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <!-- PERBAIKAN LOGIKA TOMBOL BATAL -->
                    <button type="button"
                        onclick="const m = document.getElementById('modal-tambah'); m.classList.add('hidden'); m.classList.remove('flex');"
                        class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 font-medium transition-colors">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 text-white bg-primary rounded-lg shadow hover:bg-primary-dark font-medium transition-colors">Simpan
                        Produksi</button>
                </div>
            </form>
        </div>
    </div>
@endsection