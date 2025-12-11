@extends('layouts.app')

@section('title', 'Scan Barcode Admin/Petugas - Sistem Parkir')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Parkir /</span> Scan Barcode (Admin/Petugas)
    </h4>

    <!-- Card untuk scan barcode pengguna -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Scan Barcode Pengguna</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="ti ti-info-circle"></i>
                        <strong>Petunjuk:</strong> Scan barcode milik pengguna untuk mencatat keluar masuk parkir. Fitur ini khusus untuk admin dan petugas.
                    </div>

                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="text-center mb-4">
                                <div id="cameraContainer">
                                    <video id="preview" width="100%" height="300" style="border: 1px solid #ccc; max-width: 400px;" autoplay playsinline></video>
                                </div>

                                <div id="cameraStatus" class="mt-2">
                                    <p class="text-muted">
                                        <i class="ti ti-camera"></i> Arahkan kamera ke barcode pengguna...
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="scanStatus" class="mt-3"></div>

                    <!-- Hidden elements to store URLs -->
                    <div id="routeUrls"
                         data-scan-barcode-url="{{ route('admin.scan.barcode') }}"
                         data-scan-exit-url="{{ route('parking.scan.exit') }}"
                         data-csrf-token="{{ csrf_token() }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card untuk input manual (jika kamera tidak tersedia) -->
    <div class="row mb-4">
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Input Manual</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="manualCode" class="form-label">Masukkan Kode atau Barcode</label>
                        <input type="text" class="form-control" id="manualCode" placeholder="Masukkan kode parkir atau barcode">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tipe Proses</label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="processType" id="processTypeEntry" value="entry" checked>
                                <label class="form-check-label" for="processTypeEntry">
                                    Masuk
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="processType" id="processTypeExit" value="exit">
                                <label class="form-check-label" for="processTypeExit">
                                    Keluar
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-primary w-100" onclick="processManualCode()">
                        <i class="ti ti-scan me-1"></i> Proses Kode
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Card untuk status scan terakhir -->
    <div class="row mb-4" id="lastScanCard" style="display: none;">
        <div class="col-lg-6 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Hasil Scan Terakhir</h5>
                </div>
                <div class="card-body">
                    <div id="lastScanContent">
                        <!-- Content akan diisi secara dinamis -->
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

            // Fungsi untuk menampilkan hasil scan terakhir
            function showLastScanResult(data) {
                const lastScanCard = document.getElementById('lastScanCard');
                const lastScanContent = document.getElementById('lastScanContent');

                if (!lastScanContent || !lastScanCard) return;

                let htmlContent = '';
                if (data.success) {
                    htmlContent = `
                        <div class="alert alert-success">
                            <i class="ti ti-check-circle"></i>
                            <strong>Proses Berhasil!</strong><br>
                            <strong>Nama:</strong> ${data.user_name || 'N/A'}<br>
                            <strong>Kode Parkir:</strong> ${data.kode_parkir || data.exit?.parking_entry?.kode_parkir || 'N/A'}<br>
                            <strong>Waktu:</strong> ${new Date().toLocaleString('id-ID')}<br>
                            <strong>Status:</strong> ${data.message || 'Berhasil'}
                        </div>
                    `;
                } else {
                    htmlContent = `
                        <div class="alert alert-danger">
                            <i class="ti ti-alert-circle"></i>
                            <strong>Proses Gagal!</strong><br>
                            <strong>Error:</strong> ${data.message || 'Terjadi kesalahan'}
                        </div>
                    `;
                }

                lastScanContent.innerHTML = htmlContent;
                lastScanCard.style.display = 'block';
            }

            // Event listener ketika QR Code berhasil dipindai
            scanner.addListener('scan', function (content) {
                if (isProcessing) return; // Mencegah pemrosesan ganda
                if (content === lastScanned) return; // Cegah scan berulang dalam waktu singkat

                isProcessing = true;
                lastScanned = content;
                showLoading(); // Tampilkan loading

                console.log("QR Code Ditemukan:", content);

                // Coba tentukan jenis barcode: jika mengandung simbol yang mirip kode parkir, gunakan PUT endpoint
                // Kode parkir biasanya dalam format: id-plat-tanggal (contoh: 2-N_1234_AB-111225)
                // QR code masuk biasanya dalam format: id-NO_PLATE-tanggal (contoh: 2-NO_PLATE-111225)

                const isLikelyParkirCode = content.includes('-') && content.split('-').length >= 3;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                let endpoint, method;
                if (isLikelyParkirCode) {
                    // Jika kode terlihat seperti kode parkir, gunakan PUT untuk update exit
                    endpoint = '{{ route("admin.update.exit") }}'; // PUT route for update exit
                    method = 'PUT';
                } else {
                    // Jika bukan seperti kode parkir, gunakan POST untuk scan umum
                    endpoint = '{{ route("admin.scan.barcode") }}'; // POST route for general scan
                    method = 'POST';
                }

                // Kirim ke endpoint yang sesuai
                fetch(endpoint, {
                    method: method,
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
                        showLastScanResult(data);
                    } else {
                        showScanStatus(data.message, 'error');
                        showLastScanResult(data);
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
                        status.innerHTML = '<p class="text-success"><i class="ti ti-check-circle"></i> Kamera aktif. Arahkan ke barcode pengguna...</p>';
                    }
                } else {
                    showScanStatus('Kamera tidak ditemukan. Silakan periksa perangkat Anda.', 'error');
                }
            }).catch(function (e) {
                console.error("Kesalahan saat mengakses kamera:", e);
                showScanStatus('Gagal mengakses kamera. Pastikan browser memiliki izin.', 'error');
            });
        });

        // Fungsi untuk proses input manual
        function processManualCode() {
            const manualCode = document.getElementById('manualCode').value;
            const processType = document.querySelector('input[name="processType"]:checked').value;

            if (!manualCode) {
                showScanStatus('Masukkan kode terlebih dahulu!', 'error');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            // Tentukan endpoint berdasarkan tipe proses
            let endpoint, method;
            if (processType === 'exit') {
                // Untuk keluar, gunakan PUT endpoint
                endpoint = '{{ route("admin.update.exit") }}'; // PUT route for update exit
                method = 'PUT';
            } else {
                // Untuk masuk, gunakan POST endpoint
                endpoint = '{{ route("admin.scan.barcode") }}'; // POST route for general scan
                method = 'POST';
            }

            let requestData = {
                qr_code: manualCode
            };

            fetch(endpoint, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showScanStatus(data.message, 'success');
                    showLastScanResult(data);
                } else {
                    showScanStatus(data.message, 'error');
                    showLastScanResult(data);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showScanStatus('Terjadi kesalahan saat menghubungi server', 'error');
            });
        }

        // Fungsi tambahan untuk menampilkan status scan (digunakan juga di showScanStatus)
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
    </script>
@endpush