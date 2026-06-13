<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ChatbotService
{
    public function getResponse($message)
    {
        $provider = Setting::getVal('llm_provider', 'rule_based');
        $apiKey = Setting::getVal('llm_api_key', '');
        $systemPrompt = Setting::getVal('llm_system_prompt', 'Anda adalah Asisten Virtual Holding Company Jostru. Jawablah dengan ringkas dan profesional.');

        if ($provider === 'openai' && !empty($apiKey)) {
            return $this->getOpenAIResponse($message, $apiKey, $systemPrompt);
        } elseif ($provider === 'gemini' && !empty($apiKey)) {
            return $this->getGeminiResponse($message, $apiKey, $systemPrompt);
        } else {
            return $this->getRuleBasedResponse($message);
        }
    }

    private function getOpenAIResponse($message, $apiKey, $systemPrompt)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $message],
                ],
                'max_tokens' => 300
            ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content') ?? 'Maaf, respons tidak dapat dibaca.';
            }
            return 'API Error: ' . $response->body();
        } catch (\Exception $e) {
            return 'Gagal terhubung ke OpenAI: ' . $e->getMessage();
        }
    }

    private function getGeminiResponse($message, $apiKey, $systemPrompt)
    {
        try {
            // Gunakan API Gemini
            $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key={$apiKey}", [
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $systemPrompt . "\n\nPertanyaan User: " . $message]]]
                ]
            ]);

            if ($response->successful()) {
                return $response->json('candidates.0.content.parts.0.text') ?? 'Maaf, respons tidak dapat dibaca.';
            }
            return 'API Error: ' . $response->body();
        } catch (\Exception $e) {
            return 'Gagal terhubung ke Gemini: ' . $e->getMessage();
        }
    }

    private function getRuleBasedResponse($message)
    {
        $msg = strtolower($message);

        // --- RULE-BASED DICTIONARY FOR JOSTRU ---
        if (Str::contains($msg, ['data lake', 'datalake'])) {
            return "Data Lake adalah pusat intelijen Holding Jostru yang berfungsi menampung berbagai macam data (Terstruktur maupun File/Media mentah) secara dinamis tanpa batasan kolom tabel. Anda bisa melihat sebaran lokasi divisi atau proyek di menu Spatial Map Data Lake.";
        }
        
        if (Str::contains($msg, ['dividen', 'saham'])) {
            return "Sistem Dividen memungkinkan pemegang saham (Shareholder) untuk menscan barcode kartu anggotanya menggunakan Scanner QRCode di halaman Admin, lalu mencairkan hak dividennya secara tunai dari kas Holding.";
        }

        if (Str::contains($msg, ['tambah anggota', 'daftar anggota'])) {
            return "Untuk menambah anggota baru, silakan buka menu 'Data Anggota' di sidebar kiri Admin, lalu klik tombol 'Tambah Anggota Baru'. Anda bisa mengatur kartu 2FA (NFC/Barcode) miliknya di menu 'Kartu Autentikasi'.";
        }

        if (Str::contains($msg, ['uang', 'keuangan', 'kas', 'rab'])) {
            return "Modul Keuangan mencatat arus kas (Pemasukan/Pengeluaran). Jika uang ditransfer antar anggota/divisi, Anda bisa memanfaatkan fitur 'Delegasi Izin Pencairan' di mana Superadmin harus menekan tombol 'ACC' sebelum dana resmi cair.";
        }
        
        if (Str::contains($msg, ['hutang', 'piutang', 'pinjaman'])) {
            return "Fitur Hutang memungkinkan pencatatan pinjaman antar divisi, maupun anggota ke pihak eksternal. Status lunas bisa dilacak pada tabel hutang-piutang.";
        }

        if (Str::contains($msg, ['sampah', 'bank sampah'])) {
            return "Sistem Bank Sampah memungkinkan nasabah mencatat setorannya, dan admin dapat menetapkan saldo kas yang bertambah ke dompet (finance) milik nasabah tersebut.";
        }

        if (Str::contains($msg, ['halo', 'hai', 'pagi', 'siang', 'malam'])) {
            return "Halo! Saya adalah Asisten AI Jostru (Berjalan di Mode Offline/Aturan Dasar). Ada yang bisa saya bantu terkait sistem Holding Company ini?";
        }

        return "Maaf, karena saya berjalan pada **Mode Rule-Based (Tanpa API)**, saya tidak mengenali konteks pertanyaan tersebut. Silakan tanyakan hal seputar: 'Data Lake', 'Keuangan', 'Dividen', 'Hutang', atau masukkan API Key OpenAI/Gemini di Pengaturan untuk jawaban tanpa batas.";
    }
}
