@extends('layouts.app')

@section('title', 'Detail Parkir - ' . $parkingEntry->kode_parkir)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw/light">Riwayat Parkir /</span> Detail
    </h4>

    <div class="row">
        <!-- Informasi Parkir -->
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Informasi Parkir</h5>
                    <span class="badge bg-label-{{ $parkingEntry->parkingExit ? 'success' : 'warning' }}">
                        {{ $parkingEntry->parkingExit ? 'Selesai' : 'Aktif' }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Kode Parkir:</strong></td>
                                <td>{{ $parkingEntry->kode_parkir }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tanggal:</strong></td>
                                <td>{{ $parkingEntry->entry_time->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Masuk:</strong></td>
                                <td>{{ $parkingEntry->entry_time->format('d/m/Y H:i:s') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Keluar:</strong></td>
                                <td>
                                    @if($parkingEntry->parkingExit)
                                        {{ $parkingEntry->parkingExit->exit_time->format('d/m/Y H:i:s') }}
                                    @else
                                        <span class="text-warning">Belum keluar</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Kendaraan:</strong></td>
                                <td>{{ $parkingEntry->vehicle_type ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Plat Nomor:</strong></td>
                                <td>{{ $parkingEntry->vehicle_plate_number ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Biaya:</strong></td>
                                <td>
                                    @if($parkingEntry->parkingExit)
                                        Rp{{ number_format($parkingEntry->parkingExit->parking_fee, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Lokasi Masuk:</strong></td>
                                <td>{{ $parkingEntry->entry_location ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Lokasi Keluar:</strong></td>
                                <td>
                                    @if($parkingEntry->parkingExit)
                                        {{ $parkingEntry->parkingExit->exit_location ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- QR Code dan Aksi untuk Pengguna -->
        <div class="col-xl-4">
            <!-- Card untuk QR Code Berbasis ID Entri -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">QR Code ID Entri (Barcode Keluar)</h5>
                </div>
                <div class="card-body text-center">
                    <div class="mb-3" style="display: flex; justify-content: center; align-items: center; min-height: 220px; background: #f8f9fa; border-radius: 10px; padding: 15px;">
                        @if(!empty($entryIdQrCodeImage) && is_string($entryIdQrCodeImage))
                            @if(strpos($entryIdQrCodeImage, '<svg') !== false)
                                <div id="entryIdQrCodeContainer" style="padding: 20px; background: white; border-radius: 10px; display: inline-block; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                                    {!! $entryIdQrCodeImage !!}
                                </div>
                            @else
                                <!-- Ini adalah PNG, tampilkan sebagai base64 image -->
                                <img id="entryIdQrCodeContainer"
                                     src="data:image/png;base64,{{ base64_encode($entryIdQrCodeImage) }}"
                                     alt="QR Code ID Entri"
                                     class="img-fluid border border-1 rounded"
                                     style="padding: 20px; background: white; border-radius: 10px; max-width: 180px; height: auto; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                            @endif
                        @else
                            <p class="text-muted">QR Code ID Entri tidak tersedia</p>
                        @endif
                    </div>
                    <p class="mb-1" style="font-size: 0.85rem;">
                        <strong>{{ $parkingEntry->kode_parkir }}</strong>
                    </p>
                    @if(!empty($entryIdQrCodeImage))
                    <button type="button" class="btn btn-outline-primary mt-2" onclick="downloadEntryIdQRCode()">
                        <i class="ti ti-download"></i> Download QR Code ID
                    </button>
                    @endif

                    <!-- Tambahkan informasi scanning -->
                    @if(!$parkingEntry->parkingExit)
                    <div class="alert alert-info mt-3" style="font-size: 0.85rem;">
                        <i class="ti ti-info-circle"></i> Gunakan QR Code ini untuk scan saat keluar parkir
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Aksi</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('parking.history') }}" class="btn btn-secondary w-100 mb-2">
                        <i class="ti ti-arrow-left"></i> Kembali ke Riwayat
                    </a>
                    <a href="{{ route('parking.history.download-pdf', $parkingEntry->id) }}" class="btn btn-info w-100 mb-2">
                        <i class="ti ti-download"></i> Download PDF Tiket
                    </a>
                    <a href="{{ route('scan.barcode.page') }}" class="btn btn-primary w-100">
                        <i class="ti ti-scan"></i> Scan Barcode
                    </a>

                    @if(!$parkingEntry->parkingExit)
                    <div class="mt-2">
                        <span class="text-muted" style="font-size: 0.8rem;">
                            <i class="ti ti-arrow-right"></i> Gunakan QR Code ID Entri di atas saat scan keluar parkir
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadEntryIdQRCode() {
    const qrCodeContainer = document.getElementById('entryIdQrCodeContainer');

    if (!qrCodeContainer) {
        console.error('QR Code ID Entri container not found');
        alert('QR Code ID Entri tidak ditemukan untuk diunduh.');
        return;
    }

    // Check if the element is an SVG or an image
    if (qrCodeContainer.tagName && qrCodeContainer.tagName.toLowerCase() === 'svg') {
        // Handle SVG download - clone the SVG to add white background
        const clonedSvg = qrCodeContainer.cloneNode(true);
        const svgString = new XMLSerializer().serializeToString(clonedSvg);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Set canvas size with padding
        const svgWidth = qrCodeContainer.viewBox.baseVal.width || 250;
        const svgHeight = qrCodeContainer.viewBox.baseVal.height || 250;
        canvas.width = svgWidth + 40;
        canvas.height = svgHeight + 40;

        // Fill with white background
        ctx.fillStyle = 'white';
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        const img = new Image();
        img.onload = function() {
            // Center the image on canvas with padding
            const x = (canvas.width - img.width) / 2;
            const y = (canvas.height - img.height) / 2;
            ctx.drawImage(img, x, y);

            const pngUrl = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.href = pngUrl;
            downloadLink.download = 'entry-id-qr-code-{{ $parkingEntry->id }}.png';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        };

        img.onerror = function() {
            console.error('Error loading SVG for canvas conversion');
            alert('Gagal mengunduh QR code ID Entri. Silakan coba lagi.');
        };

        const blob = new Blob([svgString], { type: 'image/svg+xml' });
        const url = URL.createObjectURL(blob);
        img.src = url;
    } else if (qrCodeContainer.tagName && qrCodeContainer.tagName.toLowerCase() === 'img') {
        // Handle image download
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');

        // Create image element from the source
        const imgElement = new Image();
        imgElement.crossOrigin = "Anonymous"; // Untuk menghindari CORS issues
        imgElement.onload = function() {
            // Add padding and white background
            const originalWidth = imgElement.width;
            const originalHeight = imgElement.height;
            const padding = 25;
            canvas.width = originalWidth + (padding * 2);
            canvas.height = originalHeight + (padding * 2);

            // Fill with white background
            ctx.fillStyle = 'white';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Draw the image with padding
            ctx.drawImage(imgElement, padding, padding);

            // Convert to data URL and download
            const pngUrl = canvas.toDataURL('image/png');
            const downloadLink = document.createElement('a');
            downloadLink.href = pngUrl;
            downloadLink.download = 'entry-id-qr-code-{{ $parkingEntry->id }}.png';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        };

        imgElement.onerror = function() {
            console.error('Error loading image for canvas conversion');
            alert('Gagal mengunduh QR code ID Entri. Silakan coba lagi.');
        };

        imgElement.src = qrCodeContainer.src;
    } else {
        // If it's a div containing the SVG
        const svgElement = qrCodeContainer.querySelector && qrCodeContainer.querySelector('svg');
        if (svgElement) {
            downloadEntryIdSVGAsPNG(svgElement);
        } else {
            console.error('QR Code ID Entri element not found or not supported for download');
            alert('Gagal mengunduh QR code ID Entri. Format tidak didukung.');
        }
    }
}

function downloadEntryIdSVGAsPNG(svgElement) {
    const clonedSvg = svgElement.cloneNode(true);
    const svgData = new XMLSerializer().serializeToString(clonedSvg);
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    // Set canvas size with padding
    const svgSize = svgElement.viewBox.baseVal || { width: 250, height: 250 };
    const padding = 20;
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
        downloadLink.download = 'entry-id-qr-code-{{ $parkingEntry->id }}.png';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    };

    img.onerror = function() {
        console.error('Error loading SVG for canvas conversion');
        alert('Gagal mengunduh QR code ID Entri. Silakan coba lagi.');
    };

    // Create a blob URL for the SVG with padding
    const blob = new Blob([svgData], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);
    img.src = url;
}
</script>
@endsection