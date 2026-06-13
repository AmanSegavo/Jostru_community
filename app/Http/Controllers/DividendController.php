<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Shareholder;
use App\Models\User;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class DividendController extends Controller
{
    public function index()
    {
        // Only superadmin can access this
        if (auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized action. Only Superadmin can manage dividends.');
        }

        $shareholders = Shareholder::with(['user', 'division'])->latest()->get();
        $users = User::orderBy('name')->get();
        $divisions = \App\Models\Division::orderBy('name')->get();
        return view('admin.dividends.index', compact('shareholders', 'users', 'divisions'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403);
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'division_id' => 'nullable|exists:divisions,id',
            'name' => 'required|string|max:255',
            'percentage' => 'required|numeric|min:0|max:100',
            'percentage_text' => 'required|string|max:255',
            'issue_date' => 'required|date',
        ]);

        // Auto generate ID (Mencegah duplikasi dengan mengecek max ID atau looping jika bentrok)
        $year = \Carbon\Carbon::parse($request->issue_date)->format('Y');
        
        $secretPin = strtoupper(Str::random(6));

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $year, $secretPin) {
            // Dapatkan ID tertinggi di tahun yang sama menggunakan penguncian (lockForUpdate) untuk mencegah race condition
            $lastShareholder = Shareholder::where('certificate_id', 'like', "JSF-PS-{$year}-%")
                                          ->lockForUpdate()
                                          ->orderBy('certificate_id', 'desc')
                                          ->first();
            
            if ($lastShareholder) {
                // Ekstrak 4 digit terakhir
                $parts = explode('-', $lastShareholder->certificate_id);
                $lastCount = (int) end($parts);
                $count = $lastCount + 1;
            } else {
                $count = 1;
            }

            $certId = 'JSF-PS-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            
            // Fallback while loop in case of weird anomaly, tapi biasanya lockForUpdate sudah cukup
            while(Shareholder::where('certificate_id', $certId)->exists()) {
                $count++;
                $certId = 'JSF-PS-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            }

            Shareholder::create([
                'user_id' => $request->user_id,
                'division_id' => $request->division_id,
                'secret_pin' => $secretPin,
                'certificate_id' => $certId,
                'name' => strtoupper($request->name),
            'percentage' => $request->percentage,
            'percentage_text' => strtoupper($request->percentage_text),
            'issue_date' => $request->issue_date,
            ]);
        });

        return back()->with('success', 'Pemegang saham berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'superadmin') {
            abort(403);
        }

        Shareholder::findOrFail($id)->delete();
        return back()->with('success', 'Data berhasil dihapus.');
    }

    public function generateCertificate(Request $request, $id)
    {
        $shareholder = Shareholder::findOrFail($id);

        // Generate QR Code URL
        $qrUrl = 'https://quickchart.io/qr?size=200&margin=0&format=png&text=' . urlencode(url('/verify-cert/' . $shareholder->certificate_id));

        return view('admin.dividends.certificate_print', compact('shareholder', 'qrUrl'));
    }

    private function writeText($image, $text, $x, $y, $fontPath, $size, $color, $align = 'center')
    {
        if ($fontPath == 5) {
            // Built-in GD font fallback
            $image->text($text, $x, $y, function ($font) use ($color, $align) {
                $font->color($color);
                $font->align($align);
            });
            return;
        }

        $image->text($text, $x, $y, function ($font) use ($fontPath, $size, $color, $align) {
            $font->file($fontPath);
            $font->size($size);
            $font->color($color);
            $font->align($align);
        });
    }
}
