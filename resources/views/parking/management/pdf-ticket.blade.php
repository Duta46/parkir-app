<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tiket Parkir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
            text-align: center;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .info-section {
            text-align: left;
            margin-bottom: 20px;
        }
        .info-section h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        .info-label {
            width: 150px;
            font-weight: bold;
            color: #666;
        }
        .info-value {
            flex: 1;
            color: #333;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .qr-code img {
            border: 1px solid #ddd;
            padding: 10px;
            background: #fff;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 12px;
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
                <div class="info-label">Lokasi Masuk:</div>
                <div class="info-value">{{ $parkingEntry->entry_location ?: '-' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Status:</div>
                <div class="info-value">
                    @if($parkingEntry->parkingExit)
                        <span style="color: green; font-weight: bold;">Selesai</span>
                        <br>
                        <span style="font-size: 12px;">Waktu Keluar: {{ $parkingEntry->parkingExit->exit_time->format('d/m/Y H:i:s') }}</span>
                        <br>
                        <span style="font-size: 12px;">Biaya: Rp{{ number_format($parkingEntry->parkingExit->parking_fee, 0, ',', '.') }}</span>
                    @else
                        <span style="color: orange; font-weight: bold;">Aktif</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="qr-code">
            <h3>Kode QR</h3>
            @if($qrCodeData)
                <img src="data:image/png;base64,{{ $qrCodeData }}" alt="QR Code">
                <p style="margin-top: 10px; font-size: 14px;">Scan untuk proses keluar parkir</p>
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