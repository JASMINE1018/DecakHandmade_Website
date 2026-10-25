# Decak Handmade — Admin Panel v1

Panel admin untuk mengelola katalog produk handmade, pesanan manual, stok, laporan penjualan, banner promosi, dan pengaturan toko.

## Stack

- Laravel 13, PHP 8.3+ (diuji pada PHP 8.5)
- MySQL/MariaDB (InnoDB, `utf8mb4`)
- Blade + Tailwind CSS v4 + Alpine.js
- Autentikasi session dengan guard `admin`
- Penyimpanan gambar lokal (`storage/app/public`, disk `public`)
- Export laporan: PDF (DomPDF) dan XLSX (PhpSpreadsheet)

## Setup

```bash
composer install
copy .env.example .env
php artisan key:generate

# Buat database MySQL
# CREATE DATABASE decak_handmade CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

php artisan migrate:fresh --seed
php artisan storage:link

npm install
npm run build
```

Sesuaikan `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=decak_handmade
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
FILESYSTEM_DISK=public
QUEUE_CONNECTION=sync
CACHE_STORE=file
APP_URL=http://localhost:8000
```

## Menjalankan

```bash
php artisan serve
```

Buka `http://localhost:8000/admin`.

### Akun admin awal

- Email: `admin@decakhandmade.test`
- Kata sandi: `DecakAdmin2026!`

Segera ganti kata sandi melalui menu **Profil** setelah login pertama.

## Modul

- **Dashboard** — penjualan hari ini, pesanan baru, pesanan Diproses, stok menipis, grafik 7 hari, pesanan terbaru.
- **Produk** — CRUD produk, galeri (JPG/PNG → WebP), variasi warna/motif, opsi personalisasi, penyesuaian stok dengan riwayat.
- **Kategori** — CRUD kategori; kategori yang masih dipakai produk tidak dapat dihapus.
- **Pesanan** — pencatatan pesanan manual, snapshot harga/nama produk, pemotongan stok, perubahan status, pembatalan dengan pengembalian stok, riwayat status.
- **Rekap Penjualan** — ringkasan, grafik harian, rincian harian, produk terlaris, export PDF/XLSX (sheet Ringkasan, Penjualan Harian, Produk Terlaris).
- **Promosi & Banner** — CRUD banner, jadwal tayang, status aktif, validasi tautan http/https atau path internal.
- **Pengaturan** — identitas toko, prefix pesanan, ambang stok.
- **Admin** — tambah/ubah/nonaktifkan admin; tidak dapat menonaktifkan akun sendiri.

## Aturan bisnis utama

- Status pesanan: **Diproses → Dikirim → Selesai**, atau **Dibatalkan**.
- Pesanan dibuat langsung berstatus `processing`; stok dipotong saat pesanan dibuat.
- Koreksi `Dikirim → Diproses` diperbolehkan dengan catatan.
- Pembatalan dari `Dikirim` memerlukan konfirmasi barang kembali/gagal kirim.
- Penjualan diakui dari pesanan **Selesai** berdasarkan `completed_at` (WIB).
- Nilai penjualan = **subtotal produk**, tanpa ongkos kirim. `grand_total` hanya total pembayaran.
- Harga selalu dihitung ulang di server; tidak menerima harga dari klien.
- Stok hanya berubah melalui `InventoryService` dengan `lockForUpdate` dan pergerakan stok idempoten.
- Nomor pesanan: `ORD-000001` (prefix dapat diubah di Pengaturan).
- Satu item hanya boleh memakai satu opsi personalisasi bertipe teks.
- Produk tidak dapat diarsipkan jika masih dipakai pesanan Diproses/Dikirim.
- Audit log bersifat append-only.

## Pengujian

Tes menggunakan database MySQL terpisah `decak_handmade_test` (lihat `phpunit.xml`).

```bash
# Buat database test
# CREATE DATABASE decak_handmade_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

php artisan test
```

Cakupan tes: autentikasi, alur pesanan dan stok, transisi status, pembatalan, pricing server-side, laporan dan export, CRUD produk + gambar, banner, pengaturan, dan pengelolaan admin.

## Catatan implementasi

- Gambar produk disimpan sebagai WebP dengan tiga turunan: `full` (maks. 1600 px), `card` (800×800), `thumb` (400×400). Banner disimpan 1600×533.
- Upload menerima JPG/PNG maksimal 2 MB.
- Zona waktu aplikasi UTC; tanggal ditampilkan dan difilter dalam WIB (`Asia/Jakarta`).
- `APP_URL` harus sesuai host agar URL gambar `storage/...` benar.
