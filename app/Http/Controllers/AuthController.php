<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login_id' => ['required'],
            'password' => ['required'],
        ]);

        $fieldType = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'member_id';

        $credentials = [
            $fieldType => $request->login_id,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->status === 'NONAKTIF' || $user->status === 'BANNED') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['login_id' => 'Akun Anda dinonaktifkan oleh Admin.']);
            }

            \App\Models\ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'LOGIN',
                'description' => 'Login berhasil dari IP: ' . $request->ip()
            ]);

            if ($user->role === 'admin' || $user->role === 'superadmin') {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/dashboard');
        }

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $seed = $request->email . uniqid();
        $hash = strtoupper(substr(hash('sha256', $seed), 0, 8));
        $memberId = 'JC-' . $hash;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'member',
            'status' => 'PENDING',
            'member_id' => $memberId,
        ]);

        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'REGISTER',
            'description' => 'Pendaftaran anggota baru dari IP: ' . $request->ip()
        ]);

        // Notify Admins
        try {
            $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'title'   => 'Pendaftar Baru',
                    'message' => $user->name . ' mendaftar dan menunggu persetujuan (ACC).',
                    'url'     => route('admin.members')
                ]);
            }
        } catch (\Exception $e) {}

        return redirect('/login')->with('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu ACC dari Admin.');
    }

    public function profile()
    {
        $user = auth()->user();
        return view('member.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $rules = [
            'alamat' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'tanggal_lahir' => 'nullable|date',
            'ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'kk' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'ijazah' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'cv' => 'nullable|file|mimes:pdf|max:5120',
            'sertifikat' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'min:6';
        }

        $request->validate($rules);

        $data = [
            'alamat' => $request->alamat,
            'tanggal_lahir' => $request->tanggal_lahir,
            'card_2fa_enabled' => $request->has('card_2fa_enabled') ? 1 : 0,
        ];

        $files = ['ktp', 'kk', 'ijazah', 'cv', 'sertifikat'];
        foreach ($files as $file) {
            if ($request->hasFile($file)) {
                $data[$file.'_path'] = $request->file($file)->store('onboarding_docs', 'public');
            }
        }

        if ($request->filled('latitude')) $data['latitude'] = $request->latitude;
        if ($request->filled('longitude')) $data['longitude'] = $request->longitude;

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        return redirect('/dashboard')->with('success', 'Profil dan lokasi berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if (!Hash::check($request->current_password, auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        auth()->user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password berhasil diubah!');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function redirectToGoogle()
    {
        return \Laravel\Socialite\Facades\Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = \Laravel\Socialite\Facades\Socialite::driver('google')->user();

            // 1. Cek apakah google_id ini sudah dipakai akun lain
            $userByGoogleId = User::where('google_id', $googleUser->id)->first();

            if (Auth::check()) {
                $currentUser = Auth::user();

                // Jika google_id sudah dipakai orang lain, jangan tautkan
                if ($userByGoogleId && $userByGoogleId->id !== $currentUser->id) {
                    return redirect('/profile')->withErrors(['email' => 'Akun Google ini sudah tertaut dengan akun lain.']);
                }

                // Tautkan ke akun saat ini
                $currentUser->update(['google_id' => $googleUser->id]);

                return redirect('/profile')->with('success', 'Akun Google berhasil ditautkan!');
            }

            // 2. Jika tidak sedang login, jalankan flow Login/Register biasa
            $user = $userByGoogleId ?: User::where('email', $googleUser->email)->first();

            if ($user) {
                // Update google_id jika belum ada (Auto-link by Email)
                if (!$user->google_id) {
                    $user->update(['google_id' => $googleUser->id]);
                }
                Auth::login($user);
                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'LOGIN',
                    'description' => 'Login berhasil via SSO Google dari IP: ' . request()->ip()
                ]);
            } else {
                // Register User Baru
                $seed = $googleUser->email . uniqid();
                $hash = strtoupper(substr(hash('sha256', $seed), 0, 8));

                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'password' => Hash::make(uniqid() . rand(1000, 9999)),
                    'role' => 'member',
                    'member_id' => 'JC-' . $hash,
                    'jabatan' => 'Anggota (Pendaftar Google)',
                    'status' => 'PENDING',
                    'tanggal_lahir' => date('Y-m-d'),
                    'alamat' => '',
                ]);

                \App\Models\ActivityLog::create([
                    'user_id' => $user->id,
                    'action' => 'REGISTER',
                    'description' => 'Pendaftaran otomatis via SSO Google dari IP: ' . request()->ip()
                ]);
            }

            if ($user->status === 'NONAKTIF' || $user->status === 'BANNED') {
                Auth::logout();
                return redirect('/login')->withErrors(['login_id' => 'Akun Anda dinonaktifkan oleh Admin.']);
            }

            if ($user->role === 'admin' || $user->role === 'superadmin') {
                return redirect()->intended('/admin/dashboard');
            }

            return redirect()->intended('/dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['email' => 'Gagal memproses autentikasi Google: ' . $e->getMessage()]);
        }
    }
}
