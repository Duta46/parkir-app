@extends('layouts.app')

@section('title', 'Scan Barcode - Sistem Parkir')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Parkir /</span> Scan Barcode
    </h4>

    <!-- Card untuk scan barcode admin -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Scan Barcode Admin</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle"></i>
                        <strong>Petunjuk:</strong> Scan barcode milik admin untuk masuk ke area parkir. Setelah scan, Anda akan mendapatkan barcode unik yang bisa digunakan untuk keluar parkir.
                    </div>

                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="text-center mb-4">
                                <div id="cameraContainer">
                                    <video id="preview" width="100%" height="300" style="border: 1px solid #ccc; max-width: 400px;" autoplay playsinline></video>
                                </div>

                                <div id="cameraStatus" class="mt-2">
                                    <p class="text-muted">
                                        <i class="ti ti-camera"></i> Arahkan kamera ke barcode admin...
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="scanStatus" class="mt-3"></div>

                    <!-- Hidden elements to store URLs -->
                    <div id="routeUrls"
                         data-scan-barcode-url="{{ route('scan.barcode') }}"
                         data-csrf-token="{{ csrf_token() }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card untuk tampilan barcode pengguna (akan muncul setelah scan admin) -->
    <div class="row mb-4" id="userBarcodeCard" style="display: none;">
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Barcode Keluar Anda</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div id="userBarcodeImage"></div>
                    </div>

                    <p class="mb-2">
                        <small class="text-muted">Barcode ini berlaku untuk satu kali penggunaan</small>
                    </p>
                    <p id="userBarcodeText" class="fw-bold"></p>

                    <div class="alert alert-success">
                        <i class="ti ti-check-circle"></i>
                        <strong>Status:</strong> Anda telah berhasil masuk ke area parkir. Gunakan barcode di atas saat ingin keluar.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card untuk riwayat parkir -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Riwayat Parkir Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Kode Parkir</th>
                                    <th>Waktu Masuk</th>
                                    <th>Waktu Keluar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $entries = \App\Models\ParkingEntry::where('user_id', auth()->id())
                                        ->with('parkingExit')
                                        ->orderBy('entry_time', 'desc')
                                        ->limit(5)
                                        ->get();
                                @endphp

                                @forelse($entries as $entry)
                                <tr>
                                    <td>{{ $entry->kode_parkir }}</td>
                                    <td>{{ $entry->entry_time->format('d/m/Y H:i:s') }}</td>
                                    <td>
                                        @if($entry->parkingExit)
                                            {{ $entry->parkingExit->exit_time->format('d/m/Y H:i:s') }}
                                        @else
                                            <span class="text-warning">Belum keluar</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($entry->parkingExit)
                                            <span class="badge bg-success">Selesai</span>
                                        @else
                                            <span class="badge bg-warning">Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada riwayat parkir</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
            let lastScanned = ""; // Menyimpan QR Code terakhir agar tidak diproses dua kali
            let isProcessing = false; // Flag untuk mencegah pemrosesan ganda

            // Menampilkan loading saat scan QR Code
            function showLoading() {
                let loadingDiv = document.createElement("div");
                loadingDiv.id = "loading-message";
                loadingDiv.style.position = "fixed";
                loadingDiv.style.top = "50%";
                loadingDiv.style.left = "50%";
                loadingDiv.style.transform = "translate(-50%, -50%)";
                loadingDiv.style.backgroundColor = "rgba(0, 0, 0, 0.7)";
                loadingDiv.style.color = "white";
                loadingDiv.style.padding = "15px";
                loadingDiv.style.borderRadius = "10px";
                loadingDiv.style.zIndex = "10000";
                loadingDiv.style.fontFamily = "Arial, sans-serif";
                loadingDiv.innerText = "Memproses QR Code...";
                document.body.appendChild(loadingDiv);
            }

            // Menghapus loading setelah scan selesai
            function hideLoading() {
                let loadingDiv = document.getElementById("loading-message");
                if (loadingDiv) {
                    loadingDiv.remove();
                }
            }

            // Fungsi untuk menampilkan status scan
            function showScanStatus(message, type) {
                const statusDiv = document.getElementById('scanStatus');
                if (!statusDiv) return;

                var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                var htmlContent = '<div class="' + alertClass + ' alert alert-dismissible fade show" role="alert">' +
                    message +
                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><\/button>' +
                    '<\/div>';
                statusDiv.innerHTML = htmlContent;

                // Auto-dismiss after 5 seconds
                setTimeout(function() {
                    if (statusDiv.querySelector('.alert')) {
                        statusDiv.innerHTML = '';
                    }
                }, 5000);
            }

            // Event listener ketika QR Code berhasil dipindai
            scanner.addListener('scan', function (content) {
                if (isProcessing) return; // Mencegah pemrosesan ganda
                if (content === lastScanned) return; // Cegah scan berulang dalam waktu singkat

                isProcessing = true;
                lastScanned = content;
                showLoading(); // Tampilkan loading

                console.log("QR Code Ditemukan:", content);

                // Kirim ke server untuk memproses scan
                const urls = document.getElementById('routeUrls');
                const scanUrl = urls ? urls.getAttribute('data-scan-barcode-url') : '{{ route("scan.barcode") }}';
                const csrfToken = urls ? urls.getAttribute('data-csrf-token') : document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                fetch(scanUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        qr_code: content
                    })
                })
                .then(response => response.json())
                .then(data => {
                    hideLoading(); // Sembunyikan loading
                    if (data.success) {
                        showScanStatus(data.message, 'success');

                        // Tampilkan barcode pengguna
                        const userBarcodeCard = document.getElementById('userBarcodeCard');
                        const userBarcodeImage = document.getElementById('userBarcodeImage');
                        const userBarcodeText = document.getElementById('userBarcodeText');

                        if(data.user_qr_code_image) {
                            // Tampilkan SVG QR code langsung (jika format SVG)
                            if(data.user_qr_code_image.includes('<svg')) {
                                userBarcodeImage.innerHTML = data.user_qr_code_image;
                            } else {
                                // Jika format base64 PNG
                                userBarcodeImage.innerHTML = '<img src="data:image/png;base64,' + data.user_qr_code_image + '" alt="Barcode Anda" class="img-fluid border border-1 rounded" style="max-width: 200px; height: auto;">';
                            }
                        }

                        if (userBarcodeText) {
                            userBarcodeText.innerHTML = '<span class="badge bg-primary">' + data.user_qr_code + '</span>';
                        }

                        if (userBarcodeCard) {
                            userBarcodeCard.style.display = 'block';
                        }

                        // Update status camera
                        const status = document.getElementById('cameraStatus');
                        if(status) {
                            status.innerHTML = '<p class="text-success"><i class="ti ti-check-circle"></i> Berhasil scan! Kamera siap untuk scan berikutnya.</p>';
                        }

                        // Reset processing flag setelah delay
                        setTimeout(() => {
                            isProcessing = false;
                        }, 2000);

                        // Reload halaman setelah 2 detik untuk memperbarui riwayat
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showScanStatus(data.message, 'error');
                        isProcessing = false; // Reset flag jika error
                    }
                })
                .catch(error => {
                    hideLoading(); // Sembunyikan loading
                    console.error('Error:', error);
                    showScanStatus('Terjadi kesalahan saat menghubungi server', 'error');
                    isProcessing = false; // Reset flag jika error
                });
            });

            // Memulai kamera dan memilih kamera belakang jika ada
            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    let backCamera = cameras.find(camera => camera.name.toLowerCase().includes('back'));
                    scanner.start(backCamera || cameras[0]); // Pilih kamera belakang jika tersedia

                    // Update status camera
                    const status = document.getElementById('cameraStatus');
                    if(status) {
                        status.innerHTML = '<p class="text-success"><i class="ti ti-check-circle"></i> Kamera aktif. Arahkan ke barcode...</p>';
                    }
                } else {
                    showScanStatus('Kamera tidak ditemukan. Silakan periksa perangkat Anda.', 'error');
                }
            }).catch(function (e) {
                console.error("Kesalahan saat mengakses kamera:", e);
                showScanStatus('Gagal mengakses kamera. Pastikan browser memiliki izin.', 'error');
            });
        });
    </script>
@endpush

