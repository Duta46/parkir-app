@extends('layouts.app')

@section('title', 'Scan Barcode Admin/Petugas - Sistem Parkir')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw/light">Parkir /</span> Scan Barcode (Admin/Petugas)
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
                        <i class="fa-solid fa-info-circle"></i>
                        <strong>Petunjuk:</strong> 
                        <div class="mt-1">
                            <ul class="mb-0">
                                <li>Scan barcode admin/petugas untuk <strong>masuk</strong> ke area parkir</li>
                                <li>Untuk <strong>keluar</strong> dari area parkir, silakan gunakan halaman exit di dashboard admin</li>
                            </ul>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <div class="text-center mb-4">
                                <div id="qr-reader" style="width: 100%; max-width: 400px; margin: 0 auto; border: 1px solid #ccc; background-color: #000;"></div>
                                <div id="qr-reader-buttons" class="mt-2">
                                    <button id="startButton" class="btn btn-success">Mulai Scan</button>
                                    <button id="stopButton" class="btn btn-warning" style="display: none;">Stop Scan</button>
                                </div>
                            </div>

                            <div id="cameraStatus" class="mt-2 text-center">
                                <p class="text-muted">
                                    <i class="fa-solid fa-camera"></i> Kamera belum diaktifkan. Klik "Mulai Scan" untuk memulai...
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <button id="uploadImageBtn" class="btn btn-outline-primary">
                            Atau Upload Gambar QR Code
                        </button>
                    </div>

                    <!-- Upload zone -->
                    <div id="uploadZone" class="mt-3 p-3 border rounded" style="display: none;">
                        <div class="mb-3">
                            <label for="qrImageUpload" class="form-label">Pilih Gambar QR Code:</label>
                            <input type="file" class="form-control" id="qrImageUpload" accept="image/*">
                        </div>
                        <div id="uploadedImagePreview" class="text-center mb-3"></div>
                        <button id="processImageBtn" class="btn btn-primary" disabled>Proses Gambar</button>
                    </div>

                    <div id="scanStatus" class="mt-3"></div>

                    <!-- Hidden elements to store URLs -->
                    <div id="routeUrls"
                         data-process-user-qrcode-exit-url="{{ route('parking.process.user.qrcode.exit') }}"
                         data-admin-scan-barcode-url="{{ route('admin.scan.barcode') }}"
                         data-csrf-token="{{ csrf_token() }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card untuk tampilan barcode pengguna (akan muncul setelah scan admin untuk masuk) -->
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
                        <i class="fa-solid fa-check-circle"></i>
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
    <!-- Load Html5Qrcode Library -->
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let html5QrCode;
            let lastScanned = ""; // Menyimpan QR Code terakhir agar tidak diproses dua kali
            let isProcessing = false; // Flag untuk mencegah pemrosesan ganda
            let isScanning = false; // Flag untuk status scanning

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


            // Fungsi untuk membuat permintaan AJAX
            function makeAjaxRequest(url, data) {
                const urls = document.getElementById('routeUrls');
                const token = urls ? urls.getAttribute('data-csrf-token') : document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(data)
                });
            }

            // Fungsi untuk memproses hasil scan
            function processScanResult(data, source = 'camera') {
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
                    if(status && source === 'camera') {
                        status.innerHTML = '<p class="text-success"><i class="fa-solid fa-check-circle"></i> Berhasil scan! Kamera siap untuk scan berikutnya.</p>';
                    } else if (status && source === 'upload') {
                        status.innerHTML = '<p class="text-success"><i class="fa-solid fa-check-circle"></i> Berhasil scan dari gambar! Kamera siap untuk scan berikutnya.</p>';
                    }
                } else {
                    showScanStatus(data.message, 'error');
                }
            }

            // Fungsi untuk menangani kode QR yang sudah terbaca
            function handleScannedCode(content, source = 'camera') {
                if (source === 'upload') {
                    isProcessingImage = true;
                    showLoading();
                } else {
                    isProcessing = true;
                    showLoading();
                }
                
                // Menampilkan kode yang dipindai untuk debugging
                console.log("QR Code Ditemukan:", content);
                console.log('Kode QR yang diproses:', content, {
                    tanggal: new Date().toLocaleDateString(),
                    sumber: source === 'upload' ? 'Upload Gambar' : 'Kamera'
                });

                // Kirim ke endpoint khusus untuk memproses QR code pengguna sebagai exit
                const urls = document.getElementById('routeUrls');
                const processExitUrl = urls ? urls.getAttribute('data-process-user-qrcode-exit-url') : '{{ route("parking.process.user.qrcode.exit") }}';
                
                makeAjaxRequest(processExitUrl, { qr_code: content })
                    .then(response => {
                        if (!response.ok) {
                            // Jika response tidak OK, baca teks untuk debugging
                            return response.text().then(text => {
                                console.error('Server error response:', text);
                                throw new Error('HTTP error! status: ' + response.status);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (source === 'upload') {
                            hideLoading();
                            isProcessingImage = false;
                        } else {
                            hideLoading();
                            isProcessing = false;
                        }
                        
                        processScanResult(data, source);
                        
                        if (source !== 'upload') {
                            // Reload halaman setelah 2 detik untuk memperbarui riwayat
                            setTimeout(() => {
                                location.reload();
                            }, 2000);
                        }
                    })
                    .catch(error => {
                        if (source === 'upload') {
                            hideLoading();
                            isProcessingImage = false;
                        } else {
                            hideLoading();
                            isProcessing = false;
                        }
                        
                        console.error('Error:', error);
                        showScanStatus('Terjadi kesalahan saat menghubungi server: ' + error.message, 'error');
                    });
            }


            // Event listener untuk tombol upload gambar
            const uploadBtn = document.getElementById('uploadImageBtn');
            const uploadZone = document.getElementById('uploadZone');
            if (uploadBtn && uploadZone) {
                uploadBtn.addEventListener('click', function() {
                    uploadZone.style.display = 'block';
                });
            }

            // Event listener untuk file input
            const qrImageUpload = document.getElementById('qrImageUpload');
            const uploadedImagePreview = document.getElementById('uploadedImagePreview');
            const processImageBtn = document.getElementById('processImageBtn');
            
            if (qrImageUpload && uploadedImagePreview && processImageBtn) {
                qrImageUpload.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            uploadedImagePreview.innerHTML = '<img src="' + event.target.result + '" style="max-width: 200px; max-height: 200px; border: 1px solid #ccc;" alt="Preview QR">';
                            processImageBtn.disabled = false;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Event listener untuk tombol proses gambar
            if (processImageBtn) {
                processImageBtn.addEventListener('click', function() {
                    const fileInput = document.getElementById('qrImageUpload');
                    if (fileInput.files.length > 0) {
                        const file = fileInput.files[0];
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            // Kita perlu mengimplementasi OCR atau library untuk membaca QR code dari gambar
                            // Untuk sekarang, kita hanya menampilkan error bahwa fitur ini perlu pengembangan lebih lanjut
                            console.log('Fitur membaca QR code dari gambar memerlukan pengembangan tambahan untuk mengimplementasi OCR atau library pembaca gambar QR.');
                        }
                        reader.readAsDataURL(file);
                    } else {
                        showScanStatus('Silakan pilih file gambar terlebih dahulu.', 'error');
                    }
                });
            }

            // Fungsi untuk memulai scanning
            function startScanning() {
                if (isScanning) return;
                
                isScanning = true;
                
                const startButton = document.getElementById('startButton');
                const stopButton = document.getElementById('stopButton');
                const cameraStatus = document.getElementById('cameraStatus');
                
                if (startButton) startButton.style.display = 'none';
                if (stopButton) stopButton.style.display = 'inline-block';
                if (cameraStatus) cameraStatus.innerHTML = '<p class="text-info"><i class="fa-solid fa-loader"></i> Menginisialisasi kamera...</p>';

                // Inisialisasi Html5Qrcode
                html5QrCode = new Html5Qrcode("qr-reader");

                // Sukses callback - dipanggil ketika QR code terbaca
                const onSuccess = function(decodedText, decodedResult) {
                    if (isProcessing) return; // Mencegah pemrosesan ganda
                    if (decodedText === lastScanned) return; // Cegah scan berulang dalam waktu singkat

                    isProcessing = true;
                    lastScanned = decodedText;
                    
                    // Menampilkan kode yang dipindai untuk debugging
                    console.log("QR Code Ditemukan:", decodedText);

                    // Proses kode QR
                    handleScannedCode(decodedText, 'camera');
                    
                    // Reset isProcessing setelah delay untuk mencegah pemrosesan ganda
                    setTimeout(() => {
                        isProcessing = false;
                    }, 2000); // Delay 2 detik sebelum bisa scan lagi
                };

                // Gagal callback - jika terjadi error
                const onFailure = function(error) {
                    // Biarkan kosong untuk mencegah spam di console
                };

                // Mendapatkan kamera dan memulai scanning
                Html5Qrcode.getCameras().then(cameras => {
                    if (cameras && cameras.length) {
                        // Gunakan kamera belakang jika tersedia
                        const cameraId = cameras[0].id;
                        
                        html5QrCode.start(
                            cameraId,
                            {
                                fps: 10,
                                qrbox: { width: 250, height: 250 }
                            },
                            onSuccess,
                            onFailure
                        ).then(() => {
                            if (cameraStatus) cameraStatus.innerHTML = '<p class="text-success"><i class="fa-solid fa-check-circle"></i> Kamera aktif. Arahkan ke barcode pengguna...</p>';
                        }).catch(err => {
                            console.error("Gagal memulai kamera: ", err);
                            if (cameraStatus) cameraStatus.innerHTML = '<p class="text-danger"><i class="fa-solid fa-x"></i> Gagal memulai kamera: ' + err + '</p>';
                            isScanning = false;
                            if (startButton) startButton.style.display = 'inline-block';
                            if (stopButton) stopButton.style.display = 'none';
                        });
                    } else {
                        console.error("Kamera tidak ditemukan");
                        if (cameraStatus) cameraStatus.innerHTML = '<p class="text-danger"><i class="fa-solid fa-x"></i> Kamera tidak ditemukan</p>';
                        isScanning = false;
                        if (startButton) startButton.style.display = 'inline-block';
                        if (stopButton) stopButton.style.display = 'none';
                    }
                }).catch(err => {
                    console.error("Gagal mengakses kamera: ", err);
                    if (cameraStatus) cameraStatus.innerHTML = '<p class="text-danger"><i class="fa-solid fa-x"></i> Gagal mengakses kamera: ' + err + '</p>';
                    isScanning = false;
                    if (startButton) startButton.style.display = 'inline-block';
                    if (stopButton) stopButton.style.display = 'none';
                });
            }

            // Fungsi untuk menghentikan scanning
            function stopScanning() {
                if (!isScanning || !html5QrCode) return;
                
                html5QrCode.stop().then(() => {
                    isScanning = false;
                    const startButton = document.getElementById('startButton');
                    const stopButton = document.getElementById('stopButton');
                    const cameraStatus = document.getElementById('cameraStatus');
                    
                    if (startButton) startButton.style.display = 'inline-block';
                    if (stopButton) stopButton.style.display = 'none';
                    if (cameraStatus) cameraStatus.innerHTML = '<p class="text-muted"><i class="fa-solid fa-camera"></i> Kamera dihentikan. Klik "Mulai Scan" untuk memulai kembali...</p>';
                }).catch(err => {
                    console.error('Gagal menghentikan kamera: ', err);
                    isScanning = false;
                });
            }

            // Event listener untuk tombol mulai scan
            const startBtn = document.getElementById('startButton');
            if (startBtn) {
                startBtn.addEventListener('click', startScanning);
            }

            // Event listener untuk tombol stop scan
            const stopBtn = document.getElementById('stopButton');
            if (stopBtn) {
                stopBtn.addEventListener('click', stopScanning);
            }
        });
    </script>
@endpush