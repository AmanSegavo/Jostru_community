import re

file_path = "d:/Jostru Community Sistem/Jostru_community/resources/views/admin/rabs.blade.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Add Edit/Delete buttons to table rows
row_target = """<button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailRabModal{{ $rab->id }}" title="Detail RAB">📄</button>
                                </td>"""
row_replace = """<button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailRabModal{{ $rab->id }}" title="Detail RAB">📄</button>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editRabModal{{ $rab->id }}" title="Edit RAB">✏️</button>
                                    <form action="{{ route('admin.rabs.destroy', $rab->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengajuan RAB ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus RAB">🗑️</button>
                                    </form>
                                </td>"""

content = content.replace(row_target, row_replace)

# 2. Add Edit Modal Template
modal_template = """
<!-- Edit RAB Modals -->
@foreach($rabs as $rab)
<div class="modal fade" id="editRabModal{{ $rab->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content glass-panel" style="border:none;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" style="font-weight:800;">Edit Pengajuan RAB</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.rabs.update', $rab->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Judul Pengajuan</label>
                            <input type="text" name="title" class="form-control" value="{{ $rab->title }}" required style="border-radius:12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Divisi Pemohon</label>
                            <select name="division_id" class="form-select" required style="border-radius:12px;">
                                @foreach($divisions as $div)
                                    <option value="{{ $div->id }}" {{ $rab->division_id == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-bold">Keterangan Singkat</label>
                        <textarea name="description" class="form-control" rows="2" style="border-radius:12px;">{{ $rab->description }}</textarea>
                    </div>

                    <h6 class="fw-bold text-success border-bottom pb-2 mb-3">Detail Item RAB</h6>
                    <div id="rabItemsContainerEdit{{ $rab->id }}">
                        @foreach($rab->items as $index => $item)
                        <div class="row align-items-center mb-2 rab-item-row-edit">
                            <div class="col-md-5">
                                <input type="text" name="items[{{ $index }}][name]" class="form-control form-control-sm" value="{{ $item->name }}" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[{{ $index }}][qty]" class="form-control form-control-sm" value="{{ $item->qty }}" required min="1">
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="items[{{ $index }}][price]" class="form-control form-control-sm" value="{{ $item->unit_price }}" required min="0">
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-sm btn-danger remove-item-edit" {{ $index == 0 ? 'style=display:none;' : '' }}>&times;</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success mt-2" onclick="addRabItemEdit({{ $rab->id }}, {{ $rab->items->count() }})">+ Tambah Item Lainnya</button>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" style="border-radius:12px;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="border-radius:12px;background:#22c55e;border:none;">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    function addRabItemEdit(rabId, startIndex) {
        // Find current max index for this modal
        let container = document.getElementById('rabItemsContainerEdit' + rabId);
        let rows = container.querySelectorAll('.rab-item-row-edit');
        let index = startIndex + rows.length; // ensures uniqueness
        
        const row = document.createElement('div');
        row.className = 'row align-items-center mb-2 rab-item-row-edit';
        row.innerHTML = `
            <div class="col-md-5">
                <input type="text" name="items[${index}][name]" class="form-control form-control-sm" placeholder="Nama Barang/Jasa" required>
            </div>
            <div class="col-md-2">
                <input type="number" name="items[${index}][qty]" class="form-control form-control-sm" placeholder="Qty" value="1" required min="1">
            </div>
            <div class="col-md-4">
                <input type="number" name="items[${index}][price]" class="form-control form-control-sm" placeholder="Harga Satuan" required min="0">
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger remove-item-edit" onclick="this.closest('.rab-item-row-edit').remove()">&times;</button>
            </div>
        `;
        container.appendChild(row);
    }
    
    // Attach remove event for existing items
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.remove-item-edit').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.rab-item-row-edit').remove();
            });
        });
    });
</script>
"""

if "<!-- Edit RAB Modals -->" not in content:
    content = content.replace("@endsection", modal_template + "\n@endsection")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("RABs UI patched successfully.")
