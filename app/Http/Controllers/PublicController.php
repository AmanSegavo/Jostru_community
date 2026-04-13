<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function verify_card($member_id)
    {
        $user = User::where('member_id', $member_id)->first();
        
        if (!$user) {
            return view('verify', ['isValid' => false, 'message' => 'Kartu Anggota Tidak Ditemukan atau Tidak Valid.']);
        }
        
        return view('verify', [
            'isValid' => true,
            'user' => $user
        ]);
    }
}
