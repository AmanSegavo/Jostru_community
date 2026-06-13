<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Event;
use App\Models\WasteDeposit;
use App\Models\Chat;
use App\Models\User;
use App\Models\SystemSetting;
use App\Models\WasteCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class MemberController extends Controller
{
    public function onboarding()
    {
        $user = auth()->user();
        if ($user->status === 'AKTIF') {
            return redirect()->route('dashboard');
        }
        
        $interview = \Illuminate\Support\Facades\DB::table('member_interviews')->where('user_id', $user->id)->first();
        return view('member.onboarding', compact('interview'));
    }

    public function submitInterview(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'motivation' => 'required|string',
            'skills' => 'required|string',
            'experience' => 'required|string',
            'expectations' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ijazah' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'cv' => 'nullable|file|mimes:pdf|max:5120',
            'sertifikat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $user = auth()->user();
        $user->update([
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
        ]);

        // Handle file uploads
        $paths = [];
        $files = ['ktp', 'kk', 'ijazah', 'cv', 'sertifikat'];
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $paths[$file.'_path'] = $request->file($file)->store('onboarding_docs', 'public');
            }
        }

        if (!empty($paths)) {
            $user->update($paths);
        }

        \Illuminate\Support\Facades\DB::table('member_interviews')->updateOrInsert(
            ['user_id' => $user->id],
            [
                'motivation' => $request->motivation,
                'skills' => $request->skills,
                'experience' => $request->experience,
                'expectations' => $request->expectations,
                'status' => 'PENDING_REVIEW',
                'created_at' => now(),
                'updated_at' => now()
            ]
        );

        return back()->with('success', 'Terima kasih! Formulir Anda telah dikirim beserta dokumen pendukung (jika ada). Sedang menunggu tinjauan dari Admin.');
    }

    public function dashboard()
    {
        try {
            $totalWeight = WasteDeposit::where('user_id', auth()->id())
                ->where('status', 'APPROVED')
                ->sum('weight');
        } catch (\Exception $e) {
            $totalWeight = 0;
        }

        try {
            $nextEvent = Event::where('event_date', '>=', now())
                ->orderBy('event_date', 'asc')
                ->first();
        } catch (\Exception $e) {
            $nextEvent = null;
        }

        try {
            $certificates = \App\Models\Shareholder::where('user_id', auth()->id())->get();
        } catch (\Exception $e) {
            $certificates = collect();
        }

        return view('member.dashboard', compact('totalWeight', 'nextEvent', 'certificates'));
    }

    public function feed()
    {
        try {
            $posts = Post::with(['user', 'comments.user'])->latest()->paginate(10);
        } catch (\Exception $e) {
            $posts = collect();
        }
        return view('member.feed', compact('posts'));
    }

    public function events()
    {
        try {
            $events = Event::orderBy('event_date', 'asc')->get();
        } catch (\Exception $e) {
            $events = collect();
        }
        return view('member.events', compact('events'));
    }

    public function wasteReport()
    {
        $mode = SystemSetting::getSetting('waste_input_mode', 'both');
        $canInput = ($mode === 'both' || $mode === 'member_only') && auth()->user()->can_input_waste;

        try {
            $reports = WasteDeposit::where('user_id', auth()->id())->latest()->get();
            $categories = WasteCategory::all();
        } catch (\Exception $e) {
            $reports = collect();
            $categories = collect();
        }
        return view('member.waste_report', compact('reports', 'canInput', 'categories'));
    }

    public function storeWasteReport(Request $request)
    {
        $mode = SystemSetting::getSetting('waste_input_mode', 'both');
        if (($mode !== 'both' && $mode !== 'member_only') || !auth()->user()->can_input_waste) {
            return back()->with('error', 'Fitur input mandiri saat ini dinonaktifkan atau Anda tidak memiliki izin.');
        }

        $request->validate([
            'waste_category_id' => 'required|exists:waste_categories,id',
            'weight'      => 'required|numeric|min:0.1',
            'description' => 'nullable|string',
            'image'       => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi|max:51200',
            'latitude'    => 'required|numeric',
            'longitude'   => 'required|numeric',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('waste_reports', 'public');
        }

        $category = WasteCategory::find($request->waste_category_id);

        WasteDeposit::create([
            'user_id'     => auth()->id(),
            'waste_category_id' => $category->id,
            'type'        => $category->name, // fallback for legacy
            'weight'      => $request->weight,
            'description' => $request->description,
            'media_path'  => $imagePath,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude
        ]);
        // --- HYBRID ARCHITECTURE: Kirim data ke Python API (Opsional) ---
        try {
            $pythonApiUrl = env('PYTHON_API_URL', 'http://localhost:8000/api/v1/waste/deposits');
            $pythonApiKey = env('PYTHON_API_KEY', 'rahasia-super-jostru-123');

            \Illuminate\Support\Facades\Http::timeout(3)->withHeaders([
                'X-API-Key' => $pythonApiKey
            ])->post($pythonApiUrl, [
                'user_id'   => auth()->id(),
                'category'  => $category->name,
                'weight_kg' => (float) $request->weight
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Python API Offline: ' . $e->getMessage());
        }

        // Notify Admins
        try {
            $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'Setoran Limbah Baru',
                    'message' => auth()->user()->name . ' menyetor ' . $request->weight . ' kg ' . $category->name,
                    'url'     => route('admin.waste_deposits')
                ]);
            }
        } catch (\Exception $e) {}

        return back()->with('success', 'Laporan setoran limbah berhasil dikirim dan menunggu persetujuan admin.');
    }

    // --- Chat & Call Features ---
    public function chatList(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        
        if (isset($user->can_chat) && !$user->can_chat) {
            return back()->with('error', 'Izin komunikasi Anda dinonaktifkan oleh Admin.');
        }

        $search = $request->input('q');
        $myId = auth()->id();

        // Ambil aturan relasi chat
        $relations = \App\Models\ChatRelation::where('source_user_id', $myId)->get();
        $canChatAll = $relations->where('target_type', 'all')->count() > 0;
        
        $allowedDivisions = $relations->where('target_type', 'division')->pluck('target_id')->toArray();
        $allowedUsers = $relations->where('target_type', 'user')->pluck('target_id')->toArray();

        // Ambil riwayat chat sebelumnya
        $chattedUserIds = \App\Models\Chat::where('sender_id', $myId)->pluck('receiver_id')
            ->merge(\App\Models\Chat::where('receiver_id', $myId)->pluck('sender_id'))
            ->unique()->toArray();

        $query = \App\Models\User::where('id', '!=', $myId);

        if (!$canChatAll && auth()->user()->role !== 'admin' && auth()->user()->role !== 'superadmin') {
            $query->where(function($q) use ($allowedUsers, $allowedDivisions, $chattedUserIds) {
                // Selalu boleh membalas orang yang sudah chat / history
                $q->whereIn('id', $chattedUserIds)
                  // Selalu boleh chat admin
                  ->orWhereIn('role', ['admin', 'superadmin'])
                  // Explicit user permissions
                  ->orWhereIn('id', $allowedUsers);
                  
                // Boleh chat anggota dari divisi yang diizinkan
                if (count($allowedDivisions) > 0) {
                    $q->orWhereHas('assignedDivisions', function($sq) use ($allowedDivisions) {
                        $sq->whereIn('divisions.id', $allowedDivisions);
                    });
                    $q->orWhereIn('division_id', $allowedDivisions);
                }
            });
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%");
            });
        }

        $users = $query->limit(100)->get();

        foreach ($users as $u) {
            $lastMsg = \App\Models\Chat::where(function($q) use ($myId, $u) {
                    $q->where('sender_id', $myId)->where('receiver_id', $u->id);
                })->orWhere(function($q) use ($myId, $u) {
                    $q->where('sender_id', $u->id)->where('receiver_id', $myId);
                })
                ->latest('created_at')->first();
                
            $u->last_message = $lastMsg ? $lastMsg->message : '';
            $u->last_message_time = $lastMsg ? $lastMsg->created_at : null;
            $u->unread_count = \App\Models\Chat::where('sender_id', $u->id)
                ->where('receiver_id', $myId)->where('is_read', false)->count();
        }

        $users = $users->sortByDesc(function($u) {
            return [ $u->unread_count > 0 ? 1 : 0, $u->last_message_time ? $u->last_message_time->timestamp : 0 ];
        });

        return view('member.chat_list', compact('users', 'search'));
    }

    public function chatRoom($receiverId)
    {
        $receiver = \App\Models\User::findOrFail($receiverId);

        if ((isset(auth()->user()->can_chat) && !auth()->user()->can_chat) || (isset($receiver->can_chat) && !$receiver->can_chat)) {
            return redirect()->route('member.chat.list')->with('error', 'Komunikasi antar anggota sedang dibatasi.');
        }

        $myId = auth()->id();
        $messages = \App\Models\Chat::where(function ($q) use ($receiverId, $myId) {
            $q->where('sender_id', $myId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($receiverId, $myId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $myId);
        })->orderBy('created_at', 'asc')->get();

        \App\Models\Chat::where('sender_id', $receiverId)
            ->where('receiver_id', $myId)
            ->update(['is_read' => true]);

        return view('member.chat_room', compact('receiver', 'messages'));
    }

    public function sendMessage(Request $request, $receiverId)
    {
        $request->validate([
            'message' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,mp4,webm|max:10240' // max 10MB
        ]);

        if (!$request->message && !$request->hasFile('attachment')) {
            return response()->json(['success' => false, 'error' => 'Pesan atau lampiran harus diisi.'], 400);
        }

        try {
            $attachmentPath = null;
            $attachmentType = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $extension = strtolower($file->getClientOriginalExtension());
                $filename = time() . '_' . uniqid() . '.' . $extension;
                
                // Pindahkan ke public/uploads/chats (aman untuk shared hosting tanpa symlink)
                $file->move(public_path('uploads/chats'), $filename);
                $attachmentPath = 'uploads/chats/' . $filename;
                
                if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $attachmentType = 'image';
                } elseif (in_array($extension, ['mp4', 'webm'])) {
                    $attachmentType = 'video';
                }
            }

            $chat = Chat::create([
                'sender_id'       => auth()->id(),
                'receiver_id'     => $receiverId,
                'message'         => $request->message ?? '',
                'attachment_path' => $attachmentPath,
                'attachment_type' => $attachmentType,
            ]);

            return response()->json([
                'success'         => true,
                'id'              => $chat->id,
                'message'         => $chat->message,
                'sender_id'       => $chat->sender_id,
                'attachment_path' => $attachmentPath ? asset($attachmentPath) : null,
                'attachment_type' => $attachmentType,
                'created_at'      => $chat->created_at->toDateTimeString(),
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // --- Polling endpoint untuk real-time chat ---
    public function pollMessages(Request $request, $receiverId)
    {
        try {
            $afterId = (int) $request->get('after', 0);

            $messages = Chat::where(function($q) use ($receiverId) {
                $q->where(function ($sq) use ($receiverId) {
                    $sq->where('sender_id', auth()->id())->where('receiver_id', $receiverId);
                })->orWhere(function ($sq) use ($receiverId) {
                    $sq->where('sender_id', $receiverId)->where('receiver_id', auth()->id());
                });
            })->where('id', '>', $afterId)
              ->orderBy('created_at', 'asc')
              ->get(['id', 'sender_id', 'message', 'attachment_path', 'attachment_type', 'created_at']);

            // Parse asset url for attachment
            foreach ($messages as $msg) {
                if ($msg->attachment_path) {
                    $msg->attachment_path = asset($msg->attachment_path);
                }
            }

            // Mark as read
            Chat::where('sender_id', $receiverId)
                ->where('receiver_id', auth()->id())
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json($messages);
        } catch (\Exception $e) {
            return response()->json([], 200);
        }
    }
    
    public function videoCall($receiverId)
{
    $receiver = User::findOrFail($receiverId);

    // === TAMBAHKAN INI ===
    try {
        if (Schema::hasColumn('users', 'can_chat')) {
            if (!auth()->user()->can_chat || !$receiver->can_chat) {
                return redirect()->route('member.chat.list')
                    ->with('error', 'Komunikasi antar anggota sedang dibatasi.');
            }
        }
    } catch (\Exception $e) {}

    // Cegah call ke diri sendiri
    if (auth()->id() == $receiverId) {
        return redirect()->route('member.chat.list')
            ->with('error', 'Tidak bisa video call ke diri sendiri.');
    }

    $ids = [auth()->id(), $receiverId];
    sort($ids);
    $roomName = "JostruCall_" . implode("_", $ids);

    return view('member.video_call', compact('receiver', 'roomName'));
}
    // --- Kartu Digital Member ---
    public function myCardEditor()
    {
        $user = auth()->user();
        if ($user->status !== 'AKTIF' && !in_array($user->role, ['admin', 'superadmin'])) {
            return back()->with('error', 'Kartu digital hanya tersedia untuk anggota yang sudah disetujui (AKTIF).');
        }

        // 2FA Security Check
        if ($user->card_2fa_enabled && !session('card_unlocked')) {
            return redirect()->route('member.card.auth');
        }

        return view('member.card_editor', compact('user'));
    }

    public function cardAuth()
    {
        return view('member.card_auth');
    }

    public function verifyCardAuth(\Illuminate\Http\Request $request)
    {
        $request->validate(['password' => 'required']);
        
        if (\Illuminate\Support\Facades\Hash::check($request->password, auth()->user()->password)) {
            session(['card_unlocked' => true]);
            return redirect()->route('member.card.editor')->with('success', 'Akses kartu berhasil dibuka.');
        }

        return back()->withErrors(['password' => 'Kata sandi salah.']);
    }
    
    // Fungsi untuk Like / Unlike
    public function toggleLike($id)
    {
        $post = \App\Models\Post::findOrFail($id);
        $like = \App\Models\Like::where('post_id', $id)->where('user_id', auth()->id())->first();

        if ($like) {
            $like->delete(); // Jika sudah like, maka unlike
        } else {
            \App\Models\Like::create([
                'post_id' => $id, 
                'user_id' => auth()->id()
            ]); // Tambahkan like baru
        }

        return back();
    }

    // Fungsi untuk memproses Komentar
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        \App\Models\Comment::create([
            'post_id' => $id,
            'user_id' => auth()->id(),
            'content' => $request->input('content')
        ]);

        return back();
    }
    
    // Fungsi Menyimpan Postingan
    // Fungsi Menyimpan Postingan
    public function storePost(Request $request)
    {
        // CEK FITUR MUTE (MEMBISU)
        if (!auth()->user()->can_post) {
            return back()->with('error', 'Akun Anda sedang DIBISUKAN (Muted) oleh Admin. Anda tidak dapat membuat postingan.');
        }

        $request->validate([
            'content' => 'required|string', 
            'image' => 'nullable|file|mimes:jpg,jpeg,png,mp4,mov,avi,webm|max:51200'
        ]);

        $mediaPath = null; 
        $mediaType = null;

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            
            // Cek apakah file benar-benar sukses diupload ke server
            if (!$file->isValid()) {
                return back()->with('error', 'Upload gagal: ' . $file->getErrorMessage());
            }

            $destinationPath = public_path('feed');
            if (!file_exists($destinationPath)) mkdir($destinationPath, 0755, true);
            
            $fileName = time() . '_' . uniqid() . '.' . $file->extension();
            
            // 1. CEK TIPE FILE SEBELUM DIPINDAHKAN!
            $mediaType = str_starts_with($file->getMimeType(), 'video') ? 'video' : 'image';
            
            // 2. SETELAH DICEK, BARU PINDAHKAN FILENYA
            $file->move($destinationPath, $fileName);
            $mediaPath = $fileName;
        }

        \App\Models\Post::create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
        ]);

        return back()->with('success', 'Postingan berhasil dibagikan!');
    }

    // --- Laporan Keuangan ---
    public function finances(Request $request)
    {
        $user = auth()->user();
        
        // Cek scope dari kolom finance_view_scope, fallback ke can_view_finances
        $scope = $user->finance_view_scope ?? ($user->can_view_finances ? 'global' : 'none');

        if ($scope === 'none') {
            return redirect()->route('dashboard')->with('error', 'Anda belum diberikan akses untuk melihat Laporan Keuangan.');
        }

        $query = \App\Models\Finance::with(['user', 'division']);

        // Jika scope = division, filter berdasarkan divisi user
        if ($scope === 'division') {
            $divisionIds = $user->assignedDivisions->pluck('id')->toArray();
            if ($user->division_id) {
                $divisionIds[] = $user->division_id;
            }
            $divisionIds = array_unique($divisionIds);

            if (empty($divisionIds)) {
                return redirect()->route('dashboard')->with('error', 'Anda tidak tergabung dalam Divisi manapun untuk melihat keuangan divisi.');
            }
            $query->whereIn('division_id', $divisionIds);
        }

        // Terapkan filter jika ada
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        // Filter divisi secara manual jika memiliki scope global dan memilih filter
        if ($request->filled('division_id') && ($scope === 'global' || $user->can_manage_finances)) {
            $query->where('division_id', $request->division_id);
        }

        // Hitung statistik berdasarkan scope yang sama (tanpa pagination)
        $statsQuery = clone $query;
        $allFinances = $statsQuery->get();
        $totalPemasukan = $allFinances->where('type', 'PEMASUKAN')->sum('amount');
        $totalPengeluaran = $allFinances->where('type', 'PENGELUARAN')->sum('amount');
        $saldo = $totalPemasukan - $totalPengeluaran;

        if ($request->has('print') && $request->print == 'true') {
            $finances = $query->orderBy('transaction_date', 'desc')->latest()->get();
        } else {
            $finances = $query->orderBy('transaction_date', 'desc')->latest()->paginate(15);
            $finances->appends($request->all());
        }

        // Data Chart (Sistem Pakar)
        $pemasukanPerKategori = (clone $statsQuery)->where('type', 'PEMASUKAN')
            ->selectRaw('kategori, sum(amount) as total')
            ->groupBy('kategori')
            ->get();
            
        $pengeluaranPerKategori = (clone $statsQuery)->where('type', 'PENGELUARAN')
            ->selectRaw('kategori, sum(amount) as total')
            ->groupBy('kategori')
            ->get();

        // Monthly Delta (This month vs Last month)
        $thisMonthPemasukan = \App\Models\Finance::where('type', 'PEMASUKAN')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year);
        $lastMonthPemasukan = \App\Models\Finance::where('type', 'PEMASUKAN')
            ->whereMonth('transaction_date', now()->subMonth()->month)
            ->whereYear('transaction_date', now()->subMonth()->year);

        if ($scope === 'division' && isset($divisionIds)) {
            $thisMonthPemasukan->whereIn('division_id', $divisionIds);
            $lastMonthPemasukan->whereIn('division_id', $divisionIds);
        }

        $thisMonthPemasukan = $thisMonthPemasukan->sum('amount');
        $lastMonthPemasukan = $lastMonthPemasukan->sum('amount');

        $debts = \App\Models\Debt::with('member')->orderBy('due_date', 'asc')->paginate(15, ['*'], 'debt_page');

        $divisions = [];
        if ($user->can_manage_finances || $user->can_allocate_budgets) {
            $divisions = \App\Models\Division::all();
            if ($scope === 'division') {
                $divisions = \App\Models\Division::whereIn('id', $divisionIds)->get();
            }
        }
        
        $members = \App\Models\User::where('status', 'AKTIF')->get();

        return view('member.finances', compact('finances', 'scope', 'totalPemasukan', 'totalPengeluaran', 'saldo', 'divisions', 'pemasukanPerKategori', 'pengeluaranPerKategori', 'thisMonthPemasukan', 'lastMonthPemasukan', 'debts', 'members'));
    }

    public function storeFinance(Request $request)
    {
        if (!auth()->user()->can_manage_finances) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengelola keuangan.');
        }

        $request->validate([
            'division_id' => 'nullable|exists:divisions,id',
            'type' => 'required|in:PEMASUKAN,PENGELUARAN',
            'kategori' => 'nullable|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
        ]);

        $proofPaths = [];
        if ($request->hasFile('proofs')) {
            foreach ($request->file('proofs') as $file) {
                $proofPaths[] = $file->store('finance_proofs', 'public');
            }
        }

        \App\Models\Finance::create([
            'user_id' => auth()->id(),
            'division_id' => $request->division_id,
            'type' => $request->type,
            'kategori' => $request->kategori,
            'description' => $request->description,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'proof_path' => empty($proofPaths) ? null : json_encode($proofPaths),
        ]);

        return back()->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function updateFinance(Request $request, $id)
    {
        if (!auth()->user()->can_manage_finances) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengelola keuangan.');
        }

        $finance = \App\Models\Finance::findOrFail($id);
        $request->validate([
            'division_id' => 'nullable|exists:divisions,id',
            'type' => 'required|in:PEMASUKAN,PENGELUARAN',
            'kategori' => 'nullable|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
        ]);

        $proofPaths = $finance->proof_path ? json_decode($finance->proof_path, true) : [];
        if ($request->hasFile('proofs')) {
            foreach ($request->file('proofs') as $file) {
                $proofPaths[] = $file->store('finance_proofs', 'public');
            }
        }

        $finance->update([
            'division_id' => $request->division_id,
            'type' => $request->type,
            'kategori' => $request->kategori,
            'description' => $request->description,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
            'proof_path' => empty($proofPaths) ? null : json_encode($proofPaths),
        ]);

        return back()->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroyFinance($id)
    {
        if (!auth()->user()->can_manage_finances) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengelola keuangan.');
        }

        $finance = \App\Models\Finance::findOrFail($id);
        $finance->delete();

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    public function storeBudget(Request $request)
    {
        if (!auth()->user()->can_allocate_budgets) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengalokasikan anggaran.');
        }

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
            'is_approved' => false, 
        ]);

        return back()->with('success', 'Alokasi anggaran diajukan dan menunggu persetujuan admin.');
    }

    // --- DEBT MANAGEMENT (Delegated Member) ---
    public function storeDebt(Request $request)
    {
        if (!auth()->user()->can_manage_finances) {
            return back()->with('error', 'Anda tidak memiliki izin mengelola keuangan.');
        }

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
        if (!auth()->user()->can_manage_finances) {
            return back()->with('error', 'Anda tidak memiliki izin mengelola keuangan.');
        }

        $debt = \App\Models\Debt::findOrFail($id);
        $request->validate([
            'creditor_name' => 'required|string|max:255',
            'member_id' => 'nullable|exists:users,id',
            'amount' => 'required|numeric|min:0',
            'type' => 'required|in:HUTANG,PIUTANG',
            'due_date' => 'nullable|date',
            'description' => 'nullable|string'
        ]);

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

        return back()->with('success', 'Catatan hutang/piutang diperbarui.');
    }

    public function payDebt(Request $request, $id)
    {
        if (!auth()->user()->can_manage_finances) {
            return back()->with('error', 'Anda tidak memiliki izin mengelola keuangan.');
        }

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

        \App\Models\Finance::create([
            'user_id' => auth()->id(),
            'type' => $debt->type == 'HUTANG' ? 'PENGELUARAN' : 'PEMASUKAN',
            'kategori' => $debt->type == 'HUTANG' ? 'Bayar Hutang' : 'Terima Piutang',
            'description' => 'Pembayaran ' . strtolower($debt->type) . ' kepada/dari: ' . $debt->creditor_name,
            'amount' => $request->pay_amount,
            'transaction_date' => $request->transaction_date
        ]);

        return back()->with('success', 'Pembayaran berhasil dan tercatat di Kas Umum.');
    }

    public function destroyDebt($id)
    {
        if (!auth()->user()->can_manage_finances) {
            return back()->with('error', 'Anda tidak memiliki izin mengelola keuangan.');
        }

        \App\Models\Debt::findOrFail($id)->delete();
        return back()->with('success', 'Hutang/Piutang dihapus.');
    }
    // --- PERMISSION DELEGATIONS ---
    public function delegations()
    {
        $user = auth()->user();
        
        // Members who have access to delegate (e.g. can_manage_finances = true)
        if (!$user->can_manage_finances && !$user->can_allocate_budgets && !$user->can_manage_members) {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses utama untuk didelegasikan.');
        }

        $myDelegations = \App\Models\PermissionDelegation::with('delegatee')->where('delegator_id', $user->id)->latest()->get();
        
        $users = \App\Models\User::where('id', '!=', $user->id)->where('status', 'AKTIF')->get();
        $divisions = \App\Models\Division::all();

        return view('member.delegations', compact('myDelegations', 'users', 'divisions'));
    }

    public function storeDelegation(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'delegatee_id' => 'required|exists:users,id',
            'permission' => 'required|string',
            'scope' => 'required|in:community,division',
            'requires_approval' => 'boolean'
        ]);

        // Verifikasi bahwa pendelegasi benar-benar memiliki izin yang akan didelegasikan
        $perm = $request->permission;
        if (!$user->$perm) {
            return back()->with('error', 'Anda tidak bisa mendelegasikan izin yang tidak Anda miliki.');
        }

        $requiresApproval = $request->has('requires_approval') ? true : false;
        
        // Buat delegasi
        \App\Models\PermissionDelegation::create([
            'delegator_id' => $user->id,
            'delegatee_id' => $request->delegatee_id,
            'permission' => $request->permission,
            'scope' => $request->scope,
            'requires_approval' => $requiresApproval,
            'status' => $requiresApproval ? 'PENDING' : 'APPROVED' // Jika tidak perlu ACC, langsung APPROVED
        ]);

        // Jika langsung APPROVED, berikan aksesnya sekarang
        if (!$requiresApproval) {
            $delegatee = \App\Models\User::find($request->delegatee_id);
            $delegatee->{$request->permission} = true;
            if ($request->permission == 'can_view_finances' || $request->permission == 'can_manage_finances') {
                $delegatee->finance_view_scope = $request->scope;
            }
            $delegatee->save();
        }

        return back()->with('success', 'Delegasi hak akses berhasil diajukan' . ($requiresApproval ? ' dan menunggu ACC Admin.' : ' dan langsung aktif.'));
    }

    public function revokeDelegation($id)
    {
        $delegation = \App\Models\PermissionDelegation::findOrFail($id);
        
        if ($delegation->delegator_id != auth()->id() && auth()->user()->role != 'superadmin') {
            return back()->with('error', 'Tidak diizinkan.');
        }

        // Cabut akses
        if ($delegation->status == 'APPROVED') {
            $delegatee = \App\Models\User::find($delegation->delegatee_id);
            $delegatee->{$delegation->permission} = false;
            if ($delegation->permission == 'can_view_finances' || $delegation->permission == 'can_manage_finances') {
                $delegatee->finance_view_scope = 'none';
            }
            $delegatee->save();
        }

        $delegation->delete();
        return back()->with('success', 'Delegasi hak akses telah dicabut.');
    }
}
