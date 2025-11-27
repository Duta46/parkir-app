@extends('layouts.app')

@section('title', 'QR Code Saya')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">QR Code /</span> Saya
    </h4>

    <div class="row">
        <div class="col-xxl-6 col-xl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">QR Code Hari Ini</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        @if($qrCodeModel)
                            <img src="data:image/png;base64,{{ base64_encode($qrCodeImage) }}"
                                 alt="QR Code Anda"
                                 class="img-fluid border border-1 rounded"
                                 style="max-width: 250px; height: 250px;">
                        @else
                            <p class="text-danger">Gagal membuat QR code. Silakan coba lagi.</p>
                        @endif
                    </div>

                    @if($qrCodeModel)
                        <div class="mb-4">
                            <p class="mb-1">
                                <small class="text-muted">QR Code berlaku sampai: {{ $qrCodeModel->expires_at->format('H:i:s') }}</small>
                            </p>
                            <p class="mb-1">
                                <small class="text-muted">Dibuat pada: {{ $qrCodeModel->date->format('Y-m-d') }}</small>
                            </p>
                            @if($qrCodeModel->is_used)
                                <p class="text-danger fw-bold">QR code ini telah digunakan</p>
                            @endif
                        </div>

                        <button id="refreshQrBtn"
                                class="btn btn-primary mb-3">
                            <i class="ti ti-refresh"></i> Perbarui QR Code
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('refreshQrBtn').addEventListener('click', function() {
        this.disabled = true;
        this.innerHTML = '<i class="ti ti-loader ti-spin ti-xs"></i> Menghasilkan...';

        fetch('{{ route("qr-code.generate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload halaman untuk menampilkan QR code baru
                location.reload();
            } else {
                alert('Gagal membuat QR code: ' + (data.message || 'Kesalahan tidak diketahui'));
                this.disabled = false;
                this.innerHTML = '<i class="ti ti-refresh"></i> Perbarui QR Code';
            }
        })
        .catch(error => {
            console.error('Kesalahan:', error);
            alert('Terjadi kesalahan saat membuat QR code');
            this.disabled = false;
            this.innerHTML = '<i class="ti ti-refresh"></i> Perbarui QR Code';
        });
    });
</script>
@endsection