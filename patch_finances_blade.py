import re

file_path = "d:/Jostru Community Sistem/Jostru_community/resources/views/admin/finances.blade.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update the Add Modal
target_add_desc = """<div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Keterangan</label>"""
replace_add_desc = """<div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Tujuan RAB (Opsional)</label>
                        <select name="rab_id" class="form-control" style="border-radius:12px;">
                            <option value="">-- Pilih RAB (Jika Alokasi) --</option>
                            @foreach($approvedRabs as $arab)
                                <option value="{{ $arab->id }}">{{ $arab->title }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Keterangan</label>"""
if "Tujuan RAB" not in content:
    content = content.replace(target_add_desc, replace_add_desc, 1)

# 2. Update the Edit Modal (inside the loop)
target_edit_desc = """<div class="mb-3">
                        <label class="form-label" style="font-weight:600;">Keterangan</label>
                        <textarea name="description" class="form-control" rows="3" required style="border-radius:12px;">{{ $finance->description }}</textarea>"""
replace_edit_desc = """<div class="mb-3">
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
                        <textarea name="description" class="form-control" rows="3" required style="border-radius:12px;">{{ $finance->description }}</textarea>"""
content = content.replace(target_edit_desc, replace_edit_desc)

# 3. Update Table rendering to show RAB badge
target_td_desc = """<td>
                                    <div style="font-weight:600; color:#334155;">{{ $finance->kategori ?? 'Umum' }}</div>
                                    <div style="font-size:12px; color:#64748b;">{{ \Illuminate\Support\Str::limit($finance->description, 50) }}</div>
                                </td>"""
replace_td_desc = """<td>
                                    <div style="font-weight:600; color:#334155;">{{ $finance->kategori ?? 'Umum' }}</div>
                                    <div style="font-size:12px; color:#64748b;">{{ \Illuminate\Support\Str::limit($finance->description, 50) }}</div>
                                    @if($finance->rab)
                                        <div style="margin-top:4px;"><span class="badge" style="background:rgba(139,92,246,0.1); color:#7c3aed; border:1px solid rgba(139,92,246,0.2); font-size:10px;">RAB: {{ $finance->rab->title }}</span></div>
                                    @endif
                                </td>"""
content = content.replace(target_td_desc, replace_td_desc)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("finances.blade.php patched.")
