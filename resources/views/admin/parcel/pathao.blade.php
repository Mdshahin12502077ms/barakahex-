<!DOCTYPE html>
<html>

<head>
    <title>Label Sticker Printing</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        media="screen,print">
    <link href="https://fonts.google.com/share?selection.family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900">
    <style>
        @media print {
            body {
                font-family: 'Lato', sans-serif;
                color: #ddd000 !important;
                /* size: 4in 5in; */
            }
        }
        body {
            background-color: transparent;
            color: #212529!important;
        }


            .sticker-print {
                width: 101.6mm;
                min-height: 76.2mm;
                border: 1px solid #ddd;
                padding: 15px;
                margin: auto;
            }


            .top-text {
                display: flex;
                justify-content: space-between;
                font-size: 8px;
                font-weight: 600;
            }

            .top-bar .invoice {
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 8px;
                font-weight: 600;
            }


            .image {
                width: 384px;
            }

            .barcode {
                width: 384px !important;
                height: 40px !important;
                margin-bottom: 3px;
            }

        .barcode {
            width: 100% !important;

        }

        .codes .barcode-text {
            width: 100%;
            font-size: 8px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .mid .location{
            border: 1px solid #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 8px;
            padding: 5px;
            font-weight: 600;
        }

        .mid .location-text{
            display: flex;
        }

        .mid .location-text span{
            border: 1px solid #ddd;
            width: 100%;
            font-size: 8px!important;
            font-weight: 600;
            padding-left: 5px;
            padding-right: 5px;
            padding-top: 4px;
            padding-bottom: 4px;
            justify-content: space-between;
        }

        .customer-address{
            font-size: 8px;
            margin-top: 5px;
        }

        .customer-address .customer-name {
            display: block;
            font-weight: 600;
        }

        .customer-address .contact {
            display: flex;
            justify-content: space-between;
            margin: 1px 0;
        }

        .customer-address .amount {
            border: 1px solid #ddd;
            width: 40%;
            padding: 5px;

        }

        .customer-address .amount span {
            font-weight: 600;
            font-size: 8px!important;
        }

        .customer-address .team {
            font-weight: 600;
            font-size: 8px!important;
        }

        .note {
            border: 1px solid #ddd !important;
            width: 100%;
            padding: 5px;
            margin-top: 3px;
        }

        .note span {
            padding-top: 4px;
            padding-bottom: 4px;
            padding-left: 5px;
            padding-right: 5px;
            font-size: 8px!important;
            display: flex;
            align-items: center;
        }

        .name {
            font-size: 8px!important;
            font-weight: bold;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        @page {
            size: 4in 5in;
            outline: 1pt dotted;
            margin: 0;
        }


    </style>
</head>

<body>
    <div class="sticker-print">
        <div class="top-bar mb-1">
            @php
            $deliveryDate  = new DateTime($parcel->delivery_date);
            $formattedDate = $deliveryDate->format('j F');
            @endphp
            <div class="top-text mb-1">
                <span>{{ setting('company_name') }}</span>
                <span>{{ __('date:') }} {{ $formattedDate }}</span>
            </div>
            <span class="invoice">{{ $parcel->parcel_no }}</span>
        </div>
        <div class="codes">
            <div class="text-center">
                <img class="barcode mb-1"
                    src="data:image/svg;base64,{{ DNS1D::getBarcodePNG($parcel->parcel_no, 'C93', 1, 18) }}"
                    alt="barcode" />
                <p class="text barcode-text">{{ @$parcel->pickupBranch->name }}</p>
            </div>
        </div>
        <div class="mid">
            <div class="location">
                <span>{{ $parcel->customer_address }}</span>
            </div>
            <div class="location-text">
                @if($parcel->home_delivery == '1')
                    <span>{{  __('home_delivery') }}
                    </span>
                @endif
                @if($parcel->open_box == '1')
                    <span>{{ __('open_box') }}</span>
                @endif
            </div>
        </div>
        <div class="customer-address">
            <span class="customer-name">{{ $parcel->customer_name }}</span>
            <div class="contact">
                <span>{{ $parcel->customer_phone_number }}</span>
                <span><strong>{{ __('weight:') }} {{ $parcel->weight }} {{ setting('default_weight') }}</strong></span>
            </div>
            <span>{{ $parcel->customer_address }}</span>
            <div class="amount mt-1 mb-2">
                <span>{{ $parcel->price }} {{ setting('default_currency') }}</span>
            </div>
            <span><strong>{{ __('order_id:') }} {{ $parcel->customer_invoice_no }}</strong></span><br>
            <span class="team">{{ $parcel->merchant->company }}</span>
        </div>
        <div class="note">
            <span>{{ $parcel->note }} </span>
        </div>
        <div class="name mt-1">
            <span>{{ setting('company_name') }}</span>
        </div>
    </div>
    <script>
        window.print();
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>

</html>
