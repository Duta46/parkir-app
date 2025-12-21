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
                                    <i class="fa-solid fa-users fa-xl"></i>
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
                                    <i class="fa-solid fa-car fa-xl"></i>
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
                                    <i class="fa-solid fa-right-to-bracket fa-xl"></i>
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
                                    <i class="fa-solid fa-right-from-bracket fa-xl"></i>
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
                                    <i class="fa-solid fa-money-bill-wave fa-xl"></i>
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
                        <i class="fa-solid fa-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- Tabel Data Parkir -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Parkir Terbaru</h5>
            <a href="{{ route('parking.management.all') }}" class="btn btn-primary">
                <i class="fa-solid fa-list-check"></i> Lihat Semua
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
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="{{ route('parking.management.show', $entry->id) }}">
                                            <i class="fa-solid fa-eye me-1"></i> Detail
                                        </a>
                                        @if(!$entry->parkingExit)
                                        <a class="dropdown-item" href="{{ route('parking.transactions.payment.form', $entry->id) }}">
                                            <i class="fa-solid fa-currency-riyal me-1"></i> Bayar
                                        </a>
                                        <form method="POST" action="{{ route('parking.management.addExit', $entry->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menambahkan catatan keluar untuk entri ini?')">
                                            @csrf
                                            <input type="hidden" name="exit_time" value="{{ now()->format('Y-m-d H:i:s') }}">
                                            <!-- Biaya akan dihitung otomatis berdasarkan kebijakan 1x bayar per hari -->
                                            <button type="submit" class="dropdown-item">
                                                <i class="fa-solid fa-logout me-1"></i> Keluar
                                            </button>
                                        </form>
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
</script>
@endsection