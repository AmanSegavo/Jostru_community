import re

file_path = "d:/Jostru Community Sistem/Jostru_community/resources/views/admin/finances.blade.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Add Edit button to table rows for Kas Pusat
row_target = """<form action="{{ route('admin.finances.destroy', $finance->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0" title="Hapus"><i class="fas fa-trash"></i> 🗑️</button>
                                    </form>"""
row_replace = """<button class="btn btn-sm btn-link text-primary p-0 me-2" data-bs-toggle="modal" data-bs-target="#editFinanceModal{{ $finance->id }}" title="Edit">✏️</button>
                                    <form action="{{ route('admin.finances.destroy', $finance->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-link text-danger p-0" title="Hapus">🗑️</button>
                                    </form>"""

content = content.replace(row_target, row_replace)

# 2. Add Modal template at the bottom
modal_template = """
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
                        <input type="date" name="transaction_date" class="form-control" required value="{{ \\Carbon\\Carbon::parse($finance->transaction_date)->format('Y-m-d') }}" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Kategori</label>
                        <input type="text" name="kategori" class="form-control" value="{{ $finance->kategori }}" style="border-radius:12px;">
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
"""

if "<!-- Edit Finance Modals (Kas Pusat) -->" not in content:
    content = content.replace("@endsection", modal_template + "\n@endsection")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Kas Pusat patched successfully.")
