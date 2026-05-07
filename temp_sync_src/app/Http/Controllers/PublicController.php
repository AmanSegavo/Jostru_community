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
    public function parseGmapsLink(Request $request)
    {
        $url = $request->input('url');
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['success' => false, 'message' => 'URL tidak valid.']);
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        $finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        curl_close($ch);

        // Pattern 1: /@lat,lng,
        if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
            return response()->json(['success' => true, 'lat' => $matches[1], 'lng' => $matches[2]]);
        }
        
        // Pattern 2: ?q=lat,lng or &q=lat,lng
        if (preg_match('/(?:\?|&)q=(-?\d+\.\d+),(-?\d+\.\d+)/', $finalUrl, $matches)) {
            return response()->json(['success' => true, 'lat' => $matches[1], 'lng' => $matches[2]]);
        }

        // Pattern 3: !3dlat!4dlng
        if (preg_match('/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/', $finalUrl, $matches)) {
            return response()->json(['success' => true, 'lat' => $matches[1], 'lng' => $matches[2]]);
        }

        return response()->json(['success' => false, 'message' => 'Koordinat tidak ditemukan dalam link tersebut.']);
    }
}
