import re

file_path = "d:/Jostru Community Sistem/Jostru_community/app/Http/Controllers/AdminController.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Add updateRab and destroyRab after storeRab
target = "        return back()->with('success', 'RAB berhasil diajukan.');\n    }"

replacement = """        return back()->with('success', 'RAB berhasil diajukan.');
    }

    public function updateRab(Request $request, $id)
    {
        $rab = \\App\\Models\\Rab::findOrFail($id);
        $oldTitle = $rab->title;
        
        $request->validate([
            'title' => 'required|string|max:255',
            'division_id' => 'required|exists:divisions,id',
            'description' => 'nullable|string',
            'items' => 'required|array',
            'items.*.name' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        $rab->update([
            'title' => $request->title,
            'division_id' => $request->division_id,
            'description' => $request->description,
        ]);

        $rab->items()->delete();
        $total = 0;
        foreach ($request->items as $item) {
            $subtotal = $item['qty'] * $item['price'];
            $total += $subtotal;
            \\App\\Models\\RabItem::create([
                'rab_id' => $rab->id,
                'name' => $item['name'],
                'qty' => $item['qty'],
                'unit_price' => $item['price'],
                'subtotal' => $subtotal
            ]);
        }
        $rab->update(['total_amount' => $total]);

        if ($rab->status === 'APPROVED') {
            $budget = \\App\\Models\\Budget::where('description', 'Alokasi otomatis dari persetujuan RAB: ' . $oldTitle)
                ->where('division_id', $rab->getOriginal('division_id'))
                ->first();
            if ($budget) {
                $budget->update([
                    'allocated_amount' => $total,
                    'description' => 'Alokasi otomatis dari persetujuan RAB: ' . $rab->title,
                    'division_id' => $rab->division_id
                ]);
            }
        }
        return back()->with('success', 'RAB berhasil diperbarui.');
    }
    
    public function destroyRab($id)
    {
        $rab = \\App\\Models\\Rab::findOrFail($id);
        if ($rab->status === 'APPROVED') {
            $budget = \\App\\Models\\Budget::where('description', 'Alokasi otomatis dari persetujuan RAB: ' . $rab->title)
                ->where('division_id', $rab->division_id)
                ->first();
            if ($budget) {
                $budget->delete();
            }
        }
        $rab->items()->delete();
        $rab->delete();
        return back()->with('success', 'RAB berhasil dihapus.');
    }"""

if "public function updateRab(" not in content:
    content = content.replace(target, replacement)
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(content)
    print("AdminController patched successfully for rabs.")
else:
    print("Already patched AdminController.")
