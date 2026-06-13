<?php

namespace App\Http\Controllers;

use App\Models\Division;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount('assignedUsers')->latest()->paginate(10);
        return view('admin.divisions.index', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:FARM,LIVESTOCK,PRODUCTION,CAFE,GENERAL',
            'description' => 'nullable|string',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,svg|max:2048',
            'about_text' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
        ]);

        $data = $request->except('logo');
        $data['slug'] = \Illuminate\Support\Str::slug($request->name);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('media/divisions'), $filename);
            $data['logo'] = $filename;
        }

        Division::create($data);

        return back()->with('success', 'Divisi berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:FARM,LIVESTOCK,PRODUCTION,CAFE,GENERAL',
            'description' => 'nullable|string',
            'logo' => 'nullable|file|mimes:jpeg,png,jpg,svg|max:2048',
            'about_text' => 'nullable|string',
            'phone_number' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_description' => 'nullable|string',
        ]);

        $division = Division::findOrFail($id);
        $data = $request->except('logo');
        $data['slug'] = \Illuminate\Support\Str::slug($request->name);
        
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('media/divisions'), $filename);
            $data['logo'] = $filename;
            
            // Delete old logo if exists
            if ($division->logo && file_exists(public_path('media/divisions/' . $division->logo))) {
                @unlink(public_path('media/divisions/' . $division->logo));
            }
        }

        $division->update($data);

        return back()->with('success', 'Divisi berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $division = Division::findOrFail($id);
        
        // Reset jabatan users that belong to this division before deleting
        \App\Models\User::where('jabatan', 'LIKE', '%(' . $division->name . ')%')->update([
            'jabatan' => 'Anggota',
            'can_manage_division' => false
        ]);
        
        $division->delete();
        return back()->with('success', 'Divisi berhasil dihapus!');
    }

    public function show($id)
    {
        $division = Division::with(['assignedUsers', 'inventories', 'livestocks', 'productionBatches'])->findOrFail($id);
        
        // Ambil semua pengguna yang belum dimasukkan ke divisi ini
        $assignedUserIds = $division->assignedUsers->pluck('id')->toArray();
        $availableUsers = \App\Models\User::whereNotIn('id', $assignedUserIds)->get();

        return view('admin.divisions.show', compact('division', 'availableUsers'));
    }

    public function assignMember(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'jabatan' => 'required|string|max:255',
        ]);

        $division = Division::findOrFail($id);
        $isAdmin = $request->has('is_admin') ? true : false;
        $division->assignedUsers()->attach($request->user_id, [
            'jabatan' => $request->jabatan,
            'is_admin' => $isAdmin
        ]);

        // Update data manajemen karyawan
        $user = \App\Models\User::find($request->user_id);
        
        $updateData = [
            'jabatan' => $request->jabatan . ' (' . $division->name . ')'
        ];

        if ($isAdmin) {
            $updateData['can_manage_division'] = true;
            if ($user->role === 'member') {
                $updateData['role'] = 'admin';
            }
        }

        $user->update($updateData);

        return back()->with('success', 'Anggota berhasil ditarik ke divisi ini!');
    }

    public function removeMember($id, $userId)
    {
        $division = Division::findOrFail($id);
        $division->assignedUsers()->detach($userId);

        $user = \App\Models\User::find($userId);
        if ($user) {
            if (strpos($user->jabatan, '(' . $division->name . ')') !== false) {
                $user->update(['jabatan' => 'Anggota']);
            }

            $isAdminAnywhere = \Illuminate\Support\Facades\DB::table('division_user')
                                ->where('user_id', $userId)
                                ->where('is_admin', true)
                                ->exists();
            if (!$isAdminAnywhere) {
                $user->update(['can_manage_division' => false]);
                if ($user->role === 'admin' && $user->email !== 'admin@jostru.site') {
                    $user->update(['role' => 'member']);
                }
            }
        }

        return back()->with('success', 'Anggota berhasil dikeluarkan dari divisi.');
    }

    // --- Budgets ---
    public function budgets($id)
    {
        $division = Division::findOrFail($id);
        $budgets = \App\Models\Budget::where('division_id', $id)->orderBy('created_at', 'desc')->get();
        return view('admin.divisions.budgets', compact('division', 'budgets'));
    }

    public function storeBudget(Request $request, $id)
    {
        $request->validate([
            'allocated_amount' => 'required|numeric|min:0',
            'period' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        \App\Models\Budget::create([
            'division_id' => $id,
            'allocated_amount' => $request->allocated_amount,
            'period' => $request->period,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Alokasi anggaran berhasil ditambahkan.');
    }

    // --- Finances ---
    public function finances($id)
    {
        $division = Division::findOrFail($id);
        $finances = \App\Models\Finance::where('division_id', $id)->with('user', 'budget')->orderBy('transaction_date', 'desc')->get();
        $budgets = \App\Models\Budget::where('division_id', $id)->orderBy('created_at', 'desc')->get();
        
        $totalPemasukan = $finances->where('type', 'PEMASUKAN')->sum('amount');
        $totalPengeluaran = $finances->where('type', 'PENGELUARAN')->sum('amount');
        $saldo = $totalPemasukan - $totalPengeluaran;
        
        return view('admin.divisions.finances', compact('division', 'finances', 'budgets', 'totalPemasukan', 'totalPengeluaran', 'saldo'));
    }

    public function storeFinance(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|in:PEMASUKAN,PENGELUARAN',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'kategori' => 'nullable|string',
            'budget_id' => 'nullable|exists:budgets,id'
        ]);

        // Cek jika ini pengeluaran yang terikat budget
        if ($request->type === 'PENGELUARAN' && $request->filled('budget_id')) {
            $budget = \App\Models\Budget::find($request->budget_id);
            if ($budget) {
                $sisa = $budget->allocated_amount - $budget->used_amount;
                if ($request->amount > $sisa) {
                    return back()->with('error', 'Jumlah pengeluaran melebihi sisa anggaran yang tersedia (Rp ' . number_format($sisa, 0, ',', '.') . ').');
                }
                $budget->used_amount += $request->amount;
                $budget->save();
            }
        }

        $finance = \App\Models\Finance::create([
            'user_id' => auth()->id(),
            'division_id' => $id,
            'budget_id' => $request->budget_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
            'kategori' => $request->kategori,
            'status' => 'APPROVED'
        ]);

        return back()->with('success', 'Transaksi keuangan divisi berhasil dicatat.');
    }

    public function updateFinance(Request $request, $id, $finance_id)
    {
        $finance = \App\Models\Finance::where('division_id', $id)->findOrFail($finance_id);
        
        $request->validate([
            'type' => 'required|in:PEMASUKAN,PENGELUARAN',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'required|string',
            'kategori' => 'nullable|string',
            'budget_id' => 'nullable|exists:budgets,id'
        ]);

        // Jika ada perubahan pada budget_id atau amount, kita perlu sesuaikan
        if ($finance->type === 'PENGELUARAN' && $finance->budget_id) {
            $oldBudget = \App\Models\Budget::find($finance->budget_id);
            if ($oldBudget) {
                $oldBudget->used_amount -= $finance->amount;
                $oldBudget->save();
            }
        }

        if ($request->type === 'PENGELUARAN' && $request->filled('budget_id')) {
            $newBudget = \App\Models\Budget::find($request->budget_id);
            if ($newBudget) {
                $sisa = $newBudget->allocated_amount - $newBudget->used_amount;
                if ($request->amount > $sisa) {
                    // Revert old budget
                    if (isset($oldBudget)) {
                        $oldBudget->used_amount += $finance->amount;
                        $oldBudget->save();
                    }
                    return back()->with('error', 'Jumlah pengeluaran melebihi sisa anggaran yang tersedia (Rp ' . number_format($sisa, 0, ',', '.') . ').');
                }
                $newBudget->used_amount += $request->amount;
                $newBudget->save();
            }
        }

        $finance->update([
            'budget_id' => $request->budget_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'description' => $request->description,
            'kategori' => $request->kategori,
        ]);

        return back()->with('success', 'Transaksi keuangan divisi berhasil diperbarui.');
    }

    public function destroyFinance($id, $finance_id)
    {
        $finance = \App\Models\Finance::where('division_id', $id)->findOrFail($finance_id);
        
        if ($finance->type === 'PENGELUARAN' && $finance->budget_id) {
            $budget = \App\Models\Budget::find($finance->budget_id);
            if ($budget) {
                $budget->used_amount -= $finance->amount;
                $budget->save();
            }
        }
        
        $finance->delete();
        
        return back()->with('success', 'Transaksi keuangan divisi berhasil dihapus.');
    }
}
