@extends('layouts.app')

@section('title', 'Riwayat Parkir Saya')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Riwayat Parkir Saya</h4>

    <!-- Statistik Pengguna -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-info">
                                    <i class="fa-solid fa-car fa-xl"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-body mb-1">Total Masuk</div>
                            <h5 class="mb-0">{{ $totalEntries }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-warning">
                                    <i class="fa-solid fa-hourglass-half fa-xl"></i>
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

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-success">
                                    <i class="fa-solid fa-right-from-bracket fa-xl"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-body mb-1">Total Keluar</div>
                            <h5 class="mb-0">{{ $totalExits }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar">
                                <span class="avatar-initial rounded bg-label-danger">
                                    <i class="fa-solid fa-money-bill-wave fa-xl"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-body mb-1">Total Biaya</div>
                            <h5 class="mb-0">Rp{{ number_format($totalSpent, 0, ',', '.') }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Parkir -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Riwayat Parkir Lengkap</h5>
            <a href="{{ route('scan.barcode.page') }}" class="btn btn-primary">
                <i class="fa-solid fa-qrcode"></i> Scan Barcode
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kode Parkir</th>
                            <th>Tanggal</th>
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
                        <tr>
                            <td>{{ $entry->kode_parkir }}</td>
                            <td>{{ $entry->entry_time->format('d/m/Y') }}</td>
                            <td>{{ $entry->entry_time->format('H:i:s') }}</td>
                            <td>
                                @if($entry->parkingExit)
                                    {{ $entry->parkingExit->exit_time->format('H:i:s') }}
                                @else
                                    <span class="text-warning">Belum keluar</span>
                                @endif
                            </td>
                            <td>{{ $entry->vehicle_type ?? '-' }}</td>
                            <td>{{ $entry->vehicle_plate_number ?? '-' }}</td>
                            <td>
                                @if($entry->parkingExit)
                                    Rp{{ number_format($entry->parkingExit->parking_fee, 0, ',', '.') }}
                                @else
                                    Rp1.000
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
                                <a href="{{ route('parking.history.detail', $entry->id) }}" class="btn btn-sm btn-outline-primary me-1">
                                    <i class="fa-solid fa-eye"></i> Lihat Detail
                                </a>
                                <a href="{{ route('parking.history.download-pdf', $entry->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="fa-solid fa-download"></i> PDF
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Tidak ada riwayat parkir</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $parkingEntries->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection