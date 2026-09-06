<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('manifest') }} - {{ $bag->bag_no }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; margin: 0; padding: 20px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .mb-1 { margin-bottom: 5px; }
        .mb-2 { margin-bottom: 10px; }
        .mb-4 { margin-bottom: 20px; }
        .mt-4 { margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .barcode { margin-top: 10px; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print</button>
    </div>
    
    <div class="header">
        <div>
            <h2 style="margin: 0;">{{ setting('system_name') ?? 'System' }}</h2>
            <p class="mb-1">{{ __('from_branch') }}: <strong>{{ $bag->fromBranch->name ?? 'N/A' }}</strong></p>
            <p class="mb-1">{{ __('to_branch') }}: <strong>{{ $bag->toBranch->name ?? 'N/A' }}</strong></p>
            <p class="mb-1">{{ __('date') }}: {{ date('d M, Y') }}</p>
        </div>
        <div class="text-center">
            <h1 style="margin: 0;">{{ __('manifest') }}</h1>
            <div class="barcode">
                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($bag->bag_no, 'C93', 1.5, 30) }}" alt="barcode" />
                <p style="margin: 5px 0 0 0; font-family: monospace;">{{ $bag->bag_no }}</p>
            </div>
        </div>
    </div>

    <div>
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 5%">{{ __('sl') }}</th>
                    <th style="width: 20%">{{ __('parcel_no') }}</th>
                    <th style="width: 25%">{{ __('merchant') }}</th>
                    <th style="width: 25%">{{ __('customer') }}</th>
                    <th style="width: 15%">{{ __('amount') }}</th>
                    <th style="width: 10%">{{ __('signature') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bag->bagParcels as $index => $bagParcel)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-center">
                            <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($bagParcel->parcel->parcel_no, 'C93', 1, 20) }}" alt="barcode" style="margin-bottom: 5px;"/><br>
                            <span style="font-family: monospace; font-size: 11px;">{{ $bagParcel->parcel->parcel_no }}</span>
                        </td>
                        <td>
                            {{ $bagParcel->parcel->merchant->company ?? 'N/A' }}<br>
                            <small>{{ $bagParcel->parcel->merchant->user->phone_number ?? '' }}</small>
                        </td>
                        <td>
                            {{ $bagParcel->parcel->customer_name }}<br>
                            <small>{{ $bagParcel->parcel->customer_phone_number }}</small>
                        </td>
                        <td>{{ format_price($bagParcel->parcel->payable ?? 0) }}</td>
                        <td></td>
                    </tr>
                @endforeach
                @if(count($bag->bagParcels) == 0)
                    <tr>
                        <td colspan="6" class="text-center">{{ __('no_parcels_found') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    
    <div style="display: flex; justify-content: space-between; margin-top: 50px;">
        <div style="border-top: 1px solid #000; padding-top: 5px; width: 200px; text-align: center;">
            {{ __('dispatched_by') }}
        </div>
        <div style="border-top: 1px solid #000; padding-top: 5px; width: 200px; text-align: center;">
            {{ __('received_by') }}
        </div>
    </div>
</body>
</html>
