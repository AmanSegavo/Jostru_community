# Fase 2: Sistem Crowdfunding & Bursa Saham Internal

Sistem ini akan memungkinkan Jostru Holding Company untuk menggalang dana secara internal dari anggota untuk mendanai proyek-proyek khusus atau divisi tertentu. Sistem ini akan menyerupai pasar modal mini.

## User Review Required
> [!IMPORTANT]
> Mohon baca **Open Questions** di bawah. Konsep dasar ini akan menjadi pondasi untuk pembagian dividen di Fase 4 nanti.

## Open Questions
1. **Pasar Sekunder:** Apakah anggota diperbolehkan menjual saham/slot mereka ke anggota lain (Pasar Sekunder/Secondary Market) atau hanya ditahan sampai proyek selesai/dividen cair?
2. **Pembayaran:** Apakah anggota cukup mengunggah bukti transfer manual (lalu diverifikasi Admin), atau perlu memotong saldo internal (bila ada)?
3. **Divisi vs Proyek:** Apakah crowdfunding ini murni berbasis **Proyek** (misal: "Proyek Pembukaan Cabang Baru"), atau langsung membeli kepemilikan saham sebuah **Divisi** secara keseluruhan?

## Proposed Changes

---

### 1. Database Schema (Migration & Models)
Akan dibuat 2 tabel utama baru untuk mengurus transaksi crowdfunding:

#### [NEW] `CrowdfundingProject`
Menyimpan data proyek/pitching yang sedang mencari dana.
*   `title`, `description`, `image_path` (Pitch deck/Thumbnail)
*   `target_amount` (Target Dana, misal: Rp 100 Juta)
*   `price_per_share` (Harga per slot, misal: Rp 1 Juta / slot)
*   `total_shares` (100 slot)
*   `status` (ACTIVE, FUNDED, RUNNING, CANCELLED)
*   `end_date` (Batas waktu penggalangan)

#### [NEW] `CrowdfundingInvestment`
Menyimpan data pembelian slot saham oleh anggota (Portofolio).
*   `user_id` (Investor)
*   `crowdfunding_project_id` (Proyek yang didanai)
*   `shares` (Jumlah slot dibeli)
*   `amount` (Total harga)
*   `status` (PENDING, VERIFIED)
*   `payment_proof` (Foto bukti transfer)

---

### 2. Admin Command Center
Fitur khusus untuk pengurus Holding Company (Admin).

#### [NEW] `app/Http/Controllers/CrowdfundingAdminController.php`
*   Fitur membuat & merilis proposal Proyek Crowdfunding baru.
*   Panel verifikasi bukti transfer pembayaran saham dari anggota.
*   Tombol "Selesaikan Penggalangan" yang otomatis mencairkan dana ke Buku Kas Umum (`Finance`) sebagai Pemasukan.

#### [NEW] `resources/views/admin/crowdfunding/`
*   `index.blade.php`: List semua proyek.
*   `investors.blade.php`: List investor yang masuk beserta nominalnya (Cap Table).

---

### 3. Member / Investor Area
Fitur khusus untuk seluruh Anggota di Dashboard mereka.

#### [NEW] `app/Http/Controllers/CrowdfundingMemberController.php`
*   Logika untuk melihat proyek yang aktif.
*   Proses *checkout* pembelian slot saham.

#### [NEW] `resources/views/member/crowdfunding/`
*   `marketplace.blade.php`: Tampilan seperti bursa saham. Menampilkan proyek yang sedang *fundraising* dengan Progress Bar (Misal: Terkumpul 70% dari target).
*   `portfolio.blade.php`: Daftar saham/proyek yang sudah dibeli anggota beserta status verifikasinya.

---

### 4. Integrasi Sertifikat (`Shareholder`)
Jika proyek sukses didanai dan status investasi anggota adalah `VERIFIED`, sistem akan otomatis:
*   Menambahkan anggota tersebut ke tabel `Shareholder`.
*   Menerbitkan E-Certificate kepemilikan sah atas saham proyek tersebut.

## Verification Plan

### Manual Verification
1. Admin membuat Proyek Crowdfunding A dengan target Rp 10 Juta (10 slot @Rp 1 Juta).
2. Login sebagai Member Biasa, masuk ke menu Bursa Internal, lalu beli 2 slot.
3. Member unggah struk transfer.
4. Admin memverifikasi struk transfer.
5. Portofolio Member akan menampilkan kepemilikan 2 slot (20%) secara *real-time*.
6. E-Certificate saham berhasil di-generate.
