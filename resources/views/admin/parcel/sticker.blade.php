<!DOCTYPE html>
<html>

<head>
    <title>Label Sticker Printing</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        media="screen,print">
    <style>
        @media print {
            body {
                color: #000000 !important;
                line-height: 11px;
                font-weight: bold;
            }
        }
            /* Your styles here */
            .sticker-print {
                width: 76.2mm;
                min-height: 50.8mm;
                border: 1px solid #ddd;
                /* padding: 15px; */
                margin: auto;
            }

            .bt{
                border-top: 0.01px solid #ddd!important;
            }

            .bb{
                border-bottom: 0.01px solid #ddd!important;
            }

            .bl{
                border-left: 0.01px solid #ddd!important;
            }

            .br{
                border-right: 0.01px solid #ddd!important;
            }

            .gp {
                padding: 5px !important;
            }

            .sticker-print p,
            .sticker-print span {
                font-size: 10px !important;
                display: block;
                line-height: 12px;
                margin-bottom: 0;
            }


            .image {
                width: 80px;
            }
            .image p {
                white-space: nowrap;
                margin-top: 2px;
            }

            .barcode {
                width: 2.5in !important;
                height: 20px !important;
                margin-bottom: 3px;
            }


        .barcode {
            width: 60% !important;

        }

        .codes .bg-dark {
            margin-bottom: 4px;
        }

        @page {
            size: 3in 2in;
            outline: 1pt dotted;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="sticker-print">
        <div class="d-flex justify-content-between  bb hstack gap-3 gp">
            <div class="image">
              <img class="img-fluid"
                    src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : get_media('images/default/logo/logo_dark.png') }}">
                <p class="mb-0">Call: {{setting('phone')}}</p>
            </div>
            <div class="vr"></div>
            <div class="merchant-details ml-1 pt-0">
                <span class="customer ml-1 pt-0">
                    <span class="merchant font-weight-bold">{{ __('merchant') }}:</span> <span
                        class="name">{{ substr($parcel->merchant->company,0, 20) }}</span>
                </span>

                <p class="mb-0">
                    {{ @$parcel->shop->contact_number ? @$parcel->shop->contact_number : (@$parcel->merchant_id == 1802 && $parcel->user->user_type == 'merchant_staff' ? @$parcel->user->phone_number : @$parcel->merchant->phone_number) }}
                </p>

            </div>
        </div>
        <div class="d-flex justify-content-between align-items-center bb gp">
            <div >
                <p class="mb-0 bold">{{ $parcel->customer_name }}, {{ $parcel->customer_phone_number }}</p>
                <span class="text-wrap">{{ Str::substr($parcel->customer_address, 0,100  ) }}</span>
            </div>

        </div>
        <div class="p-1 d-flex justify-content-between bb gp">
            <p class="mb-0">{{ __('invoice') }}: {{ $parcel->customer_invoice_no }}</p>
            <p class="mb-0">Cash Due: {{ format_price($parcel->price) }}</p>
        </div>
        <div class="codes">
            <p class="p-1 bg-dark text-white mt-1 text-wrap">{{ __('Note') }}: {{ $parcel->note }}</p>
            <div class="text-center">
                <img class="barcode"
                    src="data:image/svg;base64,{{ DNS1D::getBarcodePNG($parcel->parcel_no, 'C93', 1, 18) }}"
                    alt="barcode" />
                <p class="text">{{ $parcel->parcel_no }} Print: @php echo date("d-m-Y h:i A"); @endphp</p>
            </div>
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
