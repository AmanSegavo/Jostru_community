<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class MemberController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        return view('member.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $request->validate([
            'tanggal_lahir' => 'required|date',
            'alamat' => 'required|string',
            'password' => 'nullable|min:6'
        ]);

        $data = [
            'tanggal_lahir' => $request->tanggal_lahir,
            'alamat' => $request->alamat,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        \App\Models\ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'UPDATE PROFIL',
            'description' => 'Memperbarui profil mandiri dan data domisili'
        ]);

        return back()->with('success', 'Profil dan Kordinat Alamat berhasil diperbarui!');
    }
}
