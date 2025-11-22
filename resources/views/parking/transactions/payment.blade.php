@extends('layouts.app')

@section('title', 'Form Pembayaran Parkir')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Transaksi Parkir /</span> Pembayaran
    </h4>

    <div class="row">
        <div class="col-xxl-8 col-xl-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Form Pembayaran untuk Kode Parkir: {{ $parkingEntry->kode_parkir }}</h5>
                    <a href="{{ route('parking.management.show', $parkingEntry->id) }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('parking.transactions.process-cash', $parkingEntry->id) }}">
                        @csrf
                        
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase">Informasi Parkir</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Kode Parkir</td>
                                    <td width="2%">:</td>
                                    <td><span class="badge bg-primary">{{ $parkingEntry->kode_parkir }}</span></td>
                                </tr>
                                <tr>
                                    <td>Nama Pengguna</td>
                                    <td>:</td>
                                    <td>{{ $parkingEntry->user->name }} ({{ $parkingEntry->user->username }})</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase">Detail Waktu</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Waktu Masuk</td>
                                    <td width="2%">:</td>
                                    <td>{{ $parkingEntry->entry_time->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td>Waktu Saat Ini</td>
                                    <td>:</td>
                                    <td>{{ now()->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="text-muted text-uppercase">Estimasi Biaya</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Durasi Terhitung</td>
                                    <td width="2%">:</td>
                                    <td>
                                        @php
                                            $entryTime = $parkingEntry->entry_time;
                                            $currentTime = now();
                                            $hours = $entryTime->diffInHours($currentTime, false);
                                            $hours = max(1, ceil($hours));
                                            
                                            // Assuming Rp 5.000 per hour
                                            $estimatedFee = $hours * 5000;
                                        @endphp
                                        {{ $hours }} jam
                                    </td>
                                </tr>
                                <tr>
                                    <td>Biaya Estimasi</td>
                                    <td>:</td>
                                    <td>Rp{{ number_format($estimatedFee, 0, ',', '.') }} (@php echo $hours; @endphp jam × Rp5.000/jam)</td>
                                </tr>
                            </table>
                        </div>
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                @if(session('required_amount'))
                                    <p class="mb-0 mt-1">Jumlah yang harus dibayar: Rp{{ number_format(session('required_amount'), 0, ',', '.') }}</p>
                                    @if(session('paid_amount'))
                                        <p class="mb-0">Dibayar: Rp{{ number_format(session('paid_amount'), 0, ',', '.') }}</p>
                                    @endif
                                @endif
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah yang Dibayar (Rp)</label>
                            <input type="number" name="paid_amount" value="{{ old('paid_amount', $expectedAmount) }}" 
                                   class="form-control" min="{{ $expectedAmount }}" required>
                            <div class="form-text">Jumlah minimal yang harus dibayar adalah Rp{{ number_format($expectedAmount, 0, ',', '.') }}</div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('parking.management.show', $parkingEntry->id) }}" class="btn btn-secondary me-md-2">
                                Batal
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="ti ti-currency-riyal"></i> Proses Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-xxl-4 col-xl-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Kendaraan</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td width="30%">Jenis Kendaraan</td>
                            <td width="2%">:</td>
                            <td>{{ $parkingEntry->vehicle_type ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Nomor Plat</td>
                            <td>:</td>
                            <td>{{ $parkingEntry->vehicle_plate_number ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Lokasi Masuk</td>
                            <td>:</td>
                            <td>{{ $parkingEntry->entry_location ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td>Tanggal</td>
                            <td>:</td>
                            <td>{{ $parkingEntry->entry_time->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td>Waktu</td>
                            <td>:</td>
                            <td>{{ $parkingEntry->entry_time->format('H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Catatan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><strong>Metode Pembayaran:</strong> Cash</p>
                    <p class="mb-0"><strong>Status:</strong> Pembayaran langsung diselesaikan</p>
                    <hr>
                    <p class="text-muted mb-0">Setelah pembayaran selesai, pengguna dapat keluar dari area parkir dengan menunjukkan bukti pembayaran kepada petugas.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection