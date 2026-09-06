<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bag Barcode - {{ $bag->bag_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .barcode-card {
            background: #fff;
            border: 2px solid #333;
            border-radius: 8px;
            padding: 30px 40px;
            text-align: center;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .system-name { font-size: 14px; font-weight: bold; color: #666; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 2px; }
        .bag-label { font-size: 12px; color: #999; margin-bottom: 5px; }
        .bag-no { font-family: monospace; font-size: 22px; font-weight: bold; color: #222; margin-bottom: 10px; }
        .barcode-img { margin: 10px auto; display: block; }
        .divider { border: none; border-top: 1px dashed #ccc; margin: 15px 0; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; text-align: left; }
        .info-item { }
        .info-label { font-size: 10px; color: #999; text-transform: uppercase; }
        .info-value { font-size: 12px; font-weight: bold; color: #333; }
        .status-badge { display: inline-block; background: #333; color: #fff; padding: 2px 10px; border-radius: 20px; font-size: 11px; margin-top: 10px; text-transform: uppercase; }
        .no-print { text-align: center; margin-top: 20px; }
        .no-print button { padding: 10px 30px; background: #333; color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; }
        @media print {
            body { background: #fff; }
            .no-print { display: none; }
            .barcode-card { box-shadow: none; border: 1px solid #333; }
        }
    </style>
</head>
<body onload="window.print()">
    <div>
        <div class="barcode-card">
            <div class="system-name">{{ setting('system_name') ?? config('app.name') }}</div>
            <div class="bag-label">BAG / MANIFEST</div>
            <div class="bag-no">{{ $bag->bag_no }}</div>
            <img class="barcode-img"
                 src="data:image/png;base64,{{ DNS1D::getBarcodePNG($bag->bag_no, 'C93', 2, 60) }}"
                 alt="{{ $bag->bag_no }}" />
            <hr class="divider">
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">From</div>
                    <div class="info-value">{{ $bag->fromBranch->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">To</div>
                    <div class="info-value">{{ $bag->toBranch->name ?? 'N/A' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Total Parcels</div>
                    <div class="info-value">{{ $bag->total_parcels }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date</div>
                    <div class="info-value">{{ $bag->created_at->format('d M Y') }}</div>
                </div>
            </div>
            <div>
                <span class="status-badge">{{ strtoupper(str_replace('_', ' ', $bag->status)) }}</span>
            </div>
        </div>

        <div class="no-print">
            <button onclick="window.print()">&#x1F5A8; Print Barcode</button>
        </div>
    </div>
</body>
</html>
