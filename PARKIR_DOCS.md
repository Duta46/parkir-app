# Sistem Parkir QR Code - Dokumentasi

## Deskripsi
Sistem ini adalah aplikasi manajemen parkir berbasis QR code yang memungkinkan pengguna untuk masuk dan keluar dari area parkir menggunakan QR code unik harian.

## Fitur Utama
1. Generate QR code unik harian untuk setiap pengguna
2. Scan QR code untuk masuk dan keluar dari area parkir
3. Pelacakan waktu parkir dan biaya
4. Riwayat masuk/keluar parkir
5. Refresh otomatis QR code setiap hari

## Struktur Sistem

### Model-Model
- `QrCode`: Menyimpan QR code harian unik untuk setiap pengguna
- `ParkingEntry`: Mencatat waktu masuk parkir
- `ParkingExit`: Mencatat waktu keluar parkir dan biaya

### Fungsi Utama
- Setiap pengguna mendapatkan QR code unik setiap hari
- QR code hanya berlaku satu hari dan kadaluarsa di akhir hari
- Hanya satu sesi parkir yang aktif per pengguna
- Biaya parkir dihitung berdasarkan durasi waktu (Rp 5.000 per jam)

### Proses Masuk/Keluar
1. **Masuk**: Pengguna scan QR code mereka untuk mencatat waktu masuk
2. **Keluar**: Pengguna scan QR code mereka lagi untuk mencatat waktu keluar dan menghitung biaya

## Penggunaan

### Untuk Pengguna
1. Akses dashboard untuk melihat QR code harian Anda
2. Gunakan fitur scan untuk masuk atau keluar
3. Lihat riwayat parkir Anda di bagian bawah halaman

### Untuk Admin
- Gunakan perintah `php artisan parkir:refresh-qr-harian` untuk memperbarui semua QR code secara manual
- Sistem otomatis menjalankan perintah ini setiap hari pukul 00:01

## Konfigurasi
- Biaya parkir: Rp 5.000 per jam (dapat dimodifikasi di ParkingController)
- Waktu refresh QR code: 00:01 setiap hari

## API Endpoints
- `GET /dashboard` - Halaman utama sistem parkir
- `POST /scan-entry` - Endpoint untuk scan masuk
- `POST /scan-exit` - Endpoint untuk scan keluar
- `POST /generate-qr-code` - Generate ulang QR code

## Database Schema
- `qr_codes`: user_id, code, date, is_used, expires_at
- `parking_entries`: user_id, qr_code_id, entry_time, entry_location, vehicle_type, vehicle_plate_number
- `parking_exits`: user_id, parking_entry_id, exit_time, exit_location, parking_fee