# Dokumentasi Aplikasi Parkir App - Docker Setup

## Daftar Isi
- [Overview](#overview)
- [Layanan yang Tersedia](#layanan-yang-tersedia)
- [Konfigurasi Docker](#konfigurasi-docker)
- [Akses Aplikasi](#akses-aplikasi)
- [Akses Database](#akses-database)
- [Perintah Docker](#perintah-docker)
- [Troubleshooting](#troubleshooting)

## Overview
Dokumentasi ini menjelaskan setup Docker untuk aplikasi Laravel Parkir App, termasuk cara mengakses berbagai layanan yang tersedia.

## Layanan yang Tersedia
- **Aplikasi Laravel**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
- **MySQL Database**: localhost:3307
- **Redis**: localhost:6380

## Konfigurasi Docker

### docker-compose.yml Services
1. **laravel.test** (PHP 8.2)
   - Image: sail-8.2/app
   - Port: 8000 (host) → 8000 (container)
   - Command: `php artisan serve --host=0.0.0.0 --port=8000`

2. **mysql**
   - Image: mysql/mysql-server:8.0
   - Port: 3307 (host) → 3306 (container)
   - Database: parkir_app
   - Username: root
   - Password: password

3. **redis**
   - Image: redis:alpine
   - Port: 6380 (host) → 6379 (container)

4. **phpmyadmin**
   - Image: phpmyadmin:latest
   - Port: 8080 (host) → 80 (container)
   - Database Host: mysql (inside container network)

## Akses Aplikasi

### Aplikasi Web Laravel
- URL: http://localhost:8000
- Akses untuk melihat dan mengelola aplikasi utama

### phpMyAdmin
- URL: http://localhost:8080
- **Kredensial Login:**
  - Server/host: `mysql`
  - Username: `root`
  - Password: `password`
  - Database: `parkir_app`

## Akses Database

### Mengakses MySQL dari Aplikasi Eksternal
- Host: localhost
- Port: 3307
- Username: root
- Password: password
- Database: parkir_app

### Mengakses dari dalam container
Dalam file `.env` Laravel Anda, konfigurasi database adalah:
```
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=parkir_app
DB_USERNAME=root
DB_PASSWORD=password
```

## Perintah Docker

### Menjalankan Aplikasi
```bash
cd C:\laragon\www\parkir-app
docker compose up -d
```

### Menghentikan Aplikasi
```bash
cd C:\laragon\www\parkir-app
docker compose down
```

### Restart Semua Container
```bash
cd C:\laragon\www\parkir-app
docker compose restart
```

### Restart Satu Container
```bash
# Restart Laravel
docker compose restart laravel.test

# Restart MySQL
docker compose restart mysql

# Restart Redis
docker compose restart redis

# Restart phpMyAdmin
docker compose restart phpmyadmin
```

### Lihat Log Container
```bash
# Log Laravel
docker compose logs laravel.test

# Log MySQL
docker compose logs mysql

# Log Redis
docker compose logs redis

# Log phpMyAdmin
docker compose logs phpmyadmin
```

### Akses Shell Container
```bash
# Akses Laravel container
docker compose exec laravel.test bash

# Akses MySQL container
docker compose exec mysql mysql -uroot -p

# Akses Redis container
docker compose exec redis redis-cli
```

## Troubleshooting

### Jika Aplikasi Laravel tidak bisa diakses di http://localhost:8000
1. Cek apakah container berjalan:
   ```bash
   docker compose ps
   ```
2. Cek log Laravel:
   ```bash
   docker compose logs laravel.test
   ```
3. Pastikan port 8000 tidak digunakan aplikasi lain

### Jika phpMyAdmin tidak bisa diakses di http://localhost:8080
1. Cek apakah container phpMyAdmin berjalan:
   ```bash
   docker compose ps
   ```
2. Cek log phpMyAdmin:
   ```bash
   docker compose logs phpmyadmin
   ```
3. Coba refresh browser atau clear cache

### Jika tidak bisa terhubung ke database
1. Pastikan MySQL container berjalan
2. Cek kredensial di file `.env`
3. Pastikan `DB_HOST` di `.env` adalah `mysql` (bukan localhost) saat berjalan di dalam container

### Jika mendapatkan error port sudah digunakan
1. Cek aplikasi lain yang mungkin menggunakan port tersebut
2. Ganti konfigurasi port di file `.env`

## Catatan Penting
- Jangan hapus file `.env` karena berisi konfigurasi penting untuk Docker
- Pastikan Docker Desktop sedang berjalan sebelum menjalankan aplikasi
- Semua layanan akan otomatis terhubung melalui network Docker internal
- Database MySQL hanya bisa diakses dari host melalui port 3307, bukan 3306