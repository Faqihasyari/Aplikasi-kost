# APLIKASI SEWA KOST - PHP NATIVE

Aplikasi manajemen sewa kost berbasis PHP Native dengan tampilan dashboard modern.

## TEKNOLOGI

- PHP 8+
- MySQL
- Bootstrap 5
- Font Awesome
- JavaScript

## FITUR UTAMA

### ADMIN
- Kelola seluruh user (pemilik & pembeli)
- Monitoring data kost & transaksi
- Laporan global sistem

### PEMILIK
- Kelola kost milik sendiri
- Kelola kamar (tambah, edit, hapus)
- Kelola fasilitas & aturan kost
- Lihat penyewa AKTIF
- Lihat riwayat penyewa (yang sudah selesai kontrak)
- Kelola kontrak
- Monitoring pembayaran & tunggakan

### PEMBELI (PENYEWA)
- Registrasi & login
- Cari dan lihat detail kost
- Buat kontrak sewa langsung
- Pembayaran via QRIS
- Perpanjang kontrak
- Lihat riwayat pembayaran

## INSTALASI

### 1. Persiapan
- Install XAMPP/MAMP/Laragon
- Pastikan PHP 8+ dan MySQL aktif

### 2. Database
```sql
-- Buat database
CREATE DATABASE sewa_kost;

-- Import file database.sql
mysql -u root -p sewa_kost < database.sql
```

Atau melalui phpMyAdmin:
1. Buka phpMyAdmin
2. Buat database `sewa_kost`
3. Import file `database.sql`

### 3. Konfigurasi
Edit file `lib/database.php` jika perlu mengubah konfigurasi database:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sewa_kost');
```

### 4. Menjalankan Aplikasi
1. Copy folder `sewa_kost` ke folder `htdocs` (XAMPP) atau `www` (Laragon)
2. Akses melalui browser: `http://localhost/sewa_kost`

## AKUN DEFAULT

### Admin
- Username: `admin`
- Password: `admin123`

### Pemilik
- Username: `pemilik1`
- Password: `pemilik123`

### Pembeli
- Username: `pembeli1`
- Password: `pembeli123`

## STRUKTUR DATABASE

### Tabel Utama
1. **users** - Data user (admin, pemilik, pembeli)
2. **kost** - Data kost
3. **kamar** - Data kamar per kost
4. **fasilitas_kost** - Fasilitas yang tersedia di kost
5. **aturan_kost** - Aturan kost
6. **penyewa** - Data penyewa (status: Aktif/Selesai)
7. **kontrak** - Kontrak sewa
8. **pembayaran** - Riwayat pembayaran

### Relasi
- kost → users (pemilik)
- kamar → kost
- penyewa → users (pembeli) & kamar
- kontrak → penyewa & kamar
- pembayaran → kontrak

## ALUR PENGGUNAAN

### Pemilik
1. Login sebagai pemilik
2. Tambah data kost
3. Tambah kamar untuk kost
4. Tambah fasilitas dan aturan kost
5. Monitoring penyewa yang masuk
6. Cek pembayaran dan tunggakan

### Pembeli
1. Register akun baru (atau login)
2. Cari kost yang tersedia
3. Lihat detail kost dan kamar
4. Pilih kamar dan buat kontrak
5. Pilih durasi sewa (1-12 bulan)
6. Bayar via QRIS
7. Upload bukti pembayaran
8. Perpanjang kontrak jika diperlukan

## FITUR PEMBAYARAN

- Sistem pembayaran PER BULAN
- Pembeli bisa bayar beberapa bulan sekaligus
- Tampil QRIS untuk pembayaran
- Upload bukti pembayaran
- Otomatis tracking status: Lunas/Belum Bayar/Terlambat

## FITUR KONTRAK

- Durasi bulanan (1-12 bulan)
- Otomatis hitung tanggal berakhir
- Bisa diperpanjang
- Status: Aktif/Akan Berakhir/Selesai

## PEMISAHAN DATA PENYEWA

**PENTING**: Aplikasi memisahkan data penyewa:
- **Penyewa Aktif**: Yang masih ngekost (status = 'Aktif')
- **Riwayat Penyewa**: Yang sudah selesai kontrak (status = 'Selesai')

Data riwayat TIDAK dihapus untuk keperluan audit dan laporan.

## KEAMANAN

- Password di-hash menggunakan `password_hash()`
- SQL Injection prevention dengan `real_escape_string()`
- Session-based authentication
- Role-based access control

## STRUKTUR FOLDER

```
sewa_kost/
├── admin/          # Halaman admin
├── pemilik/        # Halaman pemilik
├── pembeli/        # Halaman pembeli
├── auth/           # Login, register, logout
├── layout/         # Header, sidebar, footer
├── lib/            # Library (database, auth)
├── assets/         # CSS, JS, images
├── database.sql    # File SQL database
└── index.php       # Entry point
```

## TROUBLESHOOTING

### Error koneksi database
- Pastikan MySQL service running
- Cek konfigurasi di `lib/database.php`
- Pastikan database `sewa_kost` sudah dibuat

### Halaman blank/error
- Aktifkan error reporting di php.ini
- Cek error log di XAMPP/Laragon
- Pastikan PHP version 8+

### Login gagal
- Pastikan sudah import `database.sql`
- Gunakan username dan password default
- Clear browser cache

## PENGEMBANGAN LANJUTAN

Fitur yang bisa ditambahkan:
1. Upload foto kost dan kamar
2. Review dan rating kost
3. Notifikasi email/SMS
4. Export laporan ke PDF/Excel
5. Dashboard analytics lebih detail
6. Integrasi payment gateway real
7. Booking online dengan down payment

## CATATAN

- Aplikasi ini menggunakan PHP Native tanpa framework
- QRIS yang ditampilkan adalah dummy/contoh
- Untuk production, implementasikan payment gateway resmi
- Backup database secara berkala

## SUPPORT

Untuk pertanyaan dan bantuan:
- Email: support@sewakost.com
- WhatsApp: 08123456789

## LICENSE

© 2024 - Aplikasi Sewa Kost. All rights reserved.
