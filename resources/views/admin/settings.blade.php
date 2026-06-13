@extends('layouts.admin')

@section('title', 'Pengaturan Sistem')
@section('page_title', 'Pengaturan Sistem')

@section('admin_content')
<div class="row">
    <div class="col-md-8 mx-auto">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Pengaturan Sistem Limbah</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf

                    <!-- Chatbot Settings (Fase 1 Holding Company) -->
                    <div class="mb-5 p-3" style="background: rgba(99, 102, 241, 0.05); border-radius: 12px; border-left: 4px solid #6366f1;">
                        <h5 class="fw-bold mb-3" style="color: #6366f1;"><i class="bi bi-robot"></i> Modul AI Chatbot Asisten</h5>
                        <p class="text-muted small">Jika API Key LLM kosong, chatbot akan menggunakan mode Rule-Based lokal.</p>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" role="switch" id="chatbotToggle" name="chatbot_active" value="1" {{ ($settings['chatbot_active'] ?? '0') == '1' ? 'checked' : '' }} style="transform: scale(1.2); margin-left: -2em;">
                            <label class="form-check-label fw-bold ms-2" for="chatbotToggle">Aktifkan Widget Chatbot di layar Admin?</label>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Provider LLM</label>
                            <select name="llm_provider" class="form-select">
                                <option value="gemini" {{ ($settings['llm_provider'] ?? '') == 'gemini' ? 'selected' : '' }}>Google Gemini API (Direkomendasikan)</option>
                                <option value="openai" {{ ($settings['llm_provider'] ?? '') == 'openai' ? 'selected' : '' }}>OpenAI / ChatGPT API</option>
                                <option value="rule_based" {{ ($settings['llm_provider'] ?? '') == 'rule_based' ? 'selected' : '' }}>Rule-Based (Offline/Keyword)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">API Key (Opsional)</label>
                            <input type="password" name="llm_api_key" class="form-control font-monospace" placeholder="sk-..." value="{{ $settings['llm_api_key'] ?? '' }}">
                        </div>
                    </div>

                    <hr class="mb-4">
                    <div class="mb-4">
                        <label class="form-label fw-bold">Skenario Input Limbah</label>
                        <select name="waste_input_mode" class="form-select">
                            <option value="both" {{ ($settings['waste_input_mode'] ?? '') == 'both' ? 'selected' : '' }}>Anggota & Admin Dapat Menginput</option>
                            <option value="admin_only" {{ ($settings['waste_input_mode'] ?? '') == 'admin_only' ? 'selected' : '' }}>Hanya Admin Yang Dapat Menginput</option>
                            <option value="member_only" {{ ($settings['waste_input_mode'] ?? '') == 'member_only' ? 'selected' : '' }}>Hanya Anggota Yang Dapat Menginput Mandiri</option>
                        </select>
                        <small class="text-muted d-block mt-1">Mengatur siapa yang diizinkan menambahkan riwayat limbah.</small>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
