<!-- resources/views/landing.blade.php -->
@extends('layouts.landing-page')

@section('title', 'Beranda - Sistem Parkir Kampus')

@section('body')
  <!-- Carousel -->
  <div id="landingCarousel" class="carousel slide landing-carousel" data-bs-ride="carousel">
    <div class="carousel-inner">
        @foreach($images as $index => $image)
            <div class="carousel-item {{ $index == 0 ? 'active' : '' }} bg-black">
                <img src="{{ asset($image) }}" class="d-block w-100 opacity-50" alt="Carousel {{ $index + 1 }}">
            </div>
        @endforeach
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#landingCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#landingCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>

    <!-- optional overlay text/button -->
    <div class="overlay-search">
      <h1 class="display-5 fw-bold" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">Sistem Parkir {{ $campus['name'] }}</h1>
      <p class="lead" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);">Kelola parkir kampus dengan mudah — cek transaksi, scanner QR, dan manajemen petugas.</p>
      <a href="{{ route('login') }}" class="btn btn-lg" style="background-color: rgb(237, 110, 19); border-color: ; color: white;"">Masuk ke Dashboard</a>
    </div>
  </div>

  <!-- Intro / About -->
  <section class="landing-intro bg-light">
    <div class="container intro-content">
      <div class="row align-items-center">
        <div class="col-md-6 mb-4">
          <h2>Apa itu Website Parkir?</h2>
          <p>
            Website Parkir adalah sistem informasi yang membantu pengelolaan parkir di lingkungan kampus.
            Dengan fitur QR code, pencatatan masuk/keluar, dan laporan transaksi, proses parkir menjadi lebih
            cepat, aman, dan terukur.
          </p>

          <h5 class="mt-4">Kegunaan Utama</h5>
          <ul>
            <li>Mendaftarkan dan memverifikasi kendaraan masuk/keluar dengan QR code.</li>
            <li>Mengelola data pengguna dan petugas parkir.</li>
            <li>Membuat laporan pendapatan harian dan histori transaksi.</li>
            <li>Menyediakan antarmuka admin untuk pengaturan lot parkir dan kode parkir.</li>
          </ul>
        </div>

        <div class="col-md-6">
          <!-- contoh card fitur -->
          <div class="card shadow-sm">
            <div class="card-body">
              <h5 class="card-title">Fitur Unggulan</h5>
              <p class="card-text">QR Code Dinamis, Integrasi Petugas, Pengelolaan Transaksi, dan Dashboard Statistik.</p>
              <ul>
                <li>QR Code per-user dan QR Umum</li>
                <li>Pencatatan otomatis masuk/keluar</li>
                <li>Manajemen role (Admin, Petugas, Mahasiswa)</li>
                <li>Export laporan</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="landing-footer mt-5">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <h5>{{ $campus['name'] }}</h5>
          <p>{{ $campus['address'] }}</p>
        </div>
        <div class="col-md-3">
          <h6>Kontak</h6>
          <p class="mb-0">Email: <a href="mailto:{{ $campus['email'] }}" class="text-white">{{ $campus['email'] }}</a></p>
          <p>Telepon: <a href="tel:{{ $campus['phone'] }}" class="text-white">{{ $campus['phone'] }}</a></p>
        </div>
        <div class="col-md-3">
          <h6>Jam Operasional</h6>
          <p class="mb-0">Senin - Jumat: 08:00 - 16:00</p>
          <p>Sabtu & Minggu : Tutup</p>
        </div>
      </div>

      <hr style="border-color: rgba(255,255,255,0.08)">

      <div class="text-center small">
        &copy; {{ date('Y') }} {{ $campus['name'] }} — Semua hak dilindungi.
      </div>
    </div>
  </footer>
@endsection
