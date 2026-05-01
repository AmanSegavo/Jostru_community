@extends('layouts.admin')

@section('title', 'Laporan Analisis AI & Machine Learning - Jostru')

@push('styles')
    <style>
        .code-container {
            position: relative;
            background: #1e1e1e;
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            overflow-x: auto;
            margin-top: 1rem;
        }

        .code-container pre {
            margin: 0;
            color: #d4d4d4;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .copy-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: var(--radius-md);
            font-size: 0.8rem;
            cursor: pointer;
            transition: var(--transition-fast);
        }

        .copy-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.3);
        }

        .info-card {
            background: linear-gradient(135deg, rgba(var(--primary-h), var(--primary-s), var(--primary-l), 0.1), rgba(var(--secondary-h), var(--secondary-s), var(--secondary-l), 0.1));
            border-left: 4px solid var(--primary);
            padding: 1.5rem;
            border-radius: var(--radius-md);
            margin-bottom: 2rem;
        }
        
        .ai-result-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            border-top: 5px solid var(--secondary);
        }
    </style>
@endpush

@section('admin_content')
    <div class="mb-4">
        <h2 class="text-2xl font-bold mb-1">✨ Dashboard Eksekutif AI</h2>
        <p class="text-muted">Hasil analisis Machine Learning yang diproses otomatis oleh Google Colab.</p>
    </div>

    <!-- TAMPILAN HASIL AI (DARI COLAB) -->
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
                    <img src="data:image/png;base64,{{ $aiData['chart_base64'] }}" alt="Grafik AI" class="max-w-full rounded shadow-sm" style="max-height: 400px;">
                </div>
            </div>
            @endif
        </div>
    @else
        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-700 font-bold">Belum ada laporan analisis AI yang masuk.</p>
                    <p class="text-xs text-yellow-600 mt-1">Gunakan Script Colab di bawah ini untuk memulai analisis dan mengirim laporan ke dashboard ini.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- BAGIAN INSTRUKSI & SCRIPT (UNTUK COLAB) -->
    <div class="info-card">
        <h4 class="font-bold mb-2">🚀 Mesin Analisis (Google Colab)</h4>
        <ol class="list-decimal pl-5 text-sm space-y-2">
            <li>Buka <a href="https://colab.research.google.com/" target="_blank" style="color: var(--primary); font-weight: bold; text-decoration: underline;">Google Colab</a> dan buat *Notebook* baru.</li>
            <li>Klik tombol <strong>Copy Script</strong> di bawah ini, *paste* ke Colab, lalu tekan *Run*.</li>
            <li>Setelah selesai, <strong>Refresh halaman ini</strong> untuk melihat Laporan Eksekutif di atas!</li>
        </ol>
    </div>

    <div class="glass p-6 rounded-2xl">
        <div class="code-container mt-0">
            <button class="copy-btn" onclick="copyCode()">📋 Copy Script Full-Loop</button>
            <pre id="python-script">
import requests
import pandas as pd
import matplotlib.pyplot as plt
import seaborn as sns
import base64
from io import BytesIO

# 1. Konfigurasi Jaringan Jostru
BASE_URL = "https://jostru.kesug.com"
EXPORT_API = f"{BASE_URL}/api/export-waste-data?token=jostru-ai-123"
SAVE_API = f"{BASE_URL}/api/save-ai-results?token=jostru-ai-123"

print("[*] Menarik data mentah dari Jostru...")
response = requests.get(EXPORT_API)

if response.status_code == 200:
    data = response.json()
    df = pd.DataFrame(data['data'])
    
    # 2. Proses Data (Pandas)
    summary = df.groupby('type')['weight_kg'].sum().reset_index()
    
    # Menemukan jenis limbah dominan
    top_waste = summary.sort_values(by='weight_kg', ascending=False).iloc[0]
    insights = f"Dari total {data['count']} setoran, limbah terbanyak yang dikumpulkan adalah {top_waste['type']} dengan total {round(top_waste['weight_kg'], 2)} Kg. Fokuskan program daur ulang pada material ini bulan depan."
    
    # 3. Prediksi Sederhana (Contoh: Kenaikan 15% bulan depan)
    predictions = []
    for index, row in summary.iterrows():
        predictions.append({
            "type": row['type'],
            "predicted_kg": row['weight_kg'] * 1.15
        })

    # 4. Membuat Grafik Mewah & Konversi ke Base64 (Untuk dikirim)
    plt.figure(figsize=(8, 5))
    sns.barplot(x='type', y='weight_kg', data=summary, palette='magma')
    plt.title('Total Distribusi Limbah Jostru')
    plt.ylabel('Berat (Kg)')
    
    # Simpan plot ke memory
    buf = BytesIO()
    plt.savefig(buf, format="png", bbox_inches='tight', dpi=150)
    plt.close()
    
    # Encode Base64
    chart_base64 = base64.b64encode(buf.getvalue()).decode('utf-8')
    
    # 5. PUSH KEMBALI KE LARAVEL
    print("[*] Mengirim Laporan AI kembali ke Dashboard Jostru...")
    payload = {
        "summary": summary.to_dict('records'),
        "predictions": predictions,
        "chart_base64": chart_base64,
        "insights": insights
    }
    
    post_res = requests.post(SAVE_API, json=payload)
    if post_res.status_code == 200:
        print("[+] SUCCESS! Laporan Eksekutif sudah tampil di web Jostru. Silakan refresh halaman web Anda.")
    else:
        print("[-] Gagal menyimpan laporan:", post_res.text)
else:
    print(f"[-] Gagal mengambil data dari Jostru. Error: {response.status_code}")
</pre>
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
            }).catch(err => {
                alert("Gagal menyalin teks.");
            });
        }
    </script>
@endpush
