@extends('layouts.app')

@section('title', 'Hasil Pencarian Data Parkir')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Parkir /</span> Hasil Pencarian
    </h4>

    <!-- Form Pencarian -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('parking.management.search') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Pengguna</label>
                    <select name="user_id" class="form-select">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $user)
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

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Hasil Pencarian</h5>
            <a href="{{ route('parking.management.index') }}" class="btn btn-secondary">
                <i class="ti ti-arrow-left"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
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
                            <td>#{{ $entry->id }}</td>
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
                                        <i class="fa-solid fa-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('parking.management.show', $entry->id) }}">
                                            <i class="fa-solid fa-eye me-1"></i> Detail
                                        </a>
                                        @if(!$entry->parkingExit)
                                        <a class="dropdown-item" href="{{ route('parking.transactions.payment.form', $entry->id) }}">
                                            <i class="fa-solid fa-currency-riyal me-1"></i> Bayar
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
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted">Tidak ada data ditemukan berdasarkan kriteria pencarian</td>
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