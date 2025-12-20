<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tiket Parkir</title>
    <style>
        /* Page setup */
        @page {
            margin: 0.2in;
            size: A4;
        }

        /* Base styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            font-size: 12px;
        }

        /* Main container */
        .container {
            width: 95%;
            max-width: 750px;
            min-height: 98vh;
            border: 3px solid #000;
            padding: 20px;
            text-align: center;
            box-sizing: border-box;
            margin: 1vh auto 0;
        }

        /* Header section */
        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 22px;
            color: #333;
        }

        /* Information section */
        .info-section {
            text-align: left;
            margin-bottom: 25px;
        }

        .info-section h3 {
            margin-top: 0;
            margin-bottom: 12px;
            color: #333;
            border-bottom: 3px solid #000;
            padding-bottom: 6px;
            font-size: 18px;
            font-weight: bold;
        }

        .info-row {
            display: flex;
            margin-bottom: 6px;
        }

        .info-label {
            width: 140px;
            font-weight: bold;
            color: #333;
            font-size: 14px;
        }

        .info-value {
            flex: 1;
            color: #333;
            font-size: 14px;
        }

        /* QR code section */
        .qr-code {
            margin: 25px 0;
        }

        .qr-code img {
            border: 3px solid #ddd;
            padding: 15px;
            background: #fff;
            width: 160px;
            height: 160px;
            display: block;
            margin: 0 auto;
        }

        /* Status indicators */
        .status-active {
            color: orange;
            font-weight: bold;
            font-size: 15px;
        }

        .status-finished {
            color: green;
            font-weight: bold;
            font-size: 15px;
        }

        /* Footer section */
        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px solid #ddd;
            color: #666;
            font-size: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Tiket Parkir</h1>
            <p>Sistem Manajemen Parkir</p>
        </div>

        <div class="info-section">
            <h3>Informasi Tiket</h3>
            <div class="info-row">
                <div class="info-label">ID Entry:</div>
                <div class="info-value">{{ $parkingEntry->id }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Kode Parkir:</div>
                <div class="info-value">{{ $parkingEntry->kode_parkir }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Pengguna:</div>
                <div class="info-value">{{ $parkingEntry->user->name }} ({{ $parkingEntry->user->username }})</div>
            </div>
            <div class="info-row">
                <div class="info-label">Waktu Masuk:</div>
                <div class="info-value">{{ $parkingEntry->entry_time->format('d/m/Y H:i:s') }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Jenis Kendaraan:</div>
                <div class="info-value">{{ $parkingEntry->vehicle_type ?: '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Plat Nomor:</div>
                <div class="info-value">{{ $parkingEntry->vehicle_plate_number ?: '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    @if($parkingEntry->parkingExit)
                        <span class="status-finished">Selesai</span>
                        <div>Waktu Keluar: {{ $parkingEntry->parkingExit->exit_time->format('d/m/Y H:i:s') }}</div>
                        <div>Biaya: Rp{{ number_format($parkingEntry->parkingExit->parking_fee, 0, ',', '.') }}</div>
                    @else
                        <span class="status-active">Aktif</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="qr-code">
            <h3>QR Code ID Entri (Barcode Keluar)</h3>
            @if($qrCodeData)
                <img src="data:image/png;base64,{{ base64_encode($qrCodeData) }}" alt="QR Code ID Entri" />
                <p>Gunakan QR Code ini untuk scan saat keluar parkir</p>
                <p style="font-weight: bold; font-size: 16px;">{{ $parkingEntry->kode_parkir }}</p>
            @else
                <p>QR Code tidak tersedia</p>
            @endif
        </div>

        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Sistem Manajemen Parkir &copy; {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>
