@extends('layouts.app')

@section('title', 'Dashboard - Sistem Parkir')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">Sistem Parkir</h4>

    <!-- Informasi Pengguna -->
    <div class="row mb-4">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Profil Saya</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded-circle bg-label-primary">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="d-flex flex-column">
                            <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                            <small>{{ Auth::user()->getIdentifierLabelAttribute() }}: {{ Auth::user()->getIdentifierAttribute() }}</small>
                            <small class="text-muted">Tipe: {{ ucfirst(Auth::user()->user_type) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik untuk admin -->
        @if(Auth::user()->hasRole('Admin'))
        <div class="col-md-8">
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
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

                <div class="col-lg-3 col-md-6 mb-4">
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

                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="avatar">
                                        <span class="avatar-initial rounded bg-label-warning">
                                            <i class="ti ti-exit ti-lg"></i>
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

                <div class="col-lg-3 col-md-6 mb-4">
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
        </div>
        @endif
    </div>

    @if(Auth::user()->hasRole(['Admin', 'Petugas']))
    <div class="row">
        <!-- QR Code Display - only for admin/petugas -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">QR Code Saya</h5>
                    <button id="refreshQrBtn" class="btn btn-sm btn-primary">
                        <i class="ti ti-refresh ti-xs"></i> Perbarui
                    </button>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        @if($qrCodeModel ?? null)
                            @if(strpos($qrCodeImage, '<svg') !== false)
                                <div class="text-center">
                                    {!! $qrCodeImage !!} <!-- Render SVG directly -->
                                </div>
                            @else
                                <img src="data:image/png;base64,{{ base64_encode($qrCodeImage) }}"
                                     alt="QR Code Anda"
                                     class="img-fluid border border-1 rounded"
                                     style="max-width: 250px; height: auto;">
                            @endif
                        @else
                            <p class="text-danger">Tidak ada QR code tersedia</p>
                        @endif
                    </div>

                    @if($qrCodeModel ?? null)
                        <p class="mb-2">
                            <small class="text-muted">Berlaku sampai: {{ $qrCodeModel->expires_at->format('H:i:s') }}</small>
                        </p>
                        <p>
                            <small class="text-muted">Dibuat pada: {{ $qrCodeModel->date->format('Y-m-d') }}</small>
                        </p>
                        @if($qrCodeModel->is_used)
                            <p class="text-danger fw-bold">QR Code ini telah digunakan</p>
                        @else
                            <p class="text-success fw-bold">QR Code aktif</p>
                        @endif
                    @endif

                    <a href="{{ route('qr-code.show') }}" class="btn btn-outline-primary">
                        Lihat QR Code Saya
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
        

    <!-- Entry/Exit History -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Riwayat Parkir Terbaru</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Peristiwa</th>
                            <th>Waktu</th>
                            <th>Lokasi</th>
                            <th>Biaya</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $entries = \App\Models\ParkingEntry::where('user_id', auth()->id())
                                ->with(['parkingExit', 'qrCode'])
                                ->orderBy('entry_time', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @forelse($entries as $entry)
                        <tr>
                            <td>
                                <span class="badge bg-success">Masuk</span>
                            </td>
                            <td>{{ $entry->entry_time->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $entry->kode_parkir }}<br><small class="text-muted">Kode untuk keluar</small></td>
                            <td>N/A</td>
                            <td>
                                @if($entry->parkingExit)
                                    <span class="badge bg-primary">Sudah Keluar</span>
                                @else
                                    <span class="badge bg-warning">Aktif</span>
                                @endif
                            </td>
                        </tr>
                        @if($entry->parkingExit)
                        <tr>
                            <td>
                                <span class="badge bg-danger">Keluar</span>
                            </td>
                            <td>{{ $entry->parkingExit->exit_time->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $entry->parkingExit->exit_location ?? 'N/A' }}</td>
                            <td>Rp{{ number_format($entry->parkingExit->parking_fee, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge bg-secondary">Selesai</span>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Tidak ditemukan riwayat parkir</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // QR Code refresh functionality
    const refreshQrBtn = document.getElementById('refreshQrBtn');
    if (refreshQrBtn) {
        refreshQrBtn.addEventListener('click', function() {
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
                    // Reload the page to show the new QR code
                    location.reload();
                } else {
                    alert('Failed to generate QR code: ' + (data.message || 'Unknown error'));
                    this.disabled = false;
                    this.innerHTML = '<i class="ti ti-refresh ti-xs"></i> Perbarui';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while generating QR code');
                this.disabled = false;
                this.innerHTML = '<i class="ti ti-refresh ti-xs"></i> Perbarui';
            });
        });
    }

    // Scan functionality
    document.getElementById('scanEntryBtn').addEventListener('click', function() {
        const qrCode = document.getElementById('qrCodeInput').value.trim();
        
        if (!qrCode) {
            showScanStatus('Please enter a QR code or scan using the camera', 'error');
            return;
        }
        
        scanQRCode(qrCode, 'entry');
    });

    document.getElementById('scanExitBtn').addEventListener('click', function() {
        const qrCode = document.getElementById('qrCodeInput').value.trim();
        
        if (!qrCode) {
            showScanStatus('Please enter a QR code or scan using the camera', 'error');
            return;
        }
        
        scanQRCode(qrCode, 'exit');
    });

    function scanQRCode(qrCode, type) {
        const scanBtn = type === 'entry' ?
            document.getElementById('scanEntryBtn') :
            document.getElementById('scanExitBtn');

        scanBtn.disabled = true;
        scanBtn.innerHTML = '<i class="ti ti-loader ti-spin ti-xs"></i> Scanning...';
        scanBtn.classList.add('disabled');

        let requestData = {};
        if (type === 'entry') {
            requestData = { qr_code: qrCode };
        } else {
            // Untuk exit, kita kirimkan kode parkir
            requestData = { kode_parkir: qrCode };
        }

        fetch(type === 'entry' ? '{{ route("parking.scan.entry") }}' : '{{ route("parking.scan.exit") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(requestData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showScanStatus(data.message || `Successfully recorded ${type}`, 'success');
                // Clear the input
                document.getElementById('qrCodeInput').value = '';
                // Reload the page to update history
                setTimeout(() => location.reload(), 2000);
            } else {
                showScanStatus(data.message || `Failed to process ${type}: Unknown error`, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showScanStatus('An error occurred while processing the QR code', 'error');
        })
        .finally(() => {
            scanBtn.disabled = false;
            scanBtn.classList.remove('disabled');
            scanBtn.innerHTML = type === 'entry' ?
                '<i class="ti ti-login"></i> Scan untuk Masuk' :
                '<i class="ti ti-logout"></i> Scan untuk Keluar';
        });
    }

    function showScanStatus(message, type) {
        const statusDiv = document.getElementById('scanStatus');
        statusDiv.innerHTML = `
            <div class="alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (statusDiv.querySelector('.alert')) {
                statusDiv.innerHTML = '';
            }
        }, 5000);
    }
</script>
@endsection