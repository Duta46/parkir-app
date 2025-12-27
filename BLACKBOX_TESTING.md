# Blackbox Testing - Aplikasi Parkir

Dokumen ini menjelaskan tes fungsional untuk aplikasi parkir tanpa melihat implementasi internal. Fokus pada pengujian perilaku sistem dari perspektif pengguna.

## Struktur Aplikasi

Aplikasi ini memiliki sistem role-based access control dengan tiga jenis pengguna utama:

- **Admin**: Pengguna dengan akses penuh
- **Petugas**: Pengguna dengan akses terbatas untuk tugas operasional
- **Pengguna**: Pengguna biasa (pegawai, dosen, mahasiswa)

### Struktur Data Pengguna
- Sistem tidak menggunakan email untuk autentikasi
- Pengguna diidentifikasi dengan username
- Tipe pengguna (user_type): admin, petugas, pegawai, dosen, mahasiswa
- Nomor identitas (identity_number): NIP/NUP untuk dosen, NIM untuk mahasiswa
- Kendaraan (untuk pengguna): jenis kendaraan dan nomor plat

## Fungsionalitas Utama

### 1. Authentication (Autentikasi)

#### 1.1 Login
- **Deskripsi**: Pengguna dapat login ke sistem
- **Input**: Username dan password
- **Output**: Akses ke dashboard sesuai role
- **Catatan**: Sistem tidak menggunakan email untuk autentikasi, hanya username dan password
- **Test Cases**:
  - TC001: Login dengan kredensial valid
  - TC002: Login dengan kredensial tidak valid
  - TC003: Login dengan username yang tidak terdaftar
  - TC004: Login dengan password salah

#### 1.2 Logout
- **Deskripsi**: Pengguna dapat logout dari sistem
- **Input**: Klik tombol logout
- **Output**: Kembali ke halaman login
- **Test Cases**:
  - TC005: Logout saat login sebagai admin
  - TC006: Logout saat login sebagai petugas
  - TC007: Logout saat login sebagai pengguna

### 2. Role-based Access Control (Kontrol Akses Berdasarkan Role)

#### 2.1 Akses Dashboard
- **Deskripsi**: Setiap role memiliki akses ke dashboard berbeda
- **Input**: Login dengan role berbeda
- **Output**: Tampilan dashboard sesuai role
- **Test Cases**:
  - TC008: Admin dapat mengakses dashboard admin
  - TC009: Petugas dapat mengakses dashboard petugas
  - TC010: Pengguna dapat mengakses dashboard pengguna

#### 2.2 Akses ke Parking Transactions
- **Deskripsi**: Hanya admin dan petugas yang dapat mengakses halaman transaksi
- **Input**: Akses ke `/parking-transactions`
- **Output**: Akses diberikan atau ditolak
- **Test Cases**:
  - TC011: Admin dapat mengakses `/parking-transactions`
  - TC012: Petugas dapat mengakses `/parking-transactions`
  - TC013: Pengguna mendapatkan error 403 saat mengakses `/parking-transactions`

#### 2.3 Akses ke Parking Management
- **Deskripsi**: Hanya admin dan petugas yang dapat mengakses manajemen parkir
- **Input**: Akses ke `/parking-management`
- **Output**: Akses diberikan atau ditolak
- **Test Cases**:
  - TC014: Admin dapat mengakses `/parking-management`
  - TC015: Petugas dapat mengakses `/parking-management`
  - TC016: Pengguna mendapatkan error 403 saat mengakses `/parking-management`

#### 2.4 Akses ke Users Management
- **Deskripsi**: Hanya admin yang dapat mengelola pengguna
- **Input**: Akses ke `/users` atau `/users/{id}`
- **Output**: Akses diberikan atau ditolak
- **Catatan**:
  - Sistem tidak menggunakan email, hanya username untuk identifikasi pengguna
  - Endpoint `/users/{id}` menampilkan detail pengguna tanpa informasi email
- **Test Cases**:
  - TC017: Admin dapat mengakses `/users`
  - TC018: Petugas mendapatkan error 403 saat mengakses `/users`
  - TC019: Pengguna mendapatkan error 403 saat mengakses `/users`
  - TC020: Admin dapat mengakses `/users/{id}` untuk melihat detail pengguna
  - TC021: Petugas mendapatkan error 403 saat mengakses `/users/{id}`
  - TC022: Pengguna mendapatkan error 403 saat mengakses `/users/{id}`

### 3. Fungsionalitas Parkir

#### 3.1 Scan Barcode Masuk
- **Deskripsi**: Pengguna dapat scan barcode untuk masuk area parkir
- **Input**: Scan barcode melalui halaman `/scan-barcode`
- **Output**: Catatan entri parkir
- **Test Cases**:
  - TC020: Pengguna berhasil scan barcode masuk
  - TC021: Pengguna dengan role tidak sesuai tidak dapat mengakses halaman scan

#### 3.2 Scan Barcode Keluar
- **Deskripsi**: Pengguna dapat scan barcode untuk keluar area parkir
- **Input**: Scan barcode untuk keluar
- **Output**: Catatan exit parkir dan biaya
- **Test Cases**:
  - TC022: Pengguna berhasil scan barcode keluar
  - TC023: Pengguna tidak bisa scan keluar tanpa entri sebelumnya

#### 3.3 Admin/Petugas Scan Barcode
- **Deskripsi**: Admin atau petugas dapat scan barcode untuk pengguna
- **Input**: Akses ke `/admin-scan-barcode` dan scan barcode
- **Output**: Proses entri atau exit untuk pengguna
- **Test Cases**:
  - TC024: Admin berhasil scan barcode untuk pengguna
  - TC025: Petugas berhasil scan barcode untuk pengguna
  - TC026: Pengguna biasa tidak dapat mengakses halaman admin scan

### 4. Fungsionalitas QR Code

#### 4.1 Generate QR Code
- **Deskripsi**: Admin dan petugas dapat generate QR code
- **Input**: Akses ke `/my-qr-code` atau `/generate-qr-code`
- **Output**: QR code unik
- **Test Cases**:
  - TC027: Admin dapat generate QR code
  - TC028: Petugas dapat generate QR code
  - TC029: Pengguna tidak dapat generate QR code

### 5. Fungsionalitas Transaksi

#### 5.1 Lihat Daftar Transaksi
- **Deskripsi**: Admin dan petugas dapat melihat daftar transaksi
- **Input**: Akses ke `/parking-transactions`
- **Output**: Daftar transaksi parkir
- **Test Cases**:
  - TC030: Admin dapat melihat daftar transaksi
  - TC031: Petugas dapat melihat daftar transaksi
  - TC032: Pengguna tidak dapat melihat daftar transaksi

#### 5.2 Lihat Detail Transaksi
- **Deskripsi**: Admin dan petugas dapat melihat detail transaksi
- **Input**: Akses ke `/parking-transactions/{id}`
- **Output**: Detail transaksi tertentu
- **Test Cases**:
  - TC033: Admin dapat melihat detail transaksi
  - TC034: Petugas dapat melihat detail transaksi
  - TC035: Pengguna tidak dapat melihat detail transaksi

#### 5.3 Proses Pembayaran Cash
- **Deskripsi**: Admin dan petugas dapat memproses pembayaran cash
- **Input**: Akses ke `/parking-transactions/{id}/process-cash`
- **Output**: Konfirmasi pembayaran dan kembalian jika ada
- **Test Cases**:
  - TC036: Admin dapat memproses pembayaran cash
  - TC037: Petugas dapat memproses pembayaran cash
  - TC038: Pengguna tidak dapat memproses pembayaran cash

### 6. Fungsionalitas Manajemen Parkir

#### 6.1 Lihat Data Parkir
- **Deskripsi**: Admin dan petugas dapat melihat data parkir
- **Input**: Akses ke `/parking-management`
- **Output**: Daftar data parkir
- **Test Cases**:
  - TC039: Admin dapat melihat data parkir
  - TC040: Petugas dapat melihat data parkir
  - TC041: Pengguna tidak dapat melihat data parkir

#### 6.2 Tambah/Edit/Hapus Data Parkir
- **Deskripsi**: Admin dan petugas dapat mengelola data parkir
- **Input**: Akses ke CRUD di `/parking-management`
- **Output**: Data parkir diperbarui
- **Test Cases**:
  - TC042: Admin dapat CRUD data parkir
  - TC043: Petugas dapat CRUD data parkir
  - TC044: Pengguna tidak dapat CRUD data parkir

### 7. Fungsionalitas Profile

#### 7.1 Edit Profile
- **Deskripsi**: Semua pengguna dapat mengedit profil mereka
- **Input**: Akses ke `/profile`
- **Output**: Form untuk mengedit profil
- **Test Cases**:
  - TC045: Admin dapat edit profil
  - TC046: Petugas dapat edit profil
  - TC047: Pengguna dapat edit profil

### 8. Fungsionalitas Riwayat Parkir

#### 8.1 Lihat Riwayat Parkir
- **Deskripsi**: Pengguna dapat melihat riwayat parkir mereka
- **Input**: Akses ke `/parking-history`
- **Output**: Daftar riwayat parkir pengguna
- **Test Cases**:
  - TC048: Pengguna dapat melihat riwayat parkir mereka
  - TC049: Pengguna hanya dapat melihat riwayat mereka sendiri
  - TC050: Admin dan petugas dapat melihat riwayat pengguna tertentu

## Test Execution

### Prasyarat
- Database telah diisi dengan data seeder
- Server Laravel berjalan di http://127.0.0.1:8000

### Akun Testing
- **Admin**: username: `admin`, password: `12345678`
- **Petugas**: username: `petugas`, password: `password`
- **Pengguna**: username: `rendi`, password: `password`

### Catatan Penting
- Semua test harus dilakukan dalam lingkungan yang terisolasi
- Pastikan untuk logout setelah setiap test case
- Dokumentasikan hasil test (pass/fail) dan error yang muncul
- Lakukan test ulang jika ditemukan bug dan perbaikan telah dilakukan