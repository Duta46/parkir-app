@extends('layouts.app')

@section('title', 'Manajemen Parkir')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Parkir /</span> Dashboard
    </h4>

    <!-- Statistik Utama -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="ti ti-users ti-lg"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-body mb-1">Total Pengguna</div>
                            <h5 class="mb-0">{{ $totalUsers }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="ti ti-car ti-lg"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-body mb-1">Sesi Aktif</div>
                            <h5 class="mb-0">{{ $activeEntries }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-primary">
                                    <i class="ti ti-login ti-lg"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-body mb-1">Masuk Hari Ini</div>
                            <h5 class="mb-0">{{ $totalEntriesToday }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="ti ti-logout ti-lg"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-body mb-1">Keluar Hari Ini</div>
                            <h5 class="mb-0">{{ $totalExitsToday }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="ti ti-currency-riyal ti-lg"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-body mb-1">Pendapatan Hari Ini</div>
                            <h5 class="mb-0">Rp{{ number_format($totalRevenueToday, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Bulanan -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-header">Statistik Bulan Ini</h5>
                    <div class="card-datatable table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td>Total Masuk</td>
                                <td class="text-end">{{ $totalEntriesThisMonth }}</td>
                            </tr>
                            <tr>
                                <td>Total Pendapatan</td>
                                <td class="text-end">Rp{{ number_format($totalRevenueThisMonth, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-header">Pendapatan per Jenis Kendaraan (Hari Ini)</h5>
                    @if($revenueByVehicleType->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                @foreach($revenueByVehicleType as $item)
                                <tr>
                                    <td>{{ $item->vehicle_type ?: '-' }}</td>
                                    <td class="text-end">Rp{{ number_format($item->total_revenue, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Tidak ada data untuk hari ini</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Form Pencarian -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('parking.management.search') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Pengguna</label>
                    <select name="user_id" class="form-select select2">
                        <option value="">Semua Pengguna</option>
                        @foreach(\App\Models\User::all() as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->username }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Tanggal Masuk</label>
                    <input type="date" name="entry_date" value="{{ request('entry_date') }}" 
                           class="form-control">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Jenis Kendaraan</label>
                    <select name="vehicle_type" class="form-select">
                        <option value="">Semua Jenis</option>
                        <option value="Mobil" {{ request('vehicle_type') == 'Mobil' ? 'selected' : '' }}>Mobil</option>
                        <option value="Motor" {{ request('vehicle_type') == 'Motor' ? 'selected' : '' }}>Motor</option>
                        <option value="Truk" {{ request('vehicle_type') == 'Truk' ? 'selected' : '' }}>Truk</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="ti ti-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Section untuk Generate QR Code Umum -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Generate Barcode Umum</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle"></i>
                        <strong>Barcode Umum:</strong> Barcode ini bisa digunakan oleh semua pengguna (mahasiswa, dosen, pegawai, admin, petugas) untuk masuk dan keluar parkir. Berlaku hanya untuk hari ini.
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 text-center">
                    <button id="generateQrUmumBtn" class="btn btn-success btn-lg">
                        <i class="ti ti-qrcode"></i> Generate Barcode Umum Hari Ini
                    </button>
                    <div id="qrCodeUmumContainer" class="mt-3" style="display: none;">
                        <div id="qrCodeUmumImage"></div>
                        <p class="mt-2"><small class="text-muted">Barcode di atas berlaku hanya untuk hari ini</small></p>
                    </div>
                    <div id="qrCodeUmumStatus" class="mt-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section untuk Proses Keluar Berdasarkan Kode Parkir -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Proses Keluar Berdasarkan Kode Parkir</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle"></i>
                        <strong>Proses Keluar:</strong> Masukkan kode parkir yang diberikan oleh pengguna untuk memproses keluar
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <div class="input-group">
                        <input type="text" id="kodeParkirInput" class="form-control" placeholder="Masukkan kode parkir..." aria-label="Kode parkir">
                        <button class="btn btn-danger" type="button" id="prosesKeluarBtn">
                            <i class="ti ti-door-exit"></i> Proses Keluar
                        </button>
                    </div>
                    <div id="prosesKeluarStatus" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Data Parkir -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Parkir Terbaru</h5>
            <a href="{{ route('parking.management.all') }}" class="btn btn-primary">
                <i class="ti ti-list-check"></i> Lihat Semua
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="parkingTableBody">
                    <thead>
                        <tr>
                            <th>Kode Parkir / Pengguna</th>
                            <th>Masuk</th>
                            <th>Keluar</th>
                            <th>Kendaraan</th>
                            <th>Plat</th>
                            <th>Biaya</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($parkingEntries as $entry)
                        <tr id="entry-{{ $entry->id }}">
                            <td>
                                <span class="badge bg-label-primary">{{ $entry->kode_parkir }}</span>
                                <br>
                                <small>{{ $entry->user->name }} ({{ $entry->user->username }})</small>
                            </td>
                            <td>{{ $entry->entry_time->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @if($entry->parkingExit)
                                    {{ $entry->parkingExit->exit_time->format('d/m/Y H:i:s') }}
                                @else
                                    <span class="text-warning">Belum keluar</span>
                                @endif
                            </td>
                            <td>{{ $entry->vehicle_type ?: '-' }}</td>
                            <td>{{ $entry->vehicle_plate_number ?: '-' }}</td>
                            <td>
                                @if($entry->parkingExit)
                                    Rp{{ number_format($entry->parkingExit->parking_fee, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($entry->parkingExit)
                                    <span class="badge bg-success">Selesai</span>
                                @else
                                    <span class="badge bg-warning">Aktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('parking.management.show', $entry->id) }}">
                                            <i class="ti ti-eye me-1"></i> Detail
                                        </a>
                                        @if(!$entry->parkingExit)
                                        <a class="dropdown-item" href="{{ route('parking.transactions.payment.form', $entry->id) }}">
                                            <i class="ti ti-currency-riyal me-1"></i> Bayar
                                        </a>
                                        <form method="POST" action="{{ route('parking.management.addExit', $entry->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menambahkan catatan keluar untuk entri ini?')">
                                            @csrf
                                            <input type="hidden" name="exit_time" value="{{ now()->format('Y-m-d H:i:s') }}">
                                            <input type="hidden" name="parking_fee" value="5000">
                                            <button type="submit" class="dropdown-item">
                                                <i class="ti ti-logout me-1"></i> Keluar
                                            </button>
                                        </form>
                                        <a class="dropdown-item" href="{{ route('parking.management.edit', $entry->id) }}">
                                            <i class="ti ti-pencil me-1"></i> Edit
                                        </a>
                                        @else
                                        <a class="dropdown-item" href="{{ route('parking.management.edit', $entry->id) }}">
                                            <i class="ti ti-pencil me-1"></i> Edit
                                        </a>
                                        @endif
                                        <form method="POST" action="{{ route('parking.management.destroy', $entry->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus entri ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="ti ti-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Tidak ada data parkir ditemukan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $parkingEntries->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    // Fungsi untuk generate QR code umum
    document.getElementById('generateQrUmumBtn').addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Menghasilkan...';

        fetch('{{ route("parking.management.generate-qr-umum") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tampilkan QR code sebagai image
                const qrCodeImageUrl = `data:image/svg+xml;base64,${btoa(
                    '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">' +
                    '<rect width="200" height="200" fill="white"/>' +
                    '<text x="100" y="100" font-size="16" text-anchor="middle" alignment-baseline="middle" fill="black">' + data.qr_code.substring(0, 20) + '...</text>' +
                    '</svg>'
                )}`;

                document.getElementById('qrCodeUmumImage').innerHTML = `
                    <img src="data:image/svg+xml;base64,${btoa(
                        `<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
                            <rect width="200" height="200" fill="white"/>
                            <text x="100" y="100" font-size="12" text-anchor="middle" alignment-baseline="middle" fill="black">${data.qr_code.substring(0, 25)}...</text>
                        </svg>`
                    )}" alt="QR Code Umum" class="img-fluid border border-1 rounded">
                    <p class="mt-2">Kode: ${data.qr_code}</p>
                `;

                document.getElementById('qrCodeUmumContainer').style.display = 'block';
                document.getElementById('qrCodeUmumStatus').innerHTML =
                    `<div class="alert alert-success mt-2">${data.message}</div>`;
            } else {
                document.getElementById('qrCodeUmumStatus').innerHTML =
                    `<div class="alert alert-danger mt-2">Gagal: ${data.message || 'Terjadi kesalahan'}</div>`;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('qrCodeUmumStatus').innerHTML =
                `<div class="alert alert-danger mt-2">Terjadi kesalahan saat menghubungi server</div>`;
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // Fungsi untuk proses keluar berdasarkan kode parkir
    document.getElementById('prosesKeluarBtn').addEventListener('click', function() {
        const kodeParkir = document.getElementById('kodeParkirInput').value.trim();

        if (!kodeParkir) {
            document.getElementById('prosesKeluarStatus').innerHTML =
                `<div class="alert alert-warning">Silakan masukkan kode parkir</div>`;
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> Memproses...';

        fetch('{{ route("parking.scan.exit") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                kode_parkir: kodeParkir
            })
        })
        .then(response => response.json())
        .then(data => {
            let alertClass = data.success ? 'alert-success' : 'alert-danger';
            document.getElementById('prosesKeluarStatus').innerHTML =
                '<div class="alert ' + alertClass + '">' + data.message + '</div>';

            if (data.success) {
                // Kosongkan input setelah berhasil
                document.getElementById('kodeParkirInput').value = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('prosesKeluarStatus').innerHTML =
                '<div class="alert alert-danger">Terjadi kesalahan saat menghubungi server</div>';
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    });

    // Tambahkan juga event listener untuk tombol enter di input
    document.getElementById('kodeParkirInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('prosesKeluarBtn').click();
        }
    });
</script>
@endsection