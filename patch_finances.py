import re

file_path = "d:/Jostru Community Sistem/Jostru_community/resources/views/admin/divisions/finances.blade.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Add 'Aksi' to the header
header_target = "<th>Oleh</th>\n                    </tr>"
header_replace = "<th>Oleh</th>\n                        <th class=\"text-center\">Aksi</th>\n                    </tr>"
content = content.replace(header_target, header_replace)

# 2. Add buttons to the row
row_target = "<td>{{ $finance->user->name ?? 'Admin' }}</td>\n                    </tr>"
row_replace = """<td>{{ $finance->user->name ?? 'Admin' }}</td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm" data-bs-toggle="modal" data-bs-target="#editFinanceModal{{ $finance->id }}" style="background:rgba(59,130,246,0.1); color:#3b82f6; border-radius:8px; border:none;" title="Edit Transaksi">✏️</button>
                                <form action="{{ route('admin.divisions.finances.destroy', [$division->id, $finance->id]) }}" method="POST" class="m-0" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm" style="background:rgba(239,68,68,0.1); color:#ef4444; border-radius:8px; border:none;" title="Hapus Transaksi">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>"""
content = content.replace(row_target, row_replace)

# 3. Fix the colspan for empty state
empty_target = '<td colspan="6" class="text-center py-5 text-muted">'
empty_replace = '<td colspan="7" class="text-center py-5 text-muted">'
content = content.replace(empty_target, empty_replace)

# 4. Add the Edit Modals at the end (before @endsection)
modal_template = """
<!-- Edit Finance Modals -->
@foreach($finances as $finance)
<div class="modal fade" id="editFinanceModal{{ $finance->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('admin.divisions.finances.update', [$division->id, $finance->id]) }}" method="POST" class="w-100">
            @csrf
            @method('PUT')
            <div class="modal-content glass-panel" style="border:none;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title" style="font-weight:800;">Edit Transaksi Divisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Tipe Transaksi</label>
                        <select name="type" class="form-control" required style="border-radius:12px;" id="financeTypeSelectEdit{{ $finance->id }}" onchange="toggleBudgetEdit{{ $finance->id }}(this.value)">
                            <option value="PEMASUKAN" {{ $finance->type == 'PEMASUKAN' ? 'selected' : '' }}>Pemasukan (Masuk)</option>
                            <option value="PENGELUARAN" {{ $finance->type == 'PENGELUARAN' ? 'selected' : '' }}>Pengeluaran (Keluar)</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="budgetSelectDivEdit{{ $finance->id }}" style="display: {{ $finance->type == 'PENGELUARAN' ? 'block' : 'none' }};">
                        <label class="form-label" style="font-weight:600;">Gunakan Dari Anggaran</label>
                        <select name="budget_id" class="form-control" style="border-radius:12px;">
                            <option value="">-- Tidak Terkait Anggaran --</option>
                            @foreach($budgets as $budget)
                                @php
                                    $sisa = $budget->allocated_amount - $budget->used_amount;
                                    // if this finance was using this budget, add it back to sisa
                                    if($finance->budget_id == $budget->id) {
                                        $sisa += $finance->amount;
                                    }
                                @endphp
                                @if($sisa > 0 || $finance->budget_id == $budget->id)
                                    <option value="{{ $budget->id }}" {{ $finance->budget_id == $budget->id ? 'selected' : '' }}>
                                        {{ $budget->description ?? 'Periode '.$budget->period }} 
                                        (Sisa Max: Rp {{ number_format($sisa, 0, ',', '.') }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                        <small class="text-muted mt-1 d-block">Pilih jika pengeluaran ini memotong anggaran yang telah dialokasikan pusat.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Jumlah (Rp)</label>
                        <input type="number" name="amount" class="form-control" required min="0" value="{{ $finance->amount }}" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Tanggal</label>
                        <input type="date" name="transaction_date" class="form-control" required value="{{ \Carbon\Carbon::parse($finance->transaction_date)->format('Y-m-d') }}" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Kategori</label>
                        <input type="text" name="kategori" class="form-control" value="{{ $finance->kategori }}" placeholder="Contoh: Operasional, Pembelian Bibit" style="border-radius:12px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" required style="border-radius:12px;">{{ $finance->description }}</textarea>
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
<script>
    function toggleBudgetEdit{{ $finance->id }}(type) {
        var budgetDiv = document.getElementById('budgetSelectDivEdit{{ $finance->id }}');
        if (type === 'PENGELUARAN') {
            budgetDiv.style.display = 'block';
        } else {
            budgetDiv.style.display = 'none';
            budgetDiv.querySelector('select').value = '';
        }
    }
</script>
@endforeach

"""

if "<!-- Edit Finance Modals -->" not in content:
    content = content.replace("</script>\n@endsection", "</script>\n" + modal_template + "@endsection")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Finances UI updated successfully.")
