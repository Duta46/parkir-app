@extends('layouts.app')

@section('title', 'Detail Parkir - ' . $parkingEntry->kode_parkir)

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Riwayat Parkir /</span> Detail
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

        <!-- QR Code dan Aksi -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">QR Code</h5>
                </div>
                <div class="card-body text-center">
                    @if($qrCodeImage)
                        <div class="mb-3">
                            @if(strpos($qrCodeImage, '<svg') !== false)
                                <div id="qrCodeContainer">
                                    {!! $qrCodeImage !!} <!-- Render SVG directly -->
                                </div>
                            @else
                                <img id="qrCodeContainer"
                                     src="data:image/png;base64,{{ base64_encode($qrCodeImage) }}"
                                     alt="QR Code"
                                     class="img-fluid border border-1 rounded"
                                     style="max-width: 250px; height: auto;">
                            @endif
                        </div>
                        <p class="mb-0">
                            <small class="text-muted">
                                QR Code untuk kode: {{ $parkingEntry->qrCode->code ?? '-' }}
                            </small>
                        </p>
                        <button type="button" class="btn btn-outline-primary mt-2" onclick="downloadQRCode()">
                            <i class="ti ti-download"></i> Download QR Code
                        </button>
                    @else
                        <p class="text-muted">QR Code tidak tersedia</p>
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
                    <a href="{{ route('scan.barcode.page') }}" class="btn btn-primary w-100">
                        <i class="ti ti-scan"></i> Scan Barcode
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadQRCode() {
    const qrCodeContainer = document.getElementById('qrCodeContainer');

    if (!qrCodeContainer) {
        console.error('QR Code container not found');
        return;
    }

    // Check if the element is an SVG or an image
    if (qrCodeContainer.tagName.toLowerCase() === 'svg') {
        // Handle SVG download
        downloadSVGAsPNG(qrCodeContainer);
    } else if (qrCodeContainer.tagName.toLowerCase() === 'img') {
        // Handle image download
        downloadImageAsPNG(qrCodeContainer);
    } else {
        // If it's a div containing the SVG
        const svgElement = qrCodeContainer.querySelector('svg');
        if (svgElement) {
            downloadSVGAsPNG(svgElement);
        } else {
            console.error('QR Code element not found');
        }
    }
}

function downloadSVGAsPNG(svgElement) {
    const svgData = new XMLSerializer().serializeToString(svgElement);
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    const svgSize = svgElement.viewBox.baseVal || { width: 200, height: 200 };
    canvas.width = svgSize.width;
    canvas.height = svgSize.height;

    const img = new Image();
    img.onload = function() {
        ctx.drawImage(img, 0, 0, svgSize.width, svgSize.height);

        const pngUrl = canvas.toDataURL('image/png');
        const downloadLink = document.createElement('a');
        downloadLink.href = pngUrl;
        downloadLink.download = 'qr-code-{{ $parkingEntry->kode_parkir }}.png';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
    };

    // Create a blob URL for the SVG
    const blob = new Blob([svgData], { type: 'image/svg+xml' });
    const url = URL.createObjectURL(blob);
    img.src = url;
}

function downloadImageAsPNG(imgElement) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');

    canvas.width = imgElement.width;
    canvas.height = imgElement.height;

    ctx.drawImage(imgElement, 0, 0);

    const pngUrl = canvas.toDataURL('image/png');
    const downloadLink = document.createElement('a');
    downloadLink.href = pngUrl;
    downloadLink.download = 'qr-code-{{ $parkingEntry->kode_parkir }}.png';
    document.body.appendChild(downloadLink);
    downloadLink.click();
    document.body.removeChild(downloadLink);
}
</script>
@endsection