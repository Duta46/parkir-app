@extends('layouts.app')

@section('title', 'Edit Data Parkir')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Parkir /</span> Edit Data
    </h4>

    <div class="row">
        <div class="col-xxl-8 col-xl-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Edit Entri Parkir #{{ $parkingEntry->id }}</h5>
                    <a href="{{ route('parking.management.show', $parkingEntry->id) }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('parking.management.update', $parkingEntry->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label class="form-label">Pengguna</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">Pilih Pengguna</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ $user->id == $parkingEntry->user_id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->username }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Waktu Masuk</label>
                            <input type="datetime-local" name="entry_time" value="{{ old('entry_time', $parkingEntry->entry_time->format('Y-m-d\TH:i')) }}" 
                                   class="form-control" required>
                            @error('entry_time')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jenis Kendaraan</label>
                            <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $parkingEntry->vehicle_type) }}"
                                   class="form-control">
                            @error('vehicle_type')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nomor Plat</label>
                            <input type="text" name="vehicle_plate_number" value="{{ old('vehicle_plate_number', $parkingEntry->vehicle_plate_number) }}"
                                   class="form-control">
                            @error('vehicle_plate_number')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Lokasi Masuk</label>
                            <input type="text" name="entry_location" value="{{ old('entry_location', $parkingEntry->entry_location) }}"
                                   class="form-control">
                            @error('entry_location')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('parking.management.show', $parkingEntry->id) }}" class="btn btn-secondary me-md-2">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-xxl-4 col-xl-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Info Entri</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="40%">Kode Parkir</td>
                            <td width="2%">:</td>
                            <td><span class="badge bg-primary">{{ $parkingEntry->kode_parkir }}</span></td>
                        </tr>
                        <tr>
                            <td>Nama Pengguna</td>
                            <td>:</td>
                            <td>{{ $parkingEntry->user->name }}</td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td>:</td>
                            <td>{{ $parkingEntry->user->username }}</td>
                        </tr>
                        <tr>
                            <td>Waktu Masuk</td>
                            <td>:</td>
                            <td>{{ $parkingEntry->entry_time->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:</td>
                            <td>
                                @if($parkingEntry->parkingExit)
                                    <span class="badge bg-success">Sudah Keluar</span>
                                @else
                                    <span class="badge bg-warning">Masih Aktif</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection