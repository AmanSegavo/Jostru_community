@extends('layouts.admin')
@section('title', 'Laporan Analisis AI & Machine Learning - Jostru')

@push('styles')
<style>
.code-container{position:relative;background:#1e1e1e;border-radius:var(--radius-lg);padding:1.5rem;overflow-x:auto;margin-top:1rem}
.code-container pre{margin:0;color:#d4d4d4;font-family:'Courier New',monospace;font-size:.9rem;line-height:1.5}
.copy-btn{position:absolute;top:1rem;right:1rem;background:var(--primary);color:white;border:none;padding:.5rem 1rem;border-radius:var(--radius-md);font-size:.8rem;cursor:pointer;transition:var(--transition-fast)}
.copy-btn:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(var(--primary-h),var(--primary-s),var(--primary-l),.3)}
.info-card{background:linear-gradient(135deg,rgba(var(--primary-h),var(--primary-s),var(--primary-l),.1),rgba(var(--secondary-h),var(--secondary-s),var(--secondary-l),.1));border-left:4px solid var(--primary);padding:1.5rem;border-radius:var(--radius-md);margin-bottom:2rem}
.ai-result-card{background:white;border-radius:var(--radius-lg);padding:2rem;box-shadow:0 10px 30px rgba(0,0,0,.05);margin-bottom:2rem;border-top:5px solid var(--secondary)}
</style>
@endpush

@section('admin_content')
<div class="mb-4">
    <h2 class="text-2xl font-bold mb-1">✨ Dashboard Eksekutif AI</h2>
    <p class="text-muted">Hasil analisis Machine Learning yang diproses otomatis oleh Google Colab.</p>
</div>

@if(isset($aiData))
<div class="ai-result-card">
    <div class="flex justify-between items-center mb-6 border-b pb-4">
        <h3 class="text-xl font-bold text-gray-800">Laporan Analisis Terakhir</h3>
        <span class="bg-gray-100 text-gray-500 text-sm px-3 py-1 rounded-full">Diperbarui: {{ $aiData['timestamp'] }}</span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
        <div>
            <h4 class="font-bold text-gray-700 mb-4 flex items-center"><span class="mr-2">📊</span> Ringkasan Limbah</h4>
            <div class="space-y-3">
                @foreach($aiData['summary'] as $item)
                <div class="flex justify-between items-center bg-gray-50 p-3 rounded-lg border border-gray-100">
                    <span class="font-medium text-gray-600">{{ $item['type'] }}</span>
                    <span class="font-bold text-primary">{{ number_format($item['weight_kg'], 2) }} Kg</span>
                </div>
                @endforeach
            </div>
        </div>

        <div>
            <h4 class="font-bold text-gray-700 mb-4 flex items-center"><span class="mr-2">💡</span> Wawasan AI (Insights)</h4>
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 text-blue-800 text-sm leading-relaxed">
                {{ $aiData['insights'] }}
            </div>

            @if(isset($aiData['predictions']) && count($aiData['predictions']) > 0)
            <h4 class="font-bold text-gray-700 mt-6 mb-3 flex items-center"><span class="mr-2">📈</span> Prediksi Bulan Depan</h4>
            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                @foreach($aiData['predictions'] as $pred)
                <div class="flex justify-between items-center mb-1">
                    <span class="text-green-800 text-sm">{{ $pred['type'] }}</span>
                    <span class="font-bold text-green-700">{{ number_format($pred['predicted_kg'], 2) }} Kg</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    @if(isset($aiData['chart_base64']) && !empty($aiData['chart_base64']))
    <div>
        <h4 class="font-bold text-gray-700 mb-4 border-b pb-2">Visualisasi Tren Data</h4>
        <div class="bg-gray-50 p-4 rounded-xl flex justify-center border border-gray-100">
            <img src="data:image/png;base64,{{ $aiData['chart_base64'] }}" alt="Grafik AI" class="max-w-full rounded shadow-sm" style="max-height:400px;">
        </div>
    </div>
    @endif
</div>
@else
<div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
    <div class="flex">
        <div class="flex-shrink-0"><svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg></div>
        <div class="ml-3">
            <p class="text-sm text-yellow-700 font-bold">Belum ada laporan analisis AI yang masuk.</p>
            <p class="text-xs text-yellow-600 mt-1">Gunakan Script Colab di bawah ini untuk memulai analisis.</p>
        </div>
    </div>
</div>
@endif

<div class="info-card">
    <h4 class="font-bold mb-2">🚀 Mesin Analisis (Google Colab)</h4>
    <ol class="list-decimal pl-5 text-sm space-y-2">
        <li>Buka <a href="https://colab.research.google.com/" target="_blank" style="color:var(--primary);font-weight:bold;text-decoration:underline;">Google Colab</a> dan buat Notebook baru.</li>
        <li>Klik tombol <strong>Copy Script</strong> di bawah, paste ke Colab, lalu tekan Run.</li>
        <li>Setelah selesai, <strong>Refresh halaman ini</strong> untuk melihat Laporan Eksekutif di atas.</li>
    </ol>
</div>

<div class="glass p-6 rounded-2xl">
    <div class="code-container mt-0">
        <button class="copy-btn" onclick="copyCode()">📋 Copy Script</button>
        <pre id="python-script">import requests
import pandas as pd
import urllib3
import datetime
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)
print("[*] Menarik data mentah dari Jostru...")
url_get = "https://jostru.site/api/export-waste-data?token=jostru-ai-123"
url_post = "https://jostru.site/api/save-ai-results?token=jostru-ai-123"
try:
    response = requests.get(url_get, verify=False)
    response.raise_for_status()
    df = pd.DataFrame(response.json())
    print("[v] Data berhasil ditarik!\n")
    nama_kolom_tipe = 'type'
    nama_kolom_berat = 'weight_kg'
    if nama_kolom_tipe in df.columns and nama_kolom_berat in df.columns:
        summary_df = df.groupby(nama_kolom_tipe)[nama_kolom_berat].sum().reset_index()
        limbah_dominan = summary_df.loc[summary_df[nama_kolom_berat].idxmax()]
        payload = {
            "timestamp": datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S"),
            "summary": summary_df.to_dict(orient="records"),
            "insights": f"Berdasarkan analisis bulan ini, limbah terbanyak adalah {limbah_dominan[nama_kolom_tipe]} dengan total {limbah_dominan[nama_kolom_berat]} Kg. Disarankan untuk memfokuskan program daur ulang pada jenis limbah ini.",
            "predictions": [],
            "chart_base64": ""
        }
        print("[*] Mengirim laporan analisis ke Dashboard Jostru...")
        post_response = requests.post(url_post, json=payload, verify=False)
        post_response.raise_for_status()
        print("[v] Laporan berhasil dikirim! Silakan refresh dashboard Laravel Anda.")
    else:
        print("[X] ERROR: Kolom tidak ditemukan dalam data.")
except Exception as e:
    print(f"Terjadi kesalahan: {e}")</pre>
    </div>
</div>
@endsection

@push('scripts')
<script>
function copyCode() {
    const codeText = document.getElementById("python-script").innerText;
    navigator.clipboard.writeText(codeText).then(() => {
        const btn = document.querySelector('.copy-btn');
        const originalText = btn.innerText;
        btn.innerText = '✅ Tersalin!';
        btn.style.background = '#10b981';
        setTimeout(() => {
            btn.innerText = originalText;
            btn.style.background = 'var(--primary)';
        }, 2000);
    }).catch(() => alert("Gagal menyalin teks."));
}
</script>
@endpush