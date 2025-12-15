@extends('layouts.app')

@section('title', 'Detail Data Parkir #'.$parkingEntry->id)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw/light">Manajemen Parkir /</span> Detail Data Parkir #{{ $parkingEntry->id }}
    </h4>

    <div class="row">
        <div class="col-xxl-8 col-xl-7 col-lg-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Detail Data Parkir #{{ $parkingEntry->id }}</h5>
                    <a href="{{ route('parking.management.index') }}" class="btn btn-secondary">
                        <i class="ti ti-arrow-left"></i> Kembali
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase">Informasi Pengguna</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Nama</td>
                                    <td width="2%">:</td>
                                    <td>{{ $parkingEntry->user->name }}</td>
                                </tr>
                                <tr>
                                    <td>Username</td>
                                    <td>:</td>
                                    <td>{{ $parkingEntry->user->username }}</td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td>:</td>
                                    <td>{{ $parkingEntry->user->email ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Kode Parkir</td>
                                    <td>:</td>
                                    <td><span class="badge bg-primary">{{ $parkingEntry->kode_parkir }}</span></td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase">Informasi Kendaraan</h6>
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
                            </table>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase">Waktu Masuk</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Tanggal</td>
                                    <td width="2%">:</td>
                                    <td>{{ $parkingEntry->entry_time->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Waktu</td>
                                    <td>:</td>
                                    <td>{{ $parkingEntry->entry_time->format('H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td>Lokasi Masuk</td>
                                    <td>:</td>
                                    <td>{{ $parkingEntry->entry_location ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase">Waktu Keluar</h6>
                            @if($parkingEntry->parkingExit)
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Tanggal</td>
                                    <td width="2%">:</td>
                                    <td>{{ $parkingEntry->parkingExit->exit_time->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Waktu</td>
                                    <td>:</td>
                                    <td>{{ $parkingEntry->parkingExit->exit_time->format('H:i:s') }}</td>
                                </tr>
                                <tr>
                                    <td>Lokasi Keluar</td>
                                    <td>:</td>
                                    <td>{{ $parkingEntry->parkingExit->exit_location ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td>Biaya Parkir</td>
                                    <td>:</td>
                                    <td>Rp{{ number_format($parkingEntry->parkingExit->parking_fee, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                            @else
                            <p><span class="text-warning">Status: Belum keluar</span></p>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase">Informasi QR Code</h6>
                            @if($qrCode)
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">QR Code</td>
                                    <td width="2%">:</td>
                                    <td>{{ $qrCode->code }}</td>
                                </tr>
                                <tr>
                                    <td>Tanggal Berlaku</td>
                                    <td>:</td>
                                    <td>{{ $qrCode->date->format('d F Y') }}</td>
                                </tr>
                                <tr>
                                    <td>Status Digunakan</td>
                                    <td>:</td>
                                    <td>
                                        @if($qrCode->is_used)
                                            <span class="text-success">Sudah</span>
                                        @else
                                            <span class="text-danger">Belum</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            @else
                            <p class="text-muted">QR Code tidak ditemukan untuk pengguna ini</p>
                            @endif
                        </div>

                        @if($parkingEntry->parkingExit)
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted text-uppercase">Durasi Parkir</h6>
                            @php
                                $entryTime = $parkingEntry->entry_time;
                                $exitTime = $parkingEntry->parkingExit->exit_time;
                                $duration = $entryTime->diff($exitTime);
                            @endphp
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%">Durasi</td>
                                    <td width="2%">:</td>
                                    <td>{{ $duration->format('%d hari, %h jam, %i menit, %s detik') }}</td>
                                </tr>
                                <tr>
                                    <td>Total Biaya</td>
                                    <td>:</td>
                                    <td>Rp{{ number_format($parkingEntry->parkingExit->parking_fee, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-xl-5 col-lg-4">
            <!-- Card untuk QR Code Pengguna -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">QR Code Pengguna</h5>
                </div>
                <div class="card-body text-center">
                    @if($qrCode)
                    <div class="mb-3" style="display: flex; justify-content: center; align-items: center; min-height: 220px; background: #f8f9fa; border-radius: 10px; padding: 15px;">
                        @php
                            $qrCodeService = app(\App\Services\QRCodeService::class);
                            $qrCodeImage = $qrCodeService->generateQRCodeImage($qrCode->code, 200);
                        @endphp
                        @if(strpos($qrCodeImage, '<svg') !== false)
                            <div style="padding: 20px; background: white; border-radius: 10px; display: inline-block; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                {!! $qrCodeImage !!}
                            </div>
                        @else
                            <img src="data:image/png;base64,{{ base64_encode($qrCodeImage) }}"
                                 alt="QR Code Pengguna"
                                 class="img-fluid border border-1 rounded"
                                 style="padding: 20px; background: white; border-radius: 10px; max-width: 180px; height: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                        @endif
                    </div>
                    <p class="mb-1" style="font-size: 0.85rem;">
                        <strong>{{ $qrCode->code }}</strong>
                    </p>
                    <button type="button" class="btn btn-outline-primary mt-2" onclick="downloadQRCode()">
                        <i class="ti ti-download"></i> Download QR Code
                    </button>
                    <div class="mt-2">
                        <span class="text-muted" style="font-size: 0.8rem;">
                            <i class="ti ti-info-circle"></i> QR Code ini bisa digunakan untuk keluar dari area parkir
                        </span>
                    </div>
                    @else
                    <p class="text-muted">QR Code tidak tersedia untuk pengguna ini</p>
                    <button type="button" class="btn btn-outline-primary mt-2" disabled>
                        <i class="ti ti-download"></i> Download QR Code
                    </button>
                    @endif
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(!$parkingEntry->parkingExit)
                        <a href="{{ route('parking.transactions.payment.form', $parkingEntry->id) }}" class="btn btn-success">
                            <i class="ti ti-currency-riyal"></i> Proses Pembayaran
                        </a>
                        <form method="POST" action="{{ route('parking.management.addExit', $parkingEntry->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menambahkan catatan keluar untuk entri ini?')">
                            @csrf
                            <input type="hidden" name="exit_time" value="{{ now()->format('Y-m-d H:i:s') }}">
                            <!-- Biaya akan dihitung otomatis berdasarkan kebijakan 1x bayar per hari -->
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="ti ti-logout"></i> Tambah Keluar
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('parking.management.download-pdf', $parkingEntry->id) }}" class="btn btn-info">
                            <i class="ti ti-download"></i> Download PDF
                        </a>
                        <a href="{{ route('parking.management.edit', $parkingEntry->id) }}" class="btn btn-primary">
                            <i class="ti ti-pencil"></i> Edit Data
                        </a>
                        <form method="POST" action="{{ route('parking.management.destroy', $parkingEntry->id) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus entri ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="ti ti-trash"></i> Hapus Data
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            @if($parkingEntry->parkingTransaction)
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detail Transaksi</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <td>Kode Transaksi</td>
                            <td>:</td>
                            <td>{{ $parkingEntry->parkingTransaction->transaction_code }}</td>
                        </tr>
                        <tr>
                            <td>Jumlah</td>
                            <td>:</td>
                            <td>Rp{{ number_format($parkingEntry->parkingTransaction->amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>Metode</td>
                            <td>:</td>
                            <td>{{ ucfirst($parkingEntry->parkingTransaction->payment_method) }}</td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>:</td>
                            <td>
                                @if($parkingEntry->parkingTransaction->status === 'completed')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($parkingEntry->parkingTransaction->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Gagal</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Waktu</td>
                            <td>:</td>
                            <td>{{ $parkingEntry->parkingTransaction->paid_at ? $parkingEntry->parkingTransaction->paid_at->format('d/m/Y H:i:s') : $parkingEntry->parkingTransaction->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function downloadQRCode() {
    // Ambil container QR code
    const qrCodeContainer = document.querySelector('.card-body.text-center');
    const qrImg = qrCodeContainer.querySelector('img');
    const qrSvg = qrCodeContainer.querySelector('svg');

    if (qrSvg) {
        // Handle SVG download
        const svgData = new XMLSerializer().serializeToString(qrSvg);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Set canvas size with padding
        const svgSize = svgSvg.viewBox.baseVal || { width: 250, height: 250 };
        const padding = 25;
        canvas.width = svgSize.width + (padding * 2);
        canvas.height = svgSize.height + (padding * 2);

        // Fill with white background
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        const img = new Image();
        img.onload = function() {
            ctx.drawImage(img, padding, padding);

            const pngUrl = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.href = pngUrl;
            downloadLink.download = 'qr-code-{{ $qrCode->code ?? $parkingEntry->kode_parkir }}.png';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        };

        // Create a blob URL for the SVG with padding
        const blob = new Blob([svgData], { type: 'image/svg+xml' });
        const url = URL.createObjectURL(blob);
        img.src = url;
    } else if (qrImg) {
        // Handle image download
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Add padding and white background
        const originalWidth = qrImg.naturalWidth || qrImg.width;
        const originalHeight = qrImg.naturalHeight || qrImg.height;
        const padding = 25;
        canvas.width = originalWidth + (padding * 2);
        canvas.height = originalHeight + (padding * 2);

        // Fill with white background
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        ctx.drawImage(qrImg, padding, padding);

        const pngUrl = canvas.toDataURL('image/png');
        const downloadLink = document.createElement('a');
        downloadLink.href = pngUrl;
        downloadLink.download = 'qr-code-{{ $qrCode->code ?? $parkingEntry->kode_parkir }}.png';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    } else {
        console.error('QR Code element not found');
        alert('QR Code tidak ditemukan untuk didownload');
    }
}
</script>
@endsection