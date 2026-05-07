@extends('layouts.app')
@section('title', 'Kebijakan Privasi - Jostru Community')

@push('styles')
<style>
    .policy-container {
        max-width: 900px;
        margin: 40px auto;
        padding: 40px;
        background: var(--surface-color);
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .policy-content h2 {
        color: var(--primary);
        font-weight: 700;
        margin-top: 30px;
        margin-bottom: 15px;
        font-size: 1.5rem;
    }
    .policy-content p, .policy-content li {
        color: var(--text-secondary);
        line-height: 1.8;
    }
</style>
@endpush

@section('content')
<div class="container mb-5">
    <div class="policy-container glass">
        <h1 class="fw-bold mb-3 text-center">Kebijakan Privasi</h1>
        <p class="text-center text-muted mb-5">Terakhir diperbarui: {{ date('d M Y') }}</p>

        <div class="policy-content">
            <p>Di Jostru Community, kami sangat menghargai privasi dan keamanan data pengguna kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan layanan kami.</p>

            <h2>1. Informasi yang Kami Kumpulkan</h2>
            <p>Kami mengumpulkan informasi berikut saat Anda mendaftar dan menggunakan aplikasi Jostru:</p>
            <ul>
                <li><strong>Informasi Profil:</strong> Nama lengkap, alamat email, nomor telepon, dan tanggal lahir.</li>
                <li><strong>Data Lokasi:</strong> Koordinat (Latitude & Longitude) tempat tinggal untuk mempermudah penjemputan limbah (hanya dikumpulkan dengan izin Anda).</li>
                <li><strong>Data Aktivitas:</strong> Foto/video limbah yang Anda unggah, riwayat setoran, dan aktivitas di komunitas.</li>
            </ul>

            <h2>2. Penggunaan Informasi</h2>
            <p>Informasi yang kami kumpulkan digunakan untuk:</p>
            <ul>
                <li>Memverifikasi identitas Anda sebagai anggota resmi.</li>
                <li>Memproses laporan setoran limbah dan menghitung estimasi poin.</li>
                <li>Berkomunikasi dengan Anda terkait layanan, pembaruan, dan notifikasi penting.</li>
                <li>Meningkatkan pengalaman pengguna (UI/UX) dan mengembangkan fitur baru.</li>
            </ul>

            <h2>3. Keamanan Data</h2>
            <p>Kami menerapkan langkah-langkah keamanan teknis dan administratif untuk melindungi data Anda dari akses, penggunaan, atau pengungkapan yang tidak sah. Kata sandi Anda dienkripsi secara aman dalam basis data kami.</p>

            <h2>4. Berbagi Informasi</h2>
            <p>Kami tidak akan pernah menjual atau menyewakan informasi pribadi Anda kepada pihak ketiga. Data Anda hanya dapat dibagikan dengan mitra logistik kami semata-mata untuk keperluan penjemputan limbah, dengan persetujuan Anda.</p>

            <h2>5. Hak Anda</h2>
            <p>Anda memiliki hak untuk:</p>
            <ul>
                <li>Mengakses dan memperbarui informasi profil Anda kapan saja.</li>
                <li>Meminta penghapusan akun beserta seluruh data yang terkait melalui layanan pelanggan kami.</li>
            </ul>

            <h2>6. Hubungi Kami</h2>
            <p>Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini, silakan hubungi tim dukungan kami melalui formulir kontak di beranda atau email ke <strong>support@jostru.site</strong>.</p>
        </div>
    </div>
</div>
@endsection
