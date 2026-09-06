<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="description"
        content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ getFileLink('originalImage_url', setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ getFileLink('57x57', setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ getFileLink('60x60', setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ getFileLink('72x72', setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ getFileLink('76x76', setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ getFileLink('114x114', setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ getFileLink('120x120', setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ getFileLink('144x144', setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ getFileLink('152x152', setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ getFileLink('180x180', setting('favicon')) }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ getFileLink('192x192', setting('favicon')) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ getFileLink('32x32', setting('favicon')) }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ getFileLink('96x96', setting('favicon')) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ getFileLink('16x16', setting('favicon')) }}">
    <link rel="manifest" href="{{ static_asset('admin/')}}/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ static_asset('admin/')}}/images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Page Title  -->
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ static_asset('admin/css/app.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/responsive.min.css') }}">
    <link id="skin-default" rel="stylesheet" href="{{ static_asset('admin/')}}/css/custom.css">

    <title>{{__('parcel') . ' ' . __('details') . ' ' . __('print')}} | {{setting('system_name')}}</title>


    <style>
        body {
            font-family: 'jost', sans-serif !important;
        }

        .printCard {
            width: 384px;
            /*height: 576px;*/
            margin: 70px auto;
            border: 1px solid #1a1a1a;
            border-radius: 6px;
        }

        .printCard b,
        .printCard strong {
            font-weight: 500;
        }

        .printCard__header {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            border-bottom: 1px solid #1a1a1a;
        }

        .printCard__header.grid-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .printCard__header.grid-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .printCard__header .printCard__box {
            border-right: 1px solid #1a1a1a;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 2px;
            color: #1a1a1a;
        }

        .printCard__header .printCard__box:last-of-type {
            border: none;
        }

        .printCard__header .printCard__box h4 {
            font-size: 14px;
            text-transform: capitalize;
            text-align: center;
            color: #1a1a1a;
        }

        .printCard__header.grid-3 .printCard__box h4 {
            font-size: 12px;
        }

        .printCard__address {
            padding: 10px;
        }

        .printCard__address .singleAddress h5 {
            font-size: 16px;
            text-transform: capitalize;
            margin: 0;
            min-width: 40px;
        }

        .printCard__address .singleAddress {
            display: flex;
            align-items: baseline;
            gap: 15px;
            margin-bottom: 15px;
            color: #1a1a1a;
        }

        .printCard__address .singleAddress span {
            font-size: 14px;
            display: block;
            margin: 0;
            color: #1a1a1a;
        }

        .printCard__footer {
            border-top: 1px solid #1a1a1a;
            padding: 10px;
        }

        .printCard__footer .barCode {
            font-size: 14px;
            color: #1a1a1a;
        }

        .printCard__footer .barCode .barcode {
            margin: 5px auto;
        }

        .printCard__footer .description .list {
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: space-between;
            color: #1a1a1a;
        }

        .barCode-image {
            height: 70px;
        }
    </style>
</head>

<body class="nk-body print-bg-lighter npc-default has-sidebar">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main print">
            <div class="nk-content">
                <div class="nk-content-inner">
                    <div>
                        <div class="row g-gs">
                            <div class="col-xxl-12 col-sm-12 col-md-12">
                                <div class="printCard ">
                                    <div class="printCard__header  grid-3">
                                        <div class="printCard__box">
                                            <div class="printLogo">
                                                <img class=""
                                                    src="{{ setting('dark_logo') && @is_file_exists(setting('dark_logo')['original_image']) ? get_media(setting('dark_logo')['original_image']) : get_media('images/default/logo/logo_dark.png') }}">
                                            </div>
                                        </div>
                                        <div class="printCard__box">
                                            <strong>Ref#</strong>
                                            <p>{{$parcel->parcel_no}}</p>
                                        </div>
                                        <div class="printCard__box">
                                            <h4>{{__('service')}}:
                                                {{strtoupper($parcel->parcel_type == "outside_city" ? "sub_urban_area" : $parcel->parcel_type)}}
                                            </h4>
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="printCard__header grid-2">

                                        <div class="printCard__box">
                                            <strong><b>{{__('weight')}}:
                                                </b><br>{{ $parcel->weight . ' ' . __(setting('default_weight'))}}</strong>
                                        </div>
                                        <div class="printCard__box">
                                            <strong><b>{{__('value')}}: </b>{{format_price($parcel->price) }}</strong>
                                        </div>
                                    </div>
                                    <div class="printCard__address">
                                        <div class="singleAddress">
                                            <strong style="min-width: 50px; max-width: 50px">{{__('from')}}:</strong>
                                            <div>
                                                <span>{{ (@$parcel->merchant_id == 1802 && $parcel->user->user_type == 'merchant_staff') ? @$parcel->user->first_name . ' ' . @$parcel->user->last_name : $parcel->merchant->user->first_name . ' ' . $parcel->merchant->user->last_name }}</span>
                                                <span>{{ $parcel->merchant->company }}</span>
                                                <span>{{__('mob')}}:
                                                    {{ (@$parcel->merchant_id == 1802 && $parcel->user->user_type == 'merchant_staff') ? $parcel->user->phone_number : $parcel->merchant->phone_number}}</span>
                                                <span>{{__('email')}}: {{ $parcel->merchant->user->email }}</span>
                                            </div>
                                        </div>
                                        <div class="singleAddress">
                                            <strong style="min-width: 50px; max-width: 50px">{{__('to')}}:</strong>
                                            <div>
                                                <span>{{ $parcel->customer_name }}</span>
                                                <span>{{ $parcel->customer_address }}</span>
                                                <span>{{__('mob')}}: {{ $parcel->customer_phone_number }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="printCard__footer pt-3">
                                        <div class="barCode">
                                            <div class="text-center">
                                                <img class="barcode barCode-image"
                                                    src="data:image/svg;base64,{{ DNS1D::getBarcodePNG($parcel->parcel_no, 'C93', 1, 18) }}"
                                                    alt="barcode" style="padding: 0px 25px;" />
                                                <p class="text"> {{__('parcel_id')}} : <b>{{ $parcel->parcel_no }}</b>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="description">
                                            <div class="list"><strong>{{__('note')}} :</strong>
                                                <strong>{{ $parcel->note }}</strong>
                                            </div>
                                            <div class="list">
                                                <strong>{{ __('date') }} :</strong>
                                                <span>{{ now()->format('d-m-Y h:i A') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ static_asset('admin/js/jquery.min.js') }}"></script>
    <!--====== Bootstrap & Popper JS ======-->
    <script src="{{ static_asset('admin/js/bootstrap.bundle.min.js') }}"></script>
    {{--
    <script type="text/javascript">
        $(document).ready(function () {
            window.print();
        });
    </script> --}}
</body>

</html>