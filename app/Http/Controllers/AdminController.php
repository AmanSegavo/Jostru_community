<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contact;
use App\Models\MembershipCard;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Post;
use App\Models\Event;
use App\Models\WasteDeposit;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\SystemSetting;
use App\Models\WasteCategory;


class AdminController extends Controller
{
    public function dashboard()
    {
        // Get last 6 months for chart
        $months = [];
        $pemasukanData = [];
        $pengeluaranData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $months[] = $date->translatedFormat('F Y');

            $pemasukan = \App\Models\Finance::where('type', 'PEMASUKAN')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $pengeluaran = \App\Models\Finance::where('type', 'PENGELUARAN')
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $pemasukanData[] = $pemasukan;
            $pengeluaranData[] = $pengeluaran;
        }

        $activities = \App\Models\ActivityLog::with('user')->latest()->take(5)->get();

        $totalWaste = \App\Models\WasteDeposit::where('status', 'APPROVED')->sum('weight');
        $pendingWaste = \App\Models\WasteDeposit::where('status', 'PENDING')->count();
        $totalPosts = \App\Models\Post::count();

        return view('admin.dashboard', compact('months', 'pemasukanData', 'pengeluaranData', 'activities', 'totalWaste', 'pendingWaste', 'totalPosts'));
    }

    public function members(Request $request)
    {
        // Tampilkan semua pengguna kecuali superadmin, termasuk admin reguler
        $query = User::where('role', '!=', 'superadmin');
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('member_id', 'like', "%{$search}%");
            });
        }
        $members = $query->latest()->get();
        $interviews = \Illuminate\Support\Facades\DB::table('member_interviews')->get()->keyBy('user_id');
        $divisions = \App\Models\Division::all();
        return view('admin.members', compact('members', 'divisions', 'interviews'));
    }

    public function exportMembers()
    {
        $members = User::whereNotIn('role', ['admin', 'superadmin'])->latest()->get();
        $filename = "Data_Anggota_Jostru_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID_Member', 'Nama', 'Email', 'Jabatan', 'Status', 'Tanggal_Lahir', 'Alamat', 'Tanggal_Daftar'];

        $callback = function () use ($members, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($members as $user) {
                fputcsv($file, [
                    $user->member_id,
                    $user->name,
                    $user->email,
                    $user->jabatan,
                    $user->status,
                    $user->tanggal_lahir,
                    $user->alamat,
                    $user->created_at ? $user->created_at->format('Y-m-d') : ''
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'password' => 'required|min:6',
            'jabatan' => 'required|string|max:255',
            'status' => 'required|string|in:AKTIF,TIDAK AKTIF',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
        ]);

        $memberId = $request->member_id;
        if (empty($memberId)) {
            // Cryptographic ID Logic based on DOB + Address + Uniqid
            $seed = $request->tanggal_lahir . $request->alamat . uniqid();
            $hash = strtoupper(substr(hash('sha256', $seed), 0, 8));
            $memberId = 'JC-' . $hash;
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'jabatan' => $request->jabatan,
            'status' => $request->status,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'member_id' => $memberId,
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'TAMBAH ANGGOTA',
            'description' => 'Menambahkan anggota baru: ' . $request->name
        ]);

        return back()->with('success', 'Anggota berhasil ditambahkan!');
    }

    public function updateMember(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'jabatan' => 'required|string|max:255',
            'status' => 'required|string|in:AKTIF,TIDAK AKTIF,PENDING,NONAKTIF,BANNED',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'division_id' => 'nullable|exists:divisions,id',
            'ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ijazah' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'cv' => 'nullable|file|mimes:pdf|max:5120',
            'sertifikat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);
        $finalMemberId = $request->member_id ?: $user->member_id;
        if (empty($finalMemberId)) {
            $seed = $request->tanggal_lahir . $request->alamat . uniqid();
            $hash = strtoupper(substr(hash('sha256', $seed), 0, 8));
            $finalMemberId = 'JC-' . $hash;
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'status' => $request->status,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'member_id' => $finalMemberId,
        ];

        $files = ['ktp', 'kk', 'ijazah', 'cv', 'sertifikat'];
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $data[$file.'_path'] = $request->file($file)->store('onboarding_docs', 'public');
            }
        }

        if ($request->has('division_id')) {
            $data['division_id'] = $request->division_id;
        }

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $oldStatus = $user->status;
        $user->update($data);

        if ($oldStatus !== 'AKTIF' && $request->status === 'AKTIF') {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title'   => 'Akun Disetujui!',
                'message' => 'Selamat! Akun Anda telah diverifikasi dan diaktifkan oleh Admin.',
                'url'     => route('dashboard')
            ]);
            
            \Illuminate\Support\Facades\DB::table('member_interviews')->where('user_id', $user->id)->update([
                'status' => 'REVIEWED'
            ]);
        }

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE ANGGOTA',
            'description' => 'Memperbarui data anggota: ' . $user->name
        ]);

        return back()->with('success', 'Data anggota berhasil diperbarui!');
    }

    public function destroyMember($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS ANGGOTA',
            'description' => 'Menghapus anggota: ' . $user->name
        ]);

        return back()->with('success', 'Data anggota berhasil dihapus!');
    }

    public function toggleChatAccess($id)
    {
        $user = User::findOrFail($id);
        $user->can_chat = !$user->can_chat;
        $user->save();

        return response()->json([
            'success' => true,
            'can_chat' => $user->can_chat,
            'message' => 'Akses chat berhasil ' . ($user->can_chat ? 'diaktifkan' : 'dinonaktifkan')
        ]);
    }

    public function toggleAppAccess(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($request->has('can_use_app')) {
            $user->can_use_app = $request->can_use_app;
        } else {
            $user->can_use_app = !$user->can_use_app;
        }
        $user->save();

        return response()->json(['success' => true]);
    }

    public function impersonate($id)
    {
        $user = User::findOrFail($id);
        
        // Simpan ID admin yang asli di session
        session()->put('impersonator_id', auth()->id());
        
        // Login sebagai user
        auth()->login($user);
        
        \App\Models\ActivityLog::create([
            'user_id' => session('impersonator_id'),
            'action' => 'PRATINJAU ANGGOTA',
            'description' => 'Admin mulai mempratinjau akun: ' . $user->name
        ]);

        return redirect()->route('dashboard')->with('success', 'Anda sedang mempratinjau halaman sebagai ' . $user->name);
    }

    public function leaveImpersonate()
    {
        if (session()->has('impersonator_id')) {
            $adminId = session('impersonator_id');
            $admin = User::findOrFail($adminId);
            
            // Login kembali sebagai admin
            auth()->login($admin);
            session()->forget('impersonator_id');
            
            return redirect()->route('admin.members')->with('success', 'Berhenti pratinjau. Anda kembali sebagai Admin.');
        }
        
        return redirect()->route('dashboard');
    }

    public function cards()
    {
        // Menampilkan kartu digital untuk semua anggota yang AKTIF, termasuk admin/pengurus
        $users = User::where('status', 'AKTIF')
                     ->latest()
                     ->paginate(10);
        return view('admin.cards', compact('users'));
    }

    public function messages()
    {
        $messages = Contact::latest()->paginate(15);
        
        $pendingMembers = User::whereNotIn('role', ['admin', 'superadmin'])
            ->where('status', '!=', 'AKTIF')
            ->latest()
            ->get();
            
        $interviews = [];
        foreach ($pendingMembers as $member) {
            $interview = \Illuminate\Support\Facades\DB::table('member_interviews')->where('user_id', $member->id)->first();
            if ($interview) {
                $interviews[$member->id] = $interview;
            }
        }

        return view('admin.messages', compact('messages', 'pendingMembers', 'interviews'));
    }

    public function markMessageAsRead($id)
    {
        $msg = Contact::findOrFail($id);
        $msg->update(['is_read' => true]);
        return back()->with('success', 'Pesan ditandai sudah dibaca.');
    }

    public function logs()
    {
        $logs = ActivityLog::with('user')->latest()->paginate(50);
        return view('admin.logs', compact('logs'));
    }

    public function finances(Request $request)
    {
        $query = \App\Models\Finance::with(['user', 'division', 'budget']);

        if ($request->has('division_id') && !empty($request->division_id)) {
            $query->where('division_id', $request->division_id);
        }

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                    ->orWhere('type', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%");
            });
        }

        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Hitung statistik berdasarkan filter (tanpa pagination)
        $statsQuery = clone $query;
        $allFinances = $statsQuery->get();
        
        // Totals
        $totalPemasukan = $allFinances->where('type', 'PEMASUKAN')->sum('amount');
        $totalPengeluaran = $allFinances->where('type', 'PENGELUARAN')->sum('amount');
        $saldo = $totalPemasukan - $totalPengeluaran;

        // Statistics per Category
        $catPemasukan = clone $query;
        $pemasukanPerKategori = $catPemasukan->where('type', 'PEMASUKAN')
            ->select('kategori', \DB::raw('SUM(amount) as total'))
            ->groupBy('kategori')
            ->get();

        $catPengeluaran = clone $query;
        $pengeluaranPerKategori = $catPengeluaran->where('type', 'PENGELUARAN')
            ->select('kategori', \DB::raw('SUM(amount) as total'))
            ->groupBy('kategori')
            ->get();

        // Monthly Delta (This month vs Last month)
        $thisMonthPemasukan = \App\Models\Finance::where('type', 'PEMASUKAN')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');
        $lastMonthPemasukan = \App\Models\Finance::where('type', 'PEMASUKAN')
            ->whereMonth('transaction_date', now()->subMonth()->month)
            ->whereYear('transaction_date', now()->subMonth()->year)
            ->sum('amount');

        if ($request->has('print') && $request->print == 'true') {
            $finances = $query->orderBy('transaction_date', 'desc')->latest()->get();
        } else {
            $finances = $query->orderBy('transaction_date', 'desc')->latest()->paginate(15, ['*'], 'finance_page');
            $finances->appends($request->all());
        }

        $divisions = \App\Models\Division::all();
        
        $debts = \App\Models\Debt::with('member')->orderBy('due_date', 'asc')->paginate(15, ['*'], 'debt_page');
        
        $members = \App\Models\User::where('status', 'AKTIF')->get();
        $approvedRabs = \App\Models\Rab::where('status', 'APPROVED')->get();

        return view('admin.finances', compact(
            'finances',
            'divisions',
            'debts',
            'members',
            'totalPemasukan',
            'totalPengeluaran',
            'saldo',
            'pemasukanPerKategori',
            'pengeluaranPerKategori',
            'thisMonthPemasukan',
            'lastMonthPemasukan',
            'approvedRabs'
        ));
    }

    public function exportFinances()
    {
        $finances = \App\Models\Finance::with('user')->orderBy('transaction_date', 'desc')->get();
        $filename = "Laporan_Keuangan_Jostru_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['Tanggal', 'Jenis', 'Keterangan', 'Nominal', 'Penginput'];

        $callback = function () use ($finances, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            foreach ($finances as $finance) {
                fputcsv($file, [
                    $finance->transaction_date ? \Carbon\Carbon::parse($finance->transaction_date)->format('Y-m-d') : '',
                    $finance->type,
                    $finance->description,
                    $finance->amount,
                    $finance->user ? $finance->user->name : 'Admin'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeFinance(Request $request)
    {
        $request->validate([
            'division_id' => 'nullable|exists:divisions,id',
            'type' => 'required|in:PEMASUKAN,PENGELUARAN',
            'kategori' => 'nullable|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'rab_id' => 'nullable|exists:rabs,id',
            'rab_id' => 'nullable|exists:rabs,id',
        ]);

        $proofPaths = [];
        if ($request->hasFile('proofs')) {
            foreach ($request->file('proofs') as $file) {
                $proofPaths[] = $file->store('finance_proofs', 'public');
            }
        }
        // Fallback backward compatibility for single proof (if old UI is ever hit)
        if ($request->hasFile('proof')) {
            $proofPaths[] = $request->file('proof')->store('finance_proofs', 'public');
        }

        // Jika pengeluaran dan ada divisi, cek budget
        if ($request->type == 'PENGELUARAN' && $request->division_id) {
            $budget = \App\Models\Budget::where('division_id', $request->division_id)
                ->where('period', date('Y-m', strtotime($request->transaction_date)))
                ->first();
            
            if ($budget) {
                $sisa = $budget->allocated_amount - $budget->used_amount;
                if ($request->amount > $sisa) {
                    return back()->with('error', 'Gagal: Pengeluaran melebihi sisa alokasi anggaran divisi (' . number_format($sisa, 0, ',', '.') . ').');
                }
                $budget->used_amount += $request->amount;
                $budget->save();
            }
        }

        \App\Models\Finance::create([
            'user_id' => auth()->id(),
            'division_id' => $request->division_id,
            'rab_id' => $request->rab_id,
            'type' => $request->type,
            'kategori' => $request->kategori,
            'description' => $request->description,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'proof_path' => count($proofPaths) > 0 ? $proofPaths[0] : null,
            'proofs' => $proofPaths
        ]);

        return back()->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function storeBudget(Request $request)
    {
        $request->validate([
            'division_id' => 'required|exists:divisions,id',
            'total_budget' => 'required|numeric|min:0',
            'month' => 'required|numeric',
            'year' => 'required|numeric',
            'notes' => 'nullable|string'
        ]);

        $period = sprintf('%04d-%02d', $request->year, $request->month);

        \App\Models\Budget::create([
            'division_id' => $request->division_id,
            'allocated_amount' => $request->total_budget,
            'period' => $period,
            'description' => $request->notes,
        ]);

        return back()->with('success', 'Anggaran berhasil dialokasikan ke Divisi!');
    }

    public function updateFinance(Request $request, $id)
    {
        $finance = \App\Models\Finance::findOrFail($id);

        $request->validate([
            'type' => 'required|in:PEMASUKAN,PENGELUARAN',
            'kategori' => 'nullable|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'rab_id' => 'nullable|exists:rabs,id',
        ]);

        $proofPaths = $finance->proofs ?? [];
        if ($finance->proof_path && empty($proofPaths)) {
            $proofPaths[] = $finance->proof_path;
        }

        if ($request->hasFile('proofs')) {
            foreach ($request->file('proofs') as $file) {
                $proofPaths[] = $file->store('finance_proofs', 'public');
            }
        }
        if ($request->hasFile('proof')) {
            $proofPaths[] = $request->file('proof')->store('finance_proofs', 'public');
        }

        // --- RELATIONAL LOGIC: Reverse Old Budget ---
        $oldType = $finance->type;
        $oldAmount = $finance->amount;
        $oldDivision = $finance->division_id;
        
        if ($oldType === 'PENGELUARAN' && $oldDivision) {
            $oldBudget = \App\Models\Budget::where('division_id', $oldDivision)
                ->where('period', date('Y-m', strtotime($finance->transaction_date)))
                ->first();
            if ($oldBudget) {
                $oldBudget->used_amount -= $oldAmount;
                $oldBudget->save();
            }
        }

        // Update Finance Record
        $finance->update([
            'rab_id' => $request->rab_id,
            'type' => $request->type,
            'kategori' => $request->kategori,
            'description' => $request->description,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'proof_path' => count($proofPaths) > 0 ? $proofPaths[0] : null,
            'proofs' => $proofPaths
        ]);
        
        // --- RELATIONAL LOGIC: Apply New Budget ---
        if ($request->type === 'PENGELUARAN' && $finance->division_id) {
            $newBudget = \App\Models\Budget::where('division_id', $finance->division_id)
                ->where('period', date('Y-m', strtotime($finance->transaction_date)))
                ->first();
            if ($newBudget) {
                $newBudget->used_amount += $finance->amount;
                $newBudget->save();
            }
        }

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE KEUANGAN',
            'description' => 'Memperbarui ' . $request->type . ' menjadi Rp ' . number_format($request->amount, 0, ',', '.')
        ]);

        return back()->with('success', 'Catatan keuangan berhasil diperbarui.');
    }

    public function destroyFinance($id)
    {
        $finance = \App\Models\Finance::findOrFail($id);
        $amount = $finance->amount;
        $type = $finance->type;
        
        // --- RELATIONAL LOGIC: Revert Budget if PENGELUARAN ---
        if ($type === 'PENGELUARAN' && $finance->division_id) {
            $budget = \App\Models\Budget::where('division_id', $finance->division_id)
                ->where('period', date('Y-m', strtotime($finance->transaction_date)))
                ->first();
            if ($budget) {
                $budget->used_amount -= $amount;
                if ($budget->used_amount < 0) {
                    $budget->used_amount = 0;
                }
                $budget->save();
            }
        }

        $finance->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS KEUANGAN',
            'description' => 'Menghapus catatan ' . $type . ' sebesar Rp ' . number_format($amount, 0, ',', '.')
        ]);

        return back()->with('success', 'Catatan keuangan berhasil dihapus.');
    }

    // --- DEBT MANAGEMENT ---
    public function storeDebt(Request $request)
    {
        $request->validate([
            'creditor_name' => 'required|string|max:255',
            'member_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:HUTANG,PIUTANG',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string'
        ]);

        $debt = \App\Models\Debt::create([
            'creditor_name' => $request->creditor_name,
            'member_id' => $request->member_id,
            'amount' => $request->amount,
            'remaining_amount' => $request->amount,
            'type' => $request->type,
            'status' => 'BELUM LUNAS',
            'due_date' => $request->due_date,
            'description' => $request->description,
            'user_id' => auth()->id()
        ]);

        // Sinkronisasi dengan Kas Umum (Finance)
        \App\Models\Finance::create([
            'user_id' => auth()->id(),
            'type' => $debt->type === 'HUTANG' ? 'PEMASUKAN' : 'PENGELUARAN',
            'kategori' => $debt->type === 'HUTANG' ? 'Pinjaman Masuk' : 'Memberi Pinjaman',
            'description' => ($debt->type === 'HUTANG' ? 'Menerima pinjaman dari: ' : 'Memberi pinjaman kepada: ') . $debt->creditor_name . ($debt->description ? ' (' . $debt->description . ')' : ''),
            'amount' => $request->amount,
            'transaction_date' => now()->format('Y-m-d')
        ]);

        return back()->with('success', 'Catatan hutang/piutang berhasil ditambahkan dan disinkronisasi ke Kas Umum.');
    }

    public function updateDebt(Request $request, $id)
    {
        $debt = \App\Models\Debt::findOrFail($id);
        $request->validate([
            'creditor_name' => 'required|string|max:255',
            'member_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:HUTANG,PIUTANG',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string'
        ]);

        // Recalculate remaining amount (this simple logic assumes no payments yet or resets it)
        $diff = $request->amount - $debt->amount;
        $new_remaining = max(0, $debt->remaining_amount + $diff);

        $debt->update([
            'creditor_name' => $request->creditor_name,
            'member_id' => $request->member_id,
            'amount' => $request->amount,
            'remaining_amount' => $new_remaining,
            'status' => $new_remaining == 0 ? 'LUNAS' : 'BELUM LUNAS',
            'type' => $request->type,
            'due_date' => $request->due_date,
            'description' => $request->description
        ]);

        return back()->with('success', 'Catatan hutang/piutang berhasil diperbarui.');
    }

    public function destroyDebt($id)
    {
        \App\Models\Debt::findOrFail($id)->delete();
        return back()->with('success', 'Hutang/Piutang dihapus.');
    }

    public function payDebt(Request $request, $id)
    {
        $debt = \App\Models\Debt::findOrFail($id);
        $request->validate([
            'pay_amount' => 'required|numeric|min:1|max:' . $debt->remaining_amount,
            'transaction_date' => 'required|date'
        ]);

        $debt->remaining_amount -= $request->pay_amount;
        if ($debt->remaining_amount <= 0) {
            $debt->status = 'LUNAS';
            $debt->remaining_amount = 0;
        }
        $debt->save();

        // Rekam ke Kas Umum otomatis
        \App\Models\Finance::create([
            'user_id' => auth()->id(),
            'type' => $debt->type == 'HUTANG' ? 'PENGELUARAN' : 'PEMASUKAN', // Bayar hutang = uang keluar, Terima cicilan piutang = uang masuk
            'kategori' => $debt->type == 'HUTANG' ? 'Bayar Hutang' : 'Terima Piutang',
            'description' => 'Pembayaran ' . strtolower($debt->type) . ' kepada/dari: ' . $debt->creditor_name,
            'amount' => $request->pay_amount,
            'transaction_date' => $request->transaction_date
        ]);

        return back()->with('success', 'Pembayaran berhasil dan tercatat di Kas Umum.');
    }

    public function delegations()
    {
        $delegations = \App\Models\PermissionDelegation::with(['delegator', 'delegatee'])->latest()->get();
        return view('admin.delegations', compact('delegations'));
    }

    public function approveDelegation($id)
    {
        $delegation = \App\Models\PermissionDelegation::findOrFail($id);
        $delegation->status = 'APPROVED';
        $delegation->save();

        $delegatee = \App\Models\User::find($delegation->delegatee_id);
        $delegatee->{$delegation->permission} = true;
        if ($delegation->permission == 'can_view_finances' || $delegation->permission == 'can_manage_finances') {
            $delegatee->finance_view_scope = $delegation->scope;
        }
        $delegatee->save();

        return back()->with('success', 'Delegasi disetujui, akses diberikan.');
    }

    public function rejectDelegation($id)
    {
        $delegation = \App\Models\PermissionDelegation::findOrFail($id);
        $delegation->status = 'REJECTED';
        $delegation->save();

        return back()->with('success', 'Delegasi ditolak.');
    }

    public function previewCard($id)
    {
        $user = User::findOrFail($id);
        return view('admin.card_preview', compact('user'));
    }

    public function generateCard(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Pastikan user memiliki member_id
        $memberId = $user->member_id ?? 'JC-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);

        // 1. Inisialisasi Image Manager (Sintaks Versi 4)
        $manager = new ImageManager(new Driver());

        // 2. Baca file template
        $templatePath = public_path('images/template_kartu.png'); // Sesuai permintaan pengguna format png
        if (!file_exists($templatePath)) {
            return back()->with('error', 'File template kartu tidak ditemukan.');
        }
        $image = $manager->decode($templatePath);

        // 3. Generate QR Code menjadi format PNG
        // Karena ekstensi Imagick di mesin Anda tidak aktif, kita akan mem-bypass library simplesoftware
        // dan menggunakan API Quickchart untuk me-render QR langsung dalam format bitmap image (bebas error).
        try {
            $qrResponse = \Illuminate\Support\Facades\Http::timeout(10)->get('https://quickchart.io/qr', [
                'text' => route('member.verify', $memberId),
                'size' => 345,
                'margin' => 0,
                'format' => 'png'
            ]);

            if (!$qrResponse->successful()) {
                throw new \Exception('API Error');
            }

            $qrImage = $manager->decode($qrResponse->body());
            $qrImage->resize(345, 345);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat QR Code: Aktifkan ekstensi php_imagick atau pastikan terhubung ke internet.');
        }

        // 4. Tempelkan QR Code ke template
        // Kordinat X: 1114, Y: 720 (Untuk kotak putih kanan bawah)
        $image->insert($qrImage, 1114, 720);

        // 5. Tulis Informasi Anggota
        $fontPath = public_path('fonts/arial.ttf');

        // Menulis Nama
        $namaText = 'NAMA : ' . strtoupper($user->name);
        $image->text($namaText, 530, 460, function ($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(50);
            $font->color('#ffffff');
            $font->align('left', 'top');
        });

        // Menulis ID Member
        $image->text('ID : ' . $memberId, 530, 550, function ($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(50);
            $font->color('#ffffff');
            $font->align('left', 'top');
        });

        // Menulis Status
        $image->text('STATUS : AKTIF', 530, 640, function ($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(50);
            $font->color('#ffffff');
            $font->align('left', 'top');
        });

        // Menulis Jabatan
        // Admin, Pengurus Inti, dsb
        $jabatanText = 'JABATAN : ' . strtoupper($user->role);
        if ($user->google_id) {
            $jabatanText .= ' (PENDAFTAR GOOGLE)';
        }
        $image->text($jabatanText, 530, 730, function ($font) use ($fontPath) {
            $font->file($fontPath);
            $font->size(50);
            $font->color('#ffffff');
            $font->align('left', 'top');
        });

        $encoded = $image->encodeUsingFileExtension('jpg', 85);

        if (request()->has('download')) {
            return response((string) $encoded)
                ->header('Content-Type', 'image/jpeg')
                ->header('Content-Disposition', 'attachment; filename="ID_Card_' . $user->name . '.jpg"');
        }

        // Mode Pratinjau (Preview)
        return response((string) $encoded)->header('Content-Type', 'image/jpeg');
    }
    // Dihapus karena diganti dengan satu fungsi download


    // Posts Management
    public function posts()
    {
        // Tambahkan 'likes' dan 'comments' di dalam with()
        $posts = \App\Models\Post::with(['user', 'likes', 'comments'])->latest()->paginate(15);
        return view('admin.posts', compact('posts'));
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'content'  => 'required|string',
            'image'    => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:102400',
            'link_url' => 'nullable|url',
            'tags'     => 'nullable|string|max:255',
        ]);

        $mediaPath = null;
        $mediaType = null;

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');

            // Simpan ke public/feed — fallback ke storage jika gagal
            $destinationPath = public_path('feed');
            if (!file_exists($destinationPath)) {
                @mkdir($destinationPath, 0777, true);
            }

            $ext      = $file->getClientOriginalExtension();
            $fileName = date('YmdHis') . '_' . uniqid() . '.' . $ext;

            // Coba copy langsung
            if (@copy($file->getRealPath(), $destinationPath . '/' . $fileName)) {
                @chmod($destinationPath . '/' . $fileName, 0644);
                $mediaPath = $fileName;
            } else {
                // Fallback: pakai move() bawaan Laravel
                try {
                    $file->move($destinationPath, $fileName);
                    $mediaPath = $fileName;
                } catch (\Exception $e) {
                    return back()->with('error', 'Gagal menyimpan file: ' . $e->getMessage());
                }
            }

            $mime = $file->getClientMimeType();
            $mediaType = str_starts_with($mime, 'video') ? 'video' : 'image';
        }

        Post::create([
            'user_id'    => auth()->id(),
            'content'    => $request->input('content'),
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
            'link_url'   => $request->link_url ?: null,
            'tags'       => $request->tags ? implode(',', array_map('trim', explode(',', $request->tags))) : null,
            'pinned'     => $request->boolean('pinned'),
        ]);

        return back()->with('success', 'Postingan berhasil dibuat!');
    }

    public function updatePost(Request $request, $id)
    {
        $request->validate([
            'content'  => 'required|string',
            'image'    => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,avi,webm|max:102400',
            'link_url' => 'nullable|url',
            'tags'     => 'nullable|string|max:255',
        ]);

        $post = Post::findOrFail($id);
        $data = [
            'content'  => $request->input('content'),
            'link_url' => $request->link_url ?: null,
            'tags'     => $request->tags ? implode(',', array_map('trim', explode(',', $request->tags))) : null,
            'pinned'   => $request->boolean('pinned'),
        ];

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $file = $request->file('image');
            $destinationPath = public_path('feed');
            if (!file_exists($destinationPath)) {
                @mkdir($destinationPath, 0777, true);
            }
            $ext      = $file->getClientOriginalExtension();
            $fileName = date('YmdHis') . '_' . uniqid() . '.' . $ext;

            if (@copy($file->getRealPath(), $destinationPath . '/' . $fileName)) {
                @chmod($destinationPath . '/' . $fileName, 0644);
            } else {
                $file->move($destinationPath, $fileName);
            }
            // Hapus media lama
            if ($post->media_path && file_exists(public_path('feed/' . $post->media_path))) {
                @unlink(public_path('feed/' . $post->media_path));
            }
            $data['media_path'] = $fileName;
            $data['media_type'] = str_starts_with($file->getClientMimeType(), 'video') ? 'video' : 'image';
        }

        if ($request->boolean('remove_media')) {
            if ($post->media_path && file_exists(public_path('feed/' . $post->media_path))) {
                @unlink(public_path('feed/' . $post->media_path));
            }
            $data['media_path'] = null;
            $data['media_type'] = null;
        }

        $post->update($data);
        return back()->with('success', 'Postingan berhasil diperbarui!');
    }

    public function destroyPost($id)
    {
        $post = Post::findOrFail($id);
        if ($post->media_path && file_exists(public_path('feed/' . $post->media_path))) {
            @unlink(public_path('feed/' . $post->media_path));
        }
        $post->delete();
        return back()->with('success', 'Postingan dihapus.');
    }

    // --- Media CMS Management ---
    private function formatBytes($bytes)
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' Bytes';
        }
    }

    public function media()
    {
        // Auto-sync existing files from public/media
        $mediaPath = public_path('media');
        if (file_exists($mediaPath)) {
            $files = array_diff(scandir($mediaPath), array('..', '.', '.htaccess', 'index.php'));
            foreach ($files as $file) {
                if (is_file($mediaPath . '/' . $file) && !\App\Models\Gallery::where('filename', $file)->exists()) {
                    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $type = in_array($ext, ['mp4', 'mov', 'avi']) ? 'video' : 'image';
                    $size = filesize($mediaPath . '/' . $file);
                    
                    \App\Models\Gallery::create([
                        'filename' => $file,
                        'type' => $type,
                        'title' => pathinfo($file, PATHINFO_FILENAME),
                        'category' => 'gallery',
                        'size' => $this->formatBytes($size)
                    ]);
                }
            }
        }

        $mediaFiles = \App\Models\Gallery::with('division')->latest()->get();
        $divisions = \App\Models\Division::all();
        return view('admin.media', compact('mediaFiles', 'divisions'));
    }

    public function storeMedia(Request $request)
    {
        $request->validate([
            'upload_type' => 'required|in:file,embed',
            'category' => 'required|in:banner,gallery,post',
            'title' => 'nullable|string|max:255',
            'division_id' => 'nullable|exists:divisions,id',
            'orientation' => 'nullable|in:landscape,portrait,square'
        ]);

        if ($request->upload_type == 'file') {
            $request->validate([
                'media' => 'required|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:102400'
            ]);

            $file = $request->file('media');
            $destinationPath = public_path('media');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '', $file->getClientOriginalName());
            $file->move($destinationPath, $fileName);

            $ext = strtolower($file->getClientOriginalExtension());
            $type = in_array($ext, ['mp4', 'mov', 'avi']) ? 'video' : 'image';
            
            \App\Models\Gallery::create([
                'filename' => $fileName,
                'type' => $type,
                'title' => $request->title ?: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                'category' => $request->category,
                'division_id' => $request->division_id,
                'orientation' => $request->orientation ?? 'landscape',
                'size' => $this->formatBytes(filesize($destinationPath . '/' . $fileName))
            ]);

            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'UPLOAD MEDIA',
                'description' => 'Mengunggah media: ' . $fileName
            ]);

        } else {
            $request->validate([
                'source_url' => 'required|url'
            ]);

            \App\Models\Gallery::create([
                'type' => 'embed',
                'source_url' => $request->source_url,
                'title' => $request->title ?: 'Embed Social Media',
                'category' => $request->category,
                'division_id' => $request->division_id,
                'orientation' => $request->orientation ?? 'landscape',
            ]);

            \App\Models\ActivityLog::create([
                'user_id' => auth()->id(),
                'action' => 'EMBED MEDIA',
                'description' => 'Menambahkan tautan embed sosial media.'
            ]);
        }

        return back()->with('success', 'Media berhasil ditambahkan.');
    }

    public function updateMedia(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:banner,gallery,post',
            'division_id' => 'nullable|exists:divisions,id',
            'orientation' => 'nullable|in:landscape,portrait,square'
        ]);

        $gallery = \App\Models\Gallery::findOrFail($id);
        $gallery->update([
            'title' => $request->title,
            'category' => $request->category,
            'division_id' => $request->division_id,
            'orientation' => $request->orientation ?? 'landscape'
        ]);

        return back()->with('success', 'Informasi media berhasil diperbarui.');
    }

    public function destroyMedia($id)
    {
        $gallery = \App\Models\Gallery::findOrFail($id);
        
        if ($gallery->type != 'embed' && $gallery->filename) {
            $path = public_path('media/' . $gallery->filename);
            if (is_file($path)) {
                unlink($path);
            }
        }
        
        $gallery->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS MEDIA',
            'description' => 'Menghapus item media dari CMS.'
        ]);

        return back()->with('success', 'Media berhasil dihapus.');
    }

    // Events Management
    public function adminEvents()
    {
        $events = Event::orderBy('event_date', 'asc')->get();
        return view('admin.events', compact('events'));
    }

    public function storeEvent(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'event_date' => 'required|date',
            'location' => 'nullable|string'
        ]);

        Event::create($request->all());
        return back()->with('success', 'Event berhasil dijadwalkan.');
    }

    public function destroyEvent($id)
    {
        Event::findOrFail($id)->delete();
        return back()->with('success', 'Event dibatalkan.');
    }

    // Waste Deposits Management
    public function wasteDeposits(\Illuminate\Http\Request $request)
    {
        $query = WasteDeposit::with(['user', 'category'])->latest();

        $filter = $request->get('filter', 'all');
        if ($filter === 'pending') {
            $query->where('status', 'PENDING');
        } elseif ($filter === 'approved') {
            $query->where('status', 'APPROVED');
        } elseif ($filter === 'rejected') {
            $query->where('status', 'REJECTED');
        } elseif ($filter === 'with_proof') {
            $query->whereNotNull('media_path');
        } elseif ($filter === 'without_proof') {
            $query->whereNull('media_path');
        }

        $deposits = $query->paginate(20);

        // Analytics
        $totalWeight = WasteDeposit::where('status', 'APPROVED')->sum('weight');
        $pendingCount = WasteDeposit::where('status', 'PENDING')->count();
        $withoutProofCount = WasteDeposit::whereNull('media_path')->count();

        $categories = WasteCategory::all();
        $members = User::where('status', 'AKTIF')->where('role', '!=', 'superadmin')->get();
        return view('admin.waste_deposits', compact('deposits', 'categories', 'members', 'filter', 'totalWeight', 'pendingCount', 'withoutProofCount'));
    }

    public function storeWasteDepositAdmin(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'waste_category_id' => 'required|exists:waste_categories,id',
            'weight' => 'required|numeric|min:0.1',
            'description' => 'nullable|string',
            'status' => 'required|in:PENDING,APPROVED,REJECTED'
        ]);

        $category = WasteCategory::findOrFail($request->waste_category_id);
        
        $deposit = WasteDeposit::create([
            'user_id' => $request->user_id,
            'waste_category_id' => $category->id,
            'type' => $category->name, // fallback
            'weight' => $request->weight,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        if ($deposit->status === 'APPROVED') {
            $points = $request->weight * $category->point_multiplier;
            $deposit->update(['points_awarded' => $points]);
            $user = User::find($request->user_id);
            $user->increment('points', $points);
            
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title'   => 'Limbah Disetujui (Admin)',
                'message' => 'Admin menambahkan dan menyetujui limbah Anda (' . $request->weight . ' kg).',
                'url'     => route('member.waste_report')
            ]);
        }

        return back()->with('success', 'Setoran limbah berhasil ditambahkan oleh Admin.');
    }

    public function updateWasteStatus(Request $request, $id)
    {
        $deposit = WasteDeposit::findOrFail($id);
        $oldStatus = $deposit->status;
        $deposit->update(['status' => $request->status]);

        // If approved and wasn't approved before, award points
        if ($request->status === 'APPROVED' && $oldStatus !== 'APPROVED') {
            $category = $deposit->category;
            if ($category) {
                $points = $deposit->weight * $category->point_multiplier;
                $deposit->update(['points_awarded' => $points]);
                $user = User::find($deposit->user_id);
                if ($user) {
                    $user->increment('points', $points);
                }
            }
        } elseif ($oldStatus === 'APPROVED' && $request->status !== 'APPROVED') {
            // Deduct points if status changed from APPROVED
            $points = $deposit->points_awarded;
            $deposit->update(['points_awarded' => 0]);
            $user = User::find($deposit->user_id);
            if ($user) {
                $user->decrement('points', $points);
            }
        }

        // Notify member of status change
        if ($oldStatus !== $request->status) {
            $statusText = $request->status === 'APPROVED' ? 'Disetujui' : ($request->status === 'REJECTED' ? 'Ditolak' : 'Diproses');
            \App\Models\Notification::create([
                'user_id' => $deposit->user_id,
                'title'   => 'Status Setoran Limbah',
                'message' => 'Setoran ' . $deposit->type . ' sebesar ' . $deposit->weight . ' kg telah ' . strtolower($statusText) . '.',
                'url'     => route('member.waste_report')
            ]);
        }

        return back()->with('success', 'Status setoran berhasil diperbarui.');
    }

    public function updateWasteDepositAdmin(Request $request, $id)
    {
        $deposit = WasteDeposit::findOrFail($id);
        
        $request->validate([
            'weight' => 'required|numeric|min:0.1',
            'description' => 'nullable|string'
        ]);

        $oldWeight = $deposit->weight;
        $deposit->update([
            'weight' => $request->weight,
            'description' => $request->description
        ]);

        // If approved, recalculate points
        if ($deposit->status === 'APPROVED' && $oldWeight != $request->weight) {
            $category = $deposit->category;
            if ($category) {
                $user = User::find($deposit->user_id);
                if ($user) {
                    // Deduct old points
                    $user->decrement('points', $deposit->points_awarded);
                    
                    // Add new points
                    $newPoints = $request->weight * $category->point_multiplier;
                    $deposit->update(['points_awarded' => $newPoints]);
                    $user->increment('points', $newPoints);
                }
            }
        }

        return back()->with('success', 'Data setoran berhasil diubah.');
    }

    public function destroyWasteDeposit($id)
    {
        $deposit = WasteDeposit::findOrFail($id);
        if ($deposit->status === 'APPROVED' && $deposit->points_awarded > 0) {
            $user = User::find($deposit->user_id);
            if ($user) {
                $user->decrement('points', $deposit->points_awarded);
            }
        }
        $deposit->delete();
        return back()->with('success', 'Data setoran dihapus.');
    }

    // --- System Settings ---
    public function settings()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'waste_input_mode' => 'required|in:member_only,admin_only,both',
        ]);

        SystemSetting::setSetting('waste_input_mode', $request->waste_input_mode);

        return back()->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    // --- Waste Categories ---
    public function wasteCategories()
    {
        $categories = WasteCategory::latest()->paginate(15);
        return view('admin.waste_categories', compact('categories'));
    }

    public function storeWasteCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'point_multiplier' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120'
        ]);

        $data = $request->except('image');
        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        WasteCategory::create($data);
        return back()->with('success', 'Kategori limbah berhasil ditambahkan.');
    }

    public function updateWasteCategory(Request $request, $id)
    {
        $category = WasteCategory::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'point_multiplier' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120'
        ]);

        $data = $request->except('image');
        if ($request->hasFile('image')) {
            if ($category->image_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($category->image_path);
            }
            $data['image_path'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        return back()->with('success', 'Kategori limbah berhasil diperbarui.');
    }

    public function destroyWasteCategory($id)
    {
        WasteCategory::findOrFail($id)->delete();
        return back()->with('success', 'Kategori limbah berhasil dihapus.');
    }

    // AI & Analytics Integration
    public function aiAnalytics()
    {
        // Membaca laporan terbaru yang dikirim dari Colab
        $resultsPath = storage_path('app/private/ai_results.json');
        $aiData = null;
        if (file_exists($resultsPath)) {
            $aiData = json_decode(file_get_contents($resultsPath), true);
        }

        return view('admin.ai_analytics', compact('aiData'));
    }

    public function saveAiResults(Request $request)
    {
        // Simple token protection
        if ($request->query('token') !== 'jostru-ai-123') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'summary' => 'required|array',
            'predictions' => 'nullable|array',
            'chart_base64' => 'required|string',
            'insights' => 'required|string'
        ]);

        $data = [
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'summary' => $request->summary,
            'predictions' => $request->predictions,
            'chart_base64' => $request->chart_base64,
            'insights' => $request->insights
        ];

        // Ensure private storage directory exists
        if (!file_exists(storage_path('app/private'))) {
            mkdir(storage_path('app/private'), 0755, true);
        }

        file_put_contents(storage_path('app/private/ai_results.json'), json_encode($data));

        return response()->json(['status' => 'success', 'message' => 'Laporan AI berhasil disimpan di server.']);
    }

    public function exportWasteData(Request $request)
    {
        // Simple token protection (optional)
        if ($request->query('token') !== 'jostru-ai-123') {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = WasteDeposit::with('user')->get()->map(function ($deposit) {
            return [
                'id' => $deposit->id,
                'user_name' => $deposit->user ? $deposit->user->name : 'Unknown',
                'type' => $deposit->type,
                'weight_kg' => $deposit->weight,
                'status' => $deposit->status,
                'date' => $deposit->created_at->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'status' => 'success',
            'count' => $data->count(),
            'data' => $data
        ]);
    }

    // --- HASIL PRODUKSI (V1.2) ---
    public function productions()
    {
        $productions = \App\Models\ProductionBatch::with('sourceWaste.user')->latest()->paginate(15);
        $approvedWastes = WasteDeposit::where('status', 'APPROVED')->get();
        return view('admin.productions', compact('productions', 'approvedWastes'));
    }

    public function storeProduction(Request $request)
    {
        $request->validate([
            'product_sku' => 'required|string|max:255',
            'quantity_produced' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'source_waste_id' => 'nullable|exists:waste_deposits,id',
            'produced_at' => 'required|date'
        ]);

        $batch = \App\Models\ProductionBatch::create($request->all());

        if ($request->filled('source_waste_id')) {
            $waste = WasteDeposit::find($request->source_waste_id);
            if ($waste) {
                $waste->update(['status' => 'PROCESSED']); // Tandai limbah sudah diolah
            }
        }

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'TAMBAH HASIL PRODUKSI',
            'description' => 'Mencatat ' . $request->quantity_produced . ' unit untuk SKU ' . $request->product_sku . ' dengan harga Rp ' . number_format($request->price, 0, ',', '.')
        ]);

        return back()->with('success', 'Catatan hasil produksi berhasil ditambahkan.');
    }

    public function destroyProduction($id)
    {
        $batch = \App\Models\ProductionBatch::findOrFail($id);

        // Kembalikan status limbah jika dihapus
        if ($batch->source_waste_id) {
            $waste = WasteDeposit::find($batch->source_waste_id);
            if ($waste && $waste->status === 'PROCESSED') {
                $waste->update(['status' => 'APPROVED']);
            }
        }

        $sku = $batch->product_sku;
        $batch->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS PRODUKSI',
            'description' => 'Menghapus catatan produksi SKU: ' . $sku
        ]);

        return back()->with('success', 'Catatan produksi berhasil dihapus.');
    }

    // --- OAuth Clients Management ---
    public function oauthClients()
    {
        $clients = [];
        if (class_exists(\Laravel\Passport\Client::class)) {
            $clients = \Laravel\Passport\Client::where('revoked', false)->get();
        }
        return view('admin.oauth_clients', compact('clients'));
    }

    public function storeOauthClient(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'redirect' => 'required|url',
        ]);

        if (!class_exists(\Laravel\Passport\ClientRepository::class)) {
            return back()->with('error', 'Laravel Passport belum diinstall.');
        }

        $clientRepo = app(\Laravel\Passport\ClientRepository::class);
        $client = $clientRepo->createPersonalAccessClient(
            null,
            $request->name,
            $request->redirect
        );

        \App\Models\ActivityLog::create([
            'user_id'     => auth()->id(),
            'action'      => 'BUAT OAUTH CLIENT',
            'description' => 'Membuat OAuth Client baru: ' . $request->name,
        ]);

        return back()->with('success', 'OAuth Client berhasil dibuat! Client ID: ' . $client->id . ' | Secret: ' . $client->plainSecret);
    }

    public function revokeOauthClient($id)
    {
        if (class_exists(\Laravel\Passport\Client::class)) {
            $client = \Laravel\Passport\Client::findOrFail($id);
            $client->update(['revoked' => true]);

            \App\Models\ActivityLog::create([
                'user_id'     => auth()->id(),
                'action'      => 'CABUT OAUTH CLIENT',
                'description' => 'Mencabut akses OAuth Client: ' . $client->name,
            ]);
        }
        return back()->with('success', 'OAuth Client berhasil dicabut.');
    }

    // --- RAB Management ---
    public function rabs()
    {
        $rabs = \App\Models\Rab::with(['division', 'items'])->latest()->paginate(15);
        $divisions = \App\Models\Division::all();
        return view('admin.rabs', compact('rabs', 'divisions'));
    }

    public function storeRab(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'division_id' => 'required|exists:divisions,id',
            'description' => 'nullable|string',
            'items' => 'required|array',
            'items.*.name' => 'required|string|max:255',
            'items.*.qty' => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        $rab = \App\Models\Rab::create([
            'title' => $request->title,
            'division_id' => $request->division_id,
            'description' => $request->description,
            'status' => 'PENDING',
            'total_amount' => 0
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $subtotal = $item['qty'] * $item['price'];
            $total += $subtotal;
            \App\Models\RabItem::create([
                'rab_id' => $rab->id,
                'name' => $item['name'],
                'qty' => $item['qty'],
                'unit_price' => $item['price'],
                'subtotal' => $subtotal
            ]);
        }

        $rab->update(['total_amount' => $total]);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'BUAT RAB',
            'description' => 'Membuat pengajuan RAB: ' . $rab->title
        ]);

        return back()->with('success', 'RAB berhasil diajukan.');
    }

    public function updateRab(Request $request, $id)
    {
        $rab = \App\Models\Rab::findOrFail($id);
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
            \App\Models\RabItem::create([
                'rab_id' => $rab->id,
                'name' => $item['name'],
                'qty' => $item['qty'],
                'unit_price' => $item['price'],
                'subtotal' => $subtotal
            ]);
        }
        $rab->update(['total_amount' => $total]);

        if ($rab->status === 'APPROVED') {
            $budget = \App\Models\Budget::where('description', 'Alokasi otomatis dari persetujuan RAB: ' . $oldTitle)
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
        $rab = \App\Models\Rab::findOrFail($id);
        if ($rab->status === 'APPROVED') {
            $budget = \App\Models\Budget::where('description', 'Alokasi otomatis dari persetujuan RAB: ' . $rab->title)
                ->where('division_id', $rab->division_id)
                ->first();
            if ($budget) {
                $budget->delete();
            }
        }
        $rab->items()->delete();
        $rab->delete();
        return back()->with('success', 'RAB berhasil dihapus.');
    }

    public function updateRabStatus(Request $request, $id)
    {
        $rab = \App\Models\Rab::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:APPROVED,REJECTED'
        ]);

        $rab->update(['status' => $request->status]);

        if ($request->status === 'APPROVED' && $request->has('create_budget')) {
            \App\Models\Budget::create([
                'division_id' => $rab->division_id,
                'allocated_amount' => $rab->total_amount,
                'period' => now()->format('Y-m'),
                'description' => 'Alokasi otomatis dari persetujuan RAB: ' . $rab->title
            ]);
        }

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'UPDATE STATUS RAB',
            'description' => 'Mengubah status RAB: ' . $rab->title . ' menjadi ' . $request->status
        ]);

        return back()->with('success', 'Status RAB berhasil diperbarui.');
    }

    public function exportRabs() {
        $rabs = \App\Models\Rab::with(['division', 'items'])->get();
        $filename = "Data_RAB_" . date('Y-m-d') . ".csv";
        $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename=$filename"];
        $callback = function() use ($rabs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Divisi', 'Pengaju', 'Judul', 'Total', 'Status', 'Tanggal']);
            foreach ($rabs as $r) {
                fputcsv($file, [$r->id, $r->division->name ?? '-', '-', $r->title, $r->total_amount, $r->status, $r->created_at->format('Y-m-d')]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportProductions() {
        $productions = \App\Models\ProductionBatch::with('division')->get();
        $filename = "Data_Produksi_" . date('Y-m-d') . ".csv";
        $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename=$filename"];
        $callback = function() use ($productions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Divisi', 'Nama Batch', 'Produk', 'Jumlah', 'Tanggal Produksi']);
            foreach ($productions as $p) {
                fputcsv($file, [$p->id, $p->division->name ?? '-', $p->batch_number, $p->product_name, $p->quantity_produced, $p->production_date]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportDivisions() {
        $divisions = \App\Models\Division::all();
        $filename = "Data_Divisi_" . date('Y-m-d') . ".csv";
        $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename=$filename"];
        $callback = function() use ($divisions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama Divisi', 'Deskripsi', 'Manajer ID', 'Dibuat Pada']);
            foreach ($divisions as $d) {
                fputcsv($file, [$d->id, $d->name, $d->description, $d->manager_id ?? '-', $d->created_at->format('Y-m-d')]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportLogs() {
        $logs = \App\Models\ActivityLog::with('user')->orderBy('created_at', 'desc')->get();
        $filename = "Data_Log_" . date('Y-m-d') . ".csv";
        $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename=$filename"];
        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User', 'Aksi', 'Deskripsi', 'Waktu']);
            foreach ($logs as $l) {
                fputcsv($file, [$l->id, $l->user->name ?? 'Sistem', $l->action, $l->description, $l->created_at->format('Y-m-d H:i:s')]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportMessages() {
        $messages = \App\Models\Message::orderBy('created_at', 'desc')->get();
        $filename = "Data_Pesan_" . date('Y-m-d') . ".csv";
        $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename=$filename"];
        $callback = function() use ($messages) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama Pengirim', 'Email', 'Isi Pesan', 'Status', 'Tanggal']);
            foreach ($messages as $m) {
                fputcsv($file, [$m->id, $m->name, $m->email, $m->content, $m->is_read ? 'Dibaca' : 'Baru', $m->created_at->format('Y-m-d H:i:s')]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportWasteCategories() {
        $categories = WasteCategory::all();
        $filename = "Data_Kategori_Limbah_" . date('Y-m-d') . ".csv";
        $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename=$filename"];
        $callback = function() use ($categories) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Nama Kategori', 'Deskripsi', 'Nilai Poin/Kg']);
            foreach ($categories as $c) {
                fputcsv($file, [$c->id, $c->name, $c->description, $c->point_value]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }

    public function exportWasteDepositsExcel() {
        $deposits = WasteDeposit::with('user')->orderBy('created_at', 'desc')->get();
        $filename = "Data_Setoran_Limbah_" . date('Y-m-d') . ".csv";
        $headers = ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename=$filename"];
        $callback = function() use ($deposits) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Anggota', 'Jenis Limbah', 'Berat (Kg)', 'Status', 'Tanggal']);
            foreach ($deposits as $d) {
                fputcsv($file, [$d->id, $d->user->name ?? '-', $d->type, $d->weight, $d->status, $d->created_at->format('Y-m-d H:i:s')]);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}