<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Models\DataLakeRecord;
use App\Models\Finance;
use App\Models\Division;

class SharedReportController extends Controller
{
    /**
     * Endpoint untuk Admin men-generate link aman (Signed URL)
     */
    public function generateLink(Request $request)
    {
        $type = $request->input('type');
        
        if ($type === 'finance') {
            $params = [
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'division_id' => $request->input('division_id'),
            ];
            // Link expired in 7 days
            $url = URL::temporarySignedRoute('shared.report.finance', now()->addDays(7), $params);
            return response()->json(['success' => true, 'url' => $url]);
        }
        
        if ($type === 'datalake') {
            $id = $request->input('id');
            $url = URL::temporarySignedRoute('shared.report.datalake', now()->addDays(7), ['id' => $id]);
            return response()->json(['success' => true, 'url' => $url]);
        }

        return response()->json(['success' => false, 'message' => 'Tipe tidak valid'], 400);
    }

    /**
     * View Laporan Keuangan (Publik via Signed URL)
     */
    public function finance(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Link laporan sudah kadaluarsa atau tidak valid.');
        }

        $query = Finance::with(['user', 'division']);

        if ($request->has('division_id') && !empty($request->division_id)) {
            $query->where('division_id', $request->division_id);
        }
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $finances = $query->orderBy('transaction_date', 'asc')->get();
        
        $totalPemasukan = $finances->where('type', 'PEMASUKAN')->sum('amount');
        $totalPengeluaran = $finances->where('type', 'PENGELUARAN')->sum('amount');
        $saldo = $totalPemasukan - $totalPengeluaran;

        $divisionName = 'GLOBAL (Semua Divisi)';
        if ($request->division_id) {
            $div = Division::find($request->division_id);
            if ($div) $divisionName = $div->name;
        }

        return view('public.shared_finance', compact('finances', 'totalPemasukan', 'totalPengeluaran', 'saldo', 'divisionName'));
    }

    /**
     * View Data Lake Record (Publik via Signed URL)
     */
    public function datalake(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Link laporan sudah kadaluarsa atau tidak valid.');
        }

        $record = DataLakeRecord::with(['division', 'creator'])->findOrFail($id);
        $payload = is_string($record->payload) ? json_decode($record->payload, true) : $record->payload;
        $media = is_string($record->media_paths) ? json_decode($record->media_paths, true) : $record->media_paths;

        return view('public.shared_datalake', compact('record', 'payload', 'media'));
    }
}
