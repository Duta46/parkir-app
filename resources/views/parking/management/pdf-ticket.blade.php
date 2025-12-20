<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tiket Parkir</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 15px;
            padding: 0;
            font-size: 12px;
        }
        .container {
            max-width: 100%;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 15px;
            text-align: center;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .info-section {
            text-align: left;
            margin-bottom: 10px;
        }
        .info-section h3 {
            margin-top: 0;
            margin-bottom: 8px;
            color: #555;
            border-bottom: 1px solid #ddd;
            padding-bottom: 3px;
            font-size: 14px;
        }
        .info-row {
            display: flex;
            margin-bottom: 4px;
        }
        .info-label {
            width: 110px;
            font-weight: bold;
            color: #666;
            font-size: 11px;
        }
        .info-value {
            flex: 1;
            color: #333;
            font-size: 11px;
        }
        .qr-container {
            display: flex;
            justify-content: space-around;
            margin: 15px 0;
            text-align: center;
        }
        .qr-item {
            text-align: center;
        }
        .qr-item img {
            border: 1px solid #ddd;
            padding: 5px;
            background: #fff;
            max-width: 80px;
            height: auto;
        }
        .qr-label {
            margin-top: 5px;
            font-size: 10px;
            color: #555;
        }
        .status-section {
            margin-top: 10px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 10px;
        }
        .status-active {
            color: orange;
            font-weight: bold;
        }
        .status-finished {
            color: green;
            font-weight: bold;
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
                        <br>
                        <span style="font-size: 10px;">Waktu Keluar: {{ $parkingEntry->parkingExit->exit_time->format('d/m/Y H:i:s') }}</span>
                        <br>
                        <span style="font-size: 10px;">Biaya: Rp{{ number_format($parkingEntry->parkingExit->parking_fee, 0, ',', '.') }}</span>
                    @else
                        <span class="status-active">Aktif</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="qr-code">
            <h3>Barcode Keluar</h3>
            @if($qrCodeData)
                <!-- QR Code for exit process (scan by admin/petugas) -->
                <div style="text-align: center; margin: 10px 0; padding: 10px; border: 1px solid #ddd; background: white; display: inline-block;">
                    <img src="data:image/png;base64,{{ base64_encode($qrCodeData) }}" alt="QR Code Keluar" style="max-width: 100px; height: 100px;" />
                </div>
                <p style="margin-top: 5px; font-size: 11px; text-align: center;">Scan barcode ini untuk proses keluar parkir</p>
            @else
                <p style="font-size: 11px; text-align: center;">QR Code tidak tersedia</p>
            @endif
        </div>

        <div class="footer">
            <p>Dicetak pada: {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Sistem Manajemen Parkir &copy; {{ date('Y') }}</p>
        </div>
    </div>
</body>
</html>