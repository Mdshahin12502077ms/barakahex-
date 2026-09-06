<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <meta name="description"
          content="A powerful and conceptual apps base dashboard template that especially build for developers and programmers.">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="{{ getFileLink('originalImage_url',setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="57x57" href="{{ getFileLink('57x57',setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ getFileLink('60x60',setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ getFileLink('72x72',setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ getFileLink('76x76',setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ getFileLink('114x114',setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ getFileLink('120x120',setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ getFileLink('144x144',setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ getFileLink('152x152',setting('favicon')) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ getFileLink('180x180',setting('favicon')) }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ getFileLink('192x192',setting('favicon')) }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ getFileLink('32x32',setting('favicon')) }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ getFileLink('96x96',setting('favicon')) }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ getFileLink('16x16',setting('favicon')) }}">
    <link rel="manifest" href="{{ static_asset('admin/')}}/images/favicon/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ static_asset('admin/')}}/images/favicon/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <!-- Page Title  -->
     <!-- StyleSheets  -->
     <link rel="stylesheet" href="{{ static_asset('admin/css/app.css') }}?v={{ time() }}">
     <link rel="stylesheet" href="{{ static_asset('admin/css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/responsive.min.css') }}">
     {{-- <link rel="stylesheet" href="{{ asset('admin/')}}/css/dashlite.css?ver=2.3.0"> --}}
     <link id="skin-default" rel="stylesheet" href="{{ static_asset('admin/')}}/css/custom.css">

    <title>{{__('parcel').' '.__('details').' '.__('print')}} | {{setting('system_name')}}</title>
</head>

<body class="nk-body print-bg-lighter npc-default has-sidebar">
<div class="nk-app-root">
    <!-- main @s -->
    <div class="nk-main print">
        <div class="nk-content">
            <div class="nk-content-inner">
                <div >
                    <div class="row g-gs">
                        <div class="col-xxl-12 col-sm-12 col-md-12">
                            <div class="card p-4">
                                <div class="justify-content-between d-inline-flex page-header">
                                    <h3 class="section-title">{{setting('system_name')}}</h3></br>
                                    <p class="date">{{ __('date').' '.date('d.m.Y') }}</p>
                                </div>
                                <table class="table">
                                    <tr>
                                        <td class="font-weight-bold">{{ __('phone') }}:</td>
                                        <td>{{ setting('phone') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-weight-bold">{{ __('address') }}:</td>
                                        <td>{{ setting('address') }} <br>
                                        </td>
                                    </tr>
                                </table>


                                <div class="mt-3">
                                    <div class="cod-invoice d-flex">
                                        <div class="font-weight-bold p-2">{{ __('invno') }}:
                                            #{{$parcel->customer_invoice_no}}</div>
                                        <div class="font-weight-bold p-2">{{ __('COD') }}
                                            : {{format_price($parcel->price) }}</div>
                                    </div>
                                    <div class="border p-3">
                                        <div class="d-inline-flex print-table gap-3">
                                            <table>
                                                <tr>
                                                    <td class="font-weight-bold">{{ __('merchant') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('name') }}
                                                        : {{ (@$parcel->merchant_id == 1802 && $parcel->user->user_type == 'merchant_staff' ) ? @$parcel->user->first_name.' '.@$parcel->user->last_name : $parcel->merchant->user->first_name.' '.$parcel->merchant->user->last_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('company_name') }}
                                                        : {{ $parcel->merchant->company }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('phone_number') }}
                                                        : {{ (@$parcel->merchant_id == 1802 && $parcel->user->user_type == 'merchant_staff' ) ? $parcel->user->phone_number : $parcel->merchant->phone_number}} </td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('email') }}
                                                        : {{ $parcel->merchant->user->email }}</td>
                                                </tr>
                                            </table>
                                            <span class="separator"></span>
                                            <table>
                                                <tr>
                                                    <td class="font-weight-bold">{{ __('customer') }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('name') }}
                                                        : {{ $parcel->customer_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('customer_phone') }}
                                                        : {{ $parcel->customer_phone_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('customer_address') }}
                                                        : {{ $parcel->customer_address }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('id')}}: #{{ $parcel->parcel_no }}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{__('weight').': '. $parcel->weight.' '.__(setting('default_weight'))}}</td>
                                                </tr>
                                                <tr>
                                                    <td>{{ __('details') }}: {{ @$parcel->note }}</td>
                                                </tr>
                                            </table>
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
<!-- main @e -->
</div>
<!-- main @e -->
<script src="{{ static_asset('admin/js/jquery.min.js') }}"></script>
<!--====== Bootstrap & Popper JS ======-->
<script src="{{ static_asset('admin/js/bootstrap.bundle.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function () {
        window.print();
    });
</script>
</body>

</html>
