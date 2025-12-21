@extends('layouts.app')

@section('title', 'Semua Data Parkir')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Parkir /</span> Semua Data
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Semua Data Parkir</h5>
            <a href="{{ route('parking.management.index') }}" class="btn btn-secondary">
                <i class="fa fa-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Kode Parkir</th>
                            <th>Pengguna</th>
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
                            <td><span class="badge bg-label-primary">{{ $entry->kode_parkir }}</span></td>
                            <td>{{ $entry->user->name }}<br><small class="text-muted">({{ $entry->user->username }})</small></td>
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
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="menu-icon fa-solid fa-ellipsis-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('parking.management.show', $entry->id) }}">
                                            <i class="fa-solid fa-eye me-1"></i> Detail
                                        </a>
                                        <a class="dropdown-item" href="{{ route('parking.management.download-pdf', $entry->id) }}" target="_blank">
                                            <i class="fa-solid fa-download me-1"></i> Download PDF
                                        </a>
                                        @if(!$entry->parkingExit)
                                        <!-- Tombol Keluar dengan Modal -->
                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#exitModal{{ $entry->id }}">
                                            <i class="fa-solid fa-right-from-bracket me-1"></i> Keluar
                                        </a>
                                        <a class="dropdown-item" href="{{ route('parking.management.edit', $entry->id) }}">
                                            <i class="fa-solid fa-pencil me-1"></i> Edit
                                        </a>
                                        @else
                                        <a class="dropdown-item" href="{{ route('parking.management.edit', $entry->id) }}">
                                            <i class="fa-solid fa-pencil me-1"></i> Edit
                                        </a>
                                        @endif
                                        <form method="POST" action="{{ route('parking.management.destroy', $entry->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus entri ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa-solid fa-trash me-1"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Modal Keluar untuk setiap entri -->
                                @if(!$entry->parkingExit)
                                <div class="modal fade" id="exitModal{{ $entry->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Proses Keluar Parkir</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('parking.management.process-exit', $entry->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Pengguna</label>
                                                        <input type="text" class="form-control" value="{{ $entry->user->name }} ({{ $entry->user->username }})" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Waktu Masuk</label>
                                                        <input type="text" class="form-control" value="{{ $entry->entry_time->format('d/m/Y H:i:s') }}" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="exit_time_{{ $entry->id }}" class="form-label">Waktu Keluar</label>
                                                        <input type="datetime-local" class="form-control" name="exit_time" id="exit_time_{{ $entry->id }}" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="exit_location_{{ $entry->id }}" class="form-label">Lokasi Keluar (Opsional)</label>
                                                        <input type="text" class="form-control" name="exit_location" id="exit_location_{{ $entry->id }}" placeholder="Lokasi keluar kendaraan">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Biaya Parkir</label>
                                                        <input type="text" class="form-control" value="Akan dihitung otomatis berdasarkan kebijakan" readonly>
                                                        <!-- Biaya akan dihitung otomatis di controller berdasarkan kebijakan 1x bayar per hari -->
                                                    </div>
                                                    <div class="alert alert-info">
                                                        <i class="fa-solid fa-info-circle"></i> Biaya akan dihitung otomatis berdasarkan kebijakan (Rp 1.000 pertama, gratis jika sudah bayar hari ini)
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Proses Keluar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted">Tidak ada data parkir ditemukan</td>
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
@endsection