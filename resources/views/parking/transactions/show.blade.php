@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Transaksi Parkir /</span> Detail
    </h4>

    <div class="row">
        <div class="col-xxl-8 col-xl-7">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detail Transaksi #{{ $transaction->id }}</h5>
                    <a href="{{ route('parking.transactions.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase">Informasi Transaksi</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Kode Transaksi</td>
                                    <td width="2%">:</td>
                                    <td>{{ $transaction->transaction_code }}</td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>:</td>
                                    <td>
                                        @if($transaction->status === 'completed')
                                            <span class="badge bg-success">Selesai</span>
                                        @elseif($transaction->status === 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @else
                                            <span class="badge bg-danger">Gagal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Metode Pembayaran</td>
                                    <td>:</td>
                                    <td>{{ ucfirst($transaction->payment_method) }}</td>
                                </tr>
                                <tr>
                                    <td>Waktu Pembayaran</td>
                                    <td>:</td>
                                    <td>{{ $transaction->paid_at ? $transaction->paid_at->format('d/m/Y H:i:s') : $transaction->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase">Detail Pembayaran</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Jumlah</td>
                                    <td width="2%">:</td>
                                    <td>Rp{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                </tr>
                                @if($transaction->payment_details && isset($transaction->payment_details['paid_amount']))
                                <tr>
                                    <td>Dibayar</td>
                                    <td>:</td>
                                    <td>Rp{{ number_format($transaction->payment_details['paid_amount'], 0, ',', '.') }}</td>
                                </tr>
                                @if(isset($transaction->payment_details['change']))
                                <tr>
                                    <td>Kembalian</td>
                                    <td>:</td>
                                    <td>Rp{{ number_format($transaction->payment_details['change'], 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase">Informasi Pengguna</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Nama</td>
                                    <td width="2%">:</td>
                                    <td>{{ $transaction->user->name }}</td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td>:</td>
                                    <td>{{ $transaction->user->username }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>:</td>
                                    <td>{{ $transaction->user->email }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase">Informasi Parkir</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Kode Parkir</td>
                                    <td width="2%">:</td>
                                    <td><span class="badge bg-primary">{{ $transaction->parkingEntry->kode_parkir }}</span></td>
                                </tr>
                                <tr>
                                    <td>Waktu Masuk</td>
                                    <td>:</td>
                                    <td>{{ $transaction->parkingEntry->entry_time->format('d/m/Y H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td>Jenis Kendaraan</td>
                                    <td>:</td>
                                    <td>{{ $transaction->parkingEntry->vehicle_type ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Nomor Plat</td>
                                    <td>:</td>
                                    <td>{{ $transaction->parkingEntry->vehicle_plate_number ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($transaction->payment_details)
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-muted text-uppercase">Detail Tambahan</h6>
                            <pre class="bg-light p-3 rounded">{{ json_encode($transaction->payment_details, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-xxl-4 col-xl-5">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('parking.management.show', $transaction->parkingEntry->id) }}" class="btn btn-primary">
                            <i class="ti ti-car"></i> Lihat Info Parkir
                        </a>
                        <a href="{{ route('parking.transactions.index') }}" class="btn btn-secondary">
                            <i class="ti ti-list-check"></i> Lihat Semua Transaksi
                        </a>
                    </div>
                </div>
            </div>
            
            @if($transaction->status !== 'completed')
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Update Status Pembayaran</h5>
                    <form method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Status Baru</label>
                            <select name="status" class="form-select">
                                <option value="completed" {{ $transaction->status === 'completed' ? 'selected' : '' }}>Selesai</option>
                                <option value="pending" {{ $transaction->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="failed" {{ $transaction->status === 'failed' ? 'selected' : '' }}>Gagal</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="ti ti-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection