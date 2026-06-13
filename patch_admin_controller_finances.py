import re

file_path = "d:/Jostru Community Sistem/Jostru_community/app/Http/Controllers/AdminController.php"

with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update finances()
target1 = "$members = \\App\\Models\\User::where('status', 'AKTIF')->get();"
replace1 = "$members = \\App\\Models\\User::where('status', 'AKTIF')->get();\n        $approvedRabs = \\App\\Models\\Rab::where('status', 'APPROVED')->get();"
content = content.replace(target1, replace1)

target2 = "            'lastMonthPemasukan'\n        ));"
replace2 = "            'lastMonthPemasukan',\n            'approvedRabs'\n        ));"
content = content.replace(target2, replace2)

# 2. Update storeFinance()
target3 = "'transaction_date' => 'required|date',"
replace3 = "'transaction_date' => 'required|date',\n            'rab_id' => 'nullable|exists:rabs,id',"
content = content.replace(target3, replace3, 1)

target4 = """        \\App\\Models\\Finance::create([
            'user_id' => auth()->id(),
            'division_id' => $request->division_id,
            'type' => $request->type,"""
replace4 = """        \\App\\Models\\Finance::create([
            'user_id' => auth()->id(),
            'division_id' => $request->division_id,
            'rab_id' => $request->rab_id,
            'type' => $request->type,"""
content = content.replace(target4, replace4)

# 3. Update updateFinance()
content = content.replace(target3, replace3, 1) # second match

target5 = """        $finance->update([
            'type' => $request->type,"""
replace5 = """        $finance->update([
            'rab_id' => $request->rab_id,
            'type' => $request->type,"""
content = content.replace(target5, replace5)

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("AdminController patched for rab_id")
