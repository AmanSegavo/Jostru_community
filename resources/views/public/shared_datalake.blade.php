<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Data Lake - Jostru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }
        .container { max-width: 800px; margin-top: 40px; }
        .card { border: none; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .glass-header {
            background: linear-gradient(135deg, #6366f1, #a855f7);
            color: white;
            padding: 20px;
            border-radius: 16px 16px 0 0;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mb-3">
            <h2 style="font-weight: 800; color: #1e293b;">Jostru Command Center</h2>
            <p class="text-muted">Laporan Rincian Data Intelligence</p>
        </div>
        
        <div class="card mb-4">
            <div class="glass-header">
                <h4 class="mb-0 fw-bold">{{ $payload['company_name'] ?? $payload['title'] ?? $payload['name'] ?? 'Data ' . $record->category }}</h4>
                <p class="mb-0" style="opacity: 0.9;">Kategori: {{ $record->category }} | Divisi: {{ $record->division->name ?? 'Global' }}</p>
            </div>
            <div class="card-body p-4">
                <div class="mb-4">
                    <span class="badge bg-{{ $record->status === 'PROCESSED' ? 'success' : 'danger' }} px-3 py-2">
                        Status: {{ $record->status }}
                    </span>
                    <span class="text-muted ms-2 small">Diinput pada: {{ $record->created_at->format('d M Y H:i') }}</span>
                </div>

                <h6 class="fw-bold text-primary mb-3">Payload Data Lengkap:</h6>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            @foreach($payload as $key => $val)
                                @if($key !== 'company_name' && $key !== 'title')
                                <tr>
                                    <th style="width: 30%; background: #f8fafc; text-transform: capitalize;">{{ str_replace('_', ' ', $key) }}</th>
                                    <td>{{ is_array($val) ? json_encode($val) : $val }}</td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($media && is_array($media) && count($media) > 0)
                <h6 class="fw-bold text-primary mt-4 mb-3">Lampiran Media:</h6>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($media as $path)
                        @if(preg_match('/\.(jpeg|jpg|gif|png|webp)$/i', $path))
                            <a href="/{{ $path }}" target="_blank">
                                <img src="/{{ $path }}" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px; border: 1px solid #ccc;">
                            </a>
                        @else
                            <a href="/{{ $path }}" target="_blank" class="btn btn-outline-secondary d-flex flex-column justify-content-center align-items-center" style="width: 120px; height: 120px; border-radius: 8px;">
                                📄 Dokumen
                            </a>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
        
        <div class="text-center text-muted small pb-4">
            Tautan dokumen digital dijamin aman dengan Signed URL Token. Valid selama 7 Hari. <br>
            &copy; 2026 Jostru Command Center.
        </div>
    </div>
</body>
</html>
