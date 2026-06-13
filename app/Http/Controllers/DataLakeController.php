<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataLakeRecord;
use App\Models\Division;
use Illuminate\Support\Facades\Storage;

class DataLakeController extends Controller
{
    public function index(Request $request)
    {
        $divisions = Division::all();
        $selectedDivisionId = $request->input('division_id');
        
        $query = DataLakeRecord::with(['division', 'creator'])->latest();
        
        if ($selectedDivisionId) {
            $query->where('division_id', $selectedDivisionId);
        }
        
        $statusFilter = $request->input('status', 'PROCESSED');
        if ($statusFilter !== 'ALL') {
            $query->where('status', $statusFilter);
        }
        
        $search = $request->input('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('category', 'like', "%{$search}%")
                  ->orWhere('payload', 'like', "%{$search}%");
            });
        }
        
        $records = $query->paginate(20)->appends($request->all());
        
        // Prepare map markers (for records that have latitude/longitude in payload)
        $mapMarkers = [];
        $allMapRecords = DataLakeRecord::whereNotNull('payload')->get();
        foreach ($allMapRecords as $rec) {
            if ($selectedDivisionId && $rec->division_id != $selectedDivisionId) continue;
            if ($statusFilter !== 'ALL' && $rec->status !== $statusFilter) continue;
            if ($search) {
                $recCat = strtolower($rec->category);
                $recPay = strtolower(is_string($rec->payload) ? $rec->payload : json_encode($rec->payload));
                $srch = strtolower($search);
                if (strpos($recCat, $srch) === false && strpos($recPay, $srch) === false) continue;
            }
            
            $payload = is_string($rec->payload) ? json_decode($rec->payload, true) : $rec->payload;
            if (is_array($payload) && isset($payload['latitude']) && isset($payload['longitude'])) {
                // Determine Marker Type
                $markerType = 'other';
                $markerColor = 'gray';
                if ($rec->category === 'USER_DUMP') {
                    $markerType = 'Anggota';
                    $markerColor = 'blue';
                } elseif ($rec->category === 'MAPS_LOCATION' && !empty($rec->division_id)) {
                    $markerType = 'Fasilitas Divisi';
                    $markerColor = 'green';
                } elseif ($rec->category === 'MAPS_LOCATION') {
                    $markerType = 'Tempat/Bangunan Eksternal';
                    $markerColor = 'red';
                } else {
                    $markerType = 'Aktivitas/Lainnya';
                    $markerColor = 'orange';
                }

                $mapMarkers[] = [
                    'id' => $rec->id,
                    'lat' => $payload['latitude'],
                    'lng' => $payload['longitude'],
                    'title' => $payload['company_name'] ?? $payload['title'] ?? $payload['name'] ?? 'Lokasi Tanpa Nama',
                    'description' => $payload['description'] ?? '',
                    'category' => $rec->category,
                    'type' => $markerType,
                    'color' => $markerColor,
                    'division' => $rec->division ? $rec->division->name : 'Global',
                    'full_payload' => $payload, // for modal detail
                    'media' => is_string($rec->media_paths) ? json_decode($rec->media_paths, true) : $rec->media_paths,
                    'signed_url' => \Illuminate\Support\Facades\URL::temporarySignedRoute('shared.report.datalake', now()->addDays(7), ['id' => $rec->id])
                ];
            }
        }

        // Stats
        $stats = [
            'total_raw' => DataLakeRecord::where('status', 'RAW')->count(),
            'total_processed' => DataLakeRecord::where('status', 'PROCESSED')->count(),
        ];

        // Fetch operational data for the specific division (or global if not selected) to satisfy "semua data di sistem"
        $financesQuery = \App\Models\Finance::with(['user', 'division'])->latest()->take(50);
        $rabsQuery = \App\Models\Rab::with(['division'])->latest()->take(50);
        $productionsQuery = \App\Models\ProductionBatch::with('division')->latest()->take(50);

        if ($selectedDivisionId) {
            $financesQuery->where('division_id', $selectedDivisionId);
            $rabsQuery->where('division_id', $selectedDivisionId);
            $productionsQuery->where('division_id', $selectedDivisionId);
        }

        $structuredData = [
            'finances' => $financesQuery->get(),
            'rabs' => $rabsQuery->get(),
            'productions' => $productionsQuery->get(),
        ];

        return view('admin.data_lake.index', compact('records', 'divisions', 'selectedDivisionId', 'statusFilter', 'mapMarkers', 'stats', 'structuredData'));
    }

    public function ingest()
    {
        $divisions = Division::all();
        
        // Load existing markers for the ingest map
        $mapMarkers = [];
        $allMapRecords = DataLakeRecord::whereNotNull('payload')->get();
        foreach ($allMapRecords as $rec) {
            $payload = is_string($rec->payload) ? json_decode($rec->payload, true) : $rec->payload;
            if (is_array($payload) && isset($payload['latitude']) && isset($payload['longitude'])) {
                $markerType = 'other';
                $markerColor = 'gray';
                if ($rec->category === 'USER_DUMP') {
                    $markerType = 'Anggota';
                    $markerColor = 'blue';
                } elseif ($rec->category === 'MAPS_LOCATION' && !empty($rec->division_id)) {
                    $markerType = 'Fasilitas Divisi';
                    $markerColor = 'green';
                } elseif ($rec->category === 'MAPS_LOCATION') {
                    $markerType = 'Tempat/Bangunan Eksternal';
                    $markerColor = 'red';
                } else {
                    $markerType = 'Aktivitas/Lainnya';
                    $markerColor = 'orange';
                }

                $mapMarkers[] = [
                    'id' => $rec->id,
                    'lat' => $payload['latitude'],
                    'lng' => $payload['longitude'],
                    'title' => $payload['company_name'] ?? $payload['title'] ?? $payload['name'] ?? 'Marker',
                    'color' => $markerColor,
                    'type' => $markerType
                ];
            }
        }

        return view('admin.data_lake.ingest', compact('divisions', 'mapMarkers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string|max:255',
            'division_id' => 'nullable|exists:divisions,id',
            'payload' => 'nullable|json', // Semi-structured
            'files.*' => 'nullable|file|max:20480', // Max 20MB per file (Unstructured)
        ]);

        $payload = $request->input('payload');
        if ($payload) {
            $payload = json_decode($payload, true);
        } else {
            $payload = [];
        }

        // Append explicit fields into payload for Maps/structured ingest
        if ($request->filled('company_name')) {
            $payload['company_name'] = $request->input('company_name');
        }
        if ($request->filled('latitude') && $request->filled('longitude')) {
            $payload['latitude'] = $request->input('latitude');
            $payload['longitude'] = $request->input('longitude');
        }

        $mediaPaths = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                // Determine upload directory
                $ext = strtolower($file->getClientOriginalExtension());
                $dir = 'data_lake/documents';
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    $dir = 'data_lake/images';
                } elseif (in_array($ext, ['mp4', 'mov', 'avi'])) {
                    $dir = 'data_lake/videos';
                }
                
                // Usually we'd use Storage::disk('public')->put(), but based on Jostru setup (maybe shared hosting)
                // We move to public/uploads
                $filename = time() . '_' . uniqid() . '.' . $ext;
                $file->move(public_path('uploads/' . $dir), $filename);
                $mediaPaths[] = 'uploads/' . $dir . '/' . $filename;
            }
        }

        DataLakeRecord::create([
            'division_id' => $request->input('division_id'),
            'category' => $request->input('category'),
            'status' => 'RAW', // Default to RAW
            'payload' => $payload,
            'media_paths' => empty($mediaPaths) ? null : $mediaPaths,
            'created_by' => auth()->id() ?? 1,
        ]);

        return redirect()->route('admin.data_lake.index', ['status' => 'RAW'])->with('success', 'Data mentah berhasil di-ingest ke Data Lake!');
    }

    public function process($id)
    {
        $record = DataLakeRecord::findOrFail($id);
        $record->update(['status' => 'PROCESSED']);
        return redirect()->back()->with('success', 'Data ditandai sebagai PROCESSED.');
    }
    
    public function destroy($id)
    {
        $record = DataLakeRecord::findOrFail($id);
        // Delete files
        if ($record->media_paths) {
            $paths = is_string($record->media_paths) ? json_decode($record->media_paths, true) : $record->media_paths;
            foreach ($paths as $path) {
                if (file_exists(public_path($path))) {
                    unlink(public_path($path));
                }
            }
        }
        $record->delete();
        return redirect()->back()->with('success', 'Data dihapus permanen dari Data Lake.');
    }
}
