<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Contact;
use App\Models\MembershipCard;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Typography\FontFactory;


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

        return view('admin.dashboard', compact('months', 'pemasukanData', 'pengeluaranData', 'activities'));
    }

    public function members(Request $request)
    {
        $query = User::where('role', 'member');
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('member_id', 'like', "%{$search}%");
            });
        }
        $members = $query->latest()->get();
        return view('admin.members', compact('members'));
    }

    public function exportMembers()
    {
        $members = User::where('role', 'member')->latest()->get();
        $filename = "Data_Anggota_Jostru_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['ID_Member', 'Nama', 'Email', 'Jabatan', 'Status', 'Tanggal_Lahir', 'Alamat', 'Tanggal_Daftar'];

        $callback = function() use($members, $columns) {
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
            'role' => 'required|in:admin,member',
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
            'role' => $request->role,
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
            'role' => 'required|in:admin,member',
            'jabatan' => 'required|string|max:255',
            'status' => 'required|string|in:AKTIF,TIDAK AKTIF',
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'jabatan' => $request->jabatan,
            'status' => $request->status,
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'member_id' => $request->member_id ?: $user->member_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($data);

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
        $name = $user->name;
        $user->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS ANGGOTA',
            'description' => 'Menghapus anggota bernama: ' . $name
        ]);

        return back()->with('success', 'Anggota berhasil dihapus!');
    }

    public function cards()
    {
        // Mengambil data kartu beserta relasi user-nya
        $cards = MembershipCard::with('user')->latest()->paginate(10);
        return view('admin.cards', compact('cards'));
    }

    public function messages()
    {
        // Mengambil pesan kontak
        $messages = Contact::latest()->paginate(10);
        return view('admin.messages', compact('messages'));
    }

    public function logs()
    {
        // Mengambil log aktivitas
        $logs = ActivityLog::with('user')->latest()->paginate(10);
        return view('admin.logs', compact('logs'));
    }

    public function finances(Request $request)
    {
        $query = \App\Models\Finance::with('user');
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
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

        $totalPemasukan = \App\Models\Finance::where('type', 'PEMASUKAN')->sum('amount');
        $totalPengeluaran = \App\Models\Finance::where('type', 'PENGELUARAN')->sum('amount');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $finances = $query->orderBy('transaction_date', 'desc')->latest()->paginate(15);
        $finances->appends($request->all()); // Preserve all filters on pagination
        
        return view('admin.finances', compact('finances', 'totalPemasukan', 'totalPengeluaran', 'saldo'));
    }

    public function exportFinances()
    {
        $finances = \App\Models\Finance::with('user')->orderBy('transaction_date', 'desc')->get();
        $filename = "Laporan_Keuangan_Jostru_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Tanggal', 'Jenis', 'Keterangan', 'Nominal', 'Penginput'];

        $callback = function() use($finances, $columns) {
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
            'type' => 'required|in:PEMASUKAN,PENGELUARAN',
            'kategori' => 'nullable|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
        ]);

        \App\Models\Finance::create([
            'user_id' => auth()->id(),
            'type' => $request->type,
            'kategori' => $request->kategori,
            'description' => $request->description,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'CATAT KEUANGAN',
            'description' => 'Mencatat ' . $request->type . ' sebesar Rp ' . number_format($request->amount, 0, ',', '.')
        ]);

        return back()->with('success', 'Catatan keuangan berhasil ditambahkan.');
    }

    public function updateFinance(Request $request, $id)
    {
        $finance = \App\Models\Finance::findOrFail($id);

        $request->validate([
            'type' => 'required|in:PEMASUKAN,PENGELUARAN',
            'kategori' => 'nullable|string|max:255',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
        ]);

        $finance->update([
            'type' => $request->type,
            'kategori' => $request->kategori,
            'description' => $request->description,
            'amount' => $request->amount,
            'transaction_date' => $request->transaction_date,
        ]);

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
        $finance->delete();

        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'HAPUS KEUANGAN',
            'description' => 'Menghapus catatan ' . $type . ' sebesar Rp ' . number_format($amount, 0, ',', '.')
        ]);

        return back()->with('success', 'Catatan keuangan berhasil dihapus.');
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

        // 1. Inisialisasi Image Manager (Sintaks Versi 3)
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
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat QR Code: Aktifkan ekstensi php_imagick atau pastikan terhubung ke internet.');
        }

        // 4. Tempelkan QR Code ke template (v4 menggunakan insert)
        // Kordinat X: 1114, Y: 720 (Untuk kotak putih kanan bawah)
        $image->insert($qrImage, 1114, 720);

        // 5. Tulis Informasi Anggota
        $fontPath = public_path('fonts/arial.ttf'); // Pastikan font arial.ttf sudah ada di public/fonts/

        // Menulis Nama (Dengan wordwrap agar tidak meluber jika nama panjang)
        $namaText = wordwrap('NAMA : ' . strtoupper($user->name), 26, "\n");
        $image->text($namaText, 530, 460, function (FontFactory $font) use ($fontPath) {
            $font->filename($fontPath);
            $font->size(60);
            $font->color('#ffffff');
            $font->align(vertical: 'top');
        });

        // Menulis ID Member
        $image->text('ID : ' . $memberId, 530, 560, function (FontFactory $font) use ($fontPath) {
            $font->filename($fontPath);
            $font->size(60);
            $font->color('#ffffff');
        });

        // Menulis Status
        $image->text('STATUS : AKTIF', 530, 660, function (FontFactory $font) use ($fontPath) {
            $font->filename($fontPath);
            $font->size(60);
            $font->color('#ffffff');
        });

        $encoded = $image->encodeUsingFileExtension('jpg');

        // 6. Kalau requestnya minta didownload (dari tombol pratinjau)
        if ($request->has('download')) {
            return response($encoded->toString())
                ->header('Content-Type', 'image/jpeg')
                ->header('Content-Disposition', 'attachment; filename="ID_Card_' . $user->name . '.jpg"');
        }

        // Tampilkan Gambar untuk Pratinjau
        return response($encoded->toString())->header('Content-Type', 'image/jpeg');
    }

    public function generateCardCustom(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Update database if name changed
        if ($request->has('nama_text') && !empty($request->nama_text)) {
            $user->update(['name' => $request->nama_text]);
        }

        $memberId = $user->member_id ?? 'JC-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $manager = new ImageManager(new Driver());

        $templatePath = public_path('images/template_kartu.png');
        if (!file_exists($templatePath)) {
            return back()->with('error', 'File template kartu tidak ditemukan.');
        }
        $image = $manager->decode($templatePath);

        // QR Code API Fallback
        try {
            $qrResponse = \Illuminate\Support\Facades\Http::timeout(10)->get('https://quickchart.io/qr', [
                'text' => route('member.verify', $memberId),
                'size' => 345,
                'margin' => 0,
                'format' => 'png'
            ]);
            if (!$qrResponse->successful()) throw new \Exception('API Error');
            $qrImage = $manager->decode($qrResponse->body());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memuat QR Code: Aktifkan ekstensi php_imagick atau cek internet.');
        }

        // Always put QR in absolute place since it's an image element
        $image->insert($qrImage, 1114, 720);

        $fontPath = public_path('fonts/arial.ttf');

        // Parse custom X Y coordinates
        $namaX = (int) $request->input('nama_x', 530);
        $namaY = (int) $request->input('nama_y', 460);
        
        $idX = (int) $request->input('id_x', 530);
        $idY = (int) $request->input('id_y', 560);

        $statusX = (int) $request->input('status_x', 530);
        $statusY = (int) $request->input('status_y', 660);

        $statusText = $request->input('status_text', 'AKTIF');

        $namaText = wordwrap('NAMA : ' . strtoupper($user->name), 26, "\n");
        $image->text($namaText, $namaX, $namaY, function (FontFactory $font) use ($fontPath) {
            $font->filename($fontPath);
            $font->size(60);
            $font->color('#ffffff');
            $font->align(vertical: 'top');
        });

        $image->text('ID : ' . $memberId, $idX, $idY, function (FontFactory $font) use ($fontPath) {
            $font->filename($fontPath);
            $font->size(60);
            $font->color('#ffffff');
            $font->align(vertical: 'top');
        });

        $image->text('STATUS : ' . strtoupper($statusText), $statusX, $statusY, function (FontFactory $font) use ($fontPath) {
            $font->filename($fontPath);
            $font->size(60);
            $font->color('#ffffff');
            $font->align(vertical: 'top');
        });

        $encoded = $image->encodeUsingFileExtension('jpg');

        return response($encoded->toString())
            ->header('Content-Type', 'image/jpeg')
            ->header('Content-Disposition', 'attachment; filename="ID_Card_' . $user->name . '.jpg"');
    }
}