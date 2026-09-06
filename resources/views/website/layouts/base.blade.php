<!DOCTYPE html>
<html lang="{{App::getLocale() ?? 'en'}}" dir="{{ isRtl() }}">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="title" content="{{ setting('meta_title') }}" />
    <meta name="description" content="{{ setting('meta_description') }}" />
    <meta name="keywords" content="{{ setting('meta_keywords') }}" />
    <meta name="author" content="{{ setting('author_name') }}">
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> --}}
    <!-- END SEO -->

    <!-- Open Graph -->
    <meta property="og:title" content="{{ setting('og_title') }}" />
    <meta property="og:description" content="{{ setting('meta_description') }}" />
    <meta property="og:url" content="{{ url('/') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:locale" content="{{ app()->getLocale() }}" />
    <meta property="og:site_name" content="{{ setting('system_name') }}" />
    <meta property="og:image" content="{{ getFileLink('original_image', setting('og_image')) }}" />
    <meta property="og:image:width" content="1200" />
    <meta property="og:image:height" content="630" />
    <!-- END Open Graph -->

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:site" content="{{ setting('system_name') }}" />
    <meta name="twitter:creator" content="{{ setting('author_name') }}" />
    <meta name="twitter:title" content="{{ setting('meta_title') }}" />
    <meta name="twitter:description" content="{{ setting('meta_description') }}" />
    <meta name="twitter:image" content="{{ getFileLink('original_image', setting('og_image')) }}" />
    @if (setting('meta_title') != '')
        <title>{{ setting('meta_title') }}</title>
    @else
        <title>@yield('title', setting('system_name'))</title>
    @endif
    @php
        $icon = setting('favicon');
        @$icon['image_57x57_url'] = $icon['image_80X80'];
        @$icon['image_60x60_url'] = $icon['image_80X80'];
        @$icon['image_72x72_url'] = $icon['image_80X80'];
        @$icon['image_76x76_url'] = $icon['image_80X80'];
        @$icon['image_114x114_url'] = $icon['image_80X80'];
        @$icon['image_144x144_url'] = $icon['image_80X80'];
        @$icon['image_120x120_url'] = $icon['image_80X80'];
        @$icon['image_144x144_url'] = $icon['image_391x541'];
        @$icon['image_152x152_url'] = $icon['image_391x541'];
        @$icon['image_180x180_url'] = $icon['image_391x541'];
        @$icon['image_192x192_url'] = $icon['image_391x541'];
        @$icon['image_32x32_url'] = $icon['image_80X80'];
        @$icon['image_96x96_url'] = $icon['image_80X80'];
        @$icon['image_16x16_url'] = $icon['image_80X80'];
    @endphp

    @if ($icon)
        <link rel="apple-touch-icon" sizes="57x57"
            href="{{ $icon != [] && @is_file_exists($icon['image_57x57_url']) ? static_asset($icon['image_57x57_url']) : static_asset('images/default/favicon/favicon-57x57.png') }}">
        <link rel="apple-touch-icon" sizes="60x60"
            href="{{ $icon != [] && @is_file_exists($icon['image_60x60_url']) ? static_asset($icon['image_60x60_url']) : static_asset('images/default/favicon/favicon-60x60.png') }}">
        <link rel="apple-touch-icon" sizes="72x72"
            href="{{ $icon != [] && @is_file_exists($icon['image_72x72_url']) ? static_asset($icon['image_72x72_url']) : static_asset('images/default/favicon/favicon-72x72.png') }}">
        <link rel="apple-touch-icon" sizes="76x76"
            href="{{ $icon != [] && @is_file_exists($icon['image_76x76_url']) ? static_asset($icon['image_76x76_url']) : static_asset('images/default/favicon/favicon-76x76.png') }}">
        <link rel="apple-touch-icon" sizes="114x114"
            href="{{ $icon != [] && @is_file_exists($icon['image_114x114_url']) ? static_asset($icon['image_114x114_url']) : static_asset('images/default/favicon/favicon-114x114.png') }}">
        <link rel="apple-touch-icon" sizes="120x120"
            href="{{ $icon != [] && @is_file_exists($icon['image_120x120_url']) ? static_asset($icon['image_120x120_url']) : static_asset('images/default/favicon/favicon-120x120.png') }}">
        <link rel="apple-touch-icon" sizes="144x144"
            href="{{ $icon != [] && @is_file_exists($icon['image_144x144_url']) ? static_asset($icon['image_144x144_url']) : static_asset('images/default/favicon/favicon-144x144.png') }}">
        <link rel="apple-touch-icon" sizes="152x152"
            href="{{ $icon != [] && @is_file_exists($icon['image_152x152_url']) ? static_asset($icon['image_152x152_url']) : static_asset('images/default/favicon/favicon-152x152.png') }}">
        <link rel="apple-touch-icon" sizes="180x180"
            href="{{ $icon != [] && @is_file_exists($icon['image_180x180_url']) ? static_asset($icon['image_180x180_url']) : static_asset('images/default/favicon/favicon-180x180.png') }}">
        <link rel="icon" type="image/png" sizes="192x192"
            href="{{ $icon != [] && @is_file_exists($icon['image_192x192_url']) ? static_asset($icon['image_192x192_url']) : static_asset('images/favicon-192x192.png') }}">
        <link rel="icon" type="image/png" sizes="32x32"
            href="{{ $icon != [] && @is_file_exists($icon['image_32x32_url']) ? static_asset($icon['image_32x32_url']) : static_asset('images/default/favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="96x96"
            href="{{ $icon != [] && @is_file_exists($icon['image_96x96_url']) ? static_asset($icon['image_96x96_url']) : static_asset('images/default/favicon/favicon-96x96.png') }}">
        <link rel="icon" type="image/png" sizes="16x16"
            href="{{ $icon != [] && @is_file_exists($icon['image_16x16_url']) ? static_asset($icon['image_16x16_url']) : static_asset('images/default/favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ static_asset('images/default/favicon/manifest.json') }}">

        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="msapplication-TileImage"
            content="{{ $icon != [] && @is_file_exists($icon['image_144x144_url']) ? static_asset($icon['image_144x144_url']) : static_asset('images/default/favicon/favicon-144x144.png') }}">
    @else
        <link rel="shortcut icon" href="{{ static_asset('images/default/favicon/favicon-96x96.png') }}">
    @endif
    <link rel="stylesheet" href="{{ static_asset('website') }}/css/all.min.css" />

    <!-- Swiper js -->
    <link rel="stylesheet" href="{{ static_asset('website') }}/css/swiper-bundle.min.css" />
    <!-- Counter Css -->
    <link rel="stylesheet" href="{{ static_asset('website') }}/css/odometer.min.css" />
    <!-- Select2 Css -->
    <link rel="stylesheet" href="{{ static_asset('website') }}/css/select2.min.css" />
    <!-- Animate Animation -->
    <link rel="stylesheet" href="{{ static_asset('website') }}/css/animate.css" />

    <!-- LTR Bootstrap -->
    <link rel="stylesheet" href="{{ static_asset('website') }}/css/bootstrap.min.css" />

    <!-- LTR Styles -->
    <link rel="stylesheet" href="{{ static_asset('website') }}/css/style.css" />
    <link rel="stylesheet" href="{{ static_asset('website') }}/css/style-rtl.css" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">


    @stack('css')


    @if (app()->getLocale() == 'bn')
        <link href="https://fonts.maateen.me/solaiman-lipi/font.css" rel="stylesheet">
        <style>
            :root {
                --body-font: 'SolaimanLipi', Arial, sans-serif !important;
                --header-font: 'SolaimanLipi', Arial, sans-serif !important;
            }

            html *,
            .secondary-font,
            .heading-font {
                font-family: 'SolaimanLipi', Arial, sans-serif !important;
                /*font-weight: normal !important;*/
            }
        </style>
    @else
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">

        <style>
            :root {
                --body-font: 'jost', sans-serif !important;
                --header-font: 'jost', sans-serif !important;
            }
        </style>
        {!! font_link() !!}
    @endif
    <style>
        @if (base64_decode(setting('custom_css')))
            {{ base64_decode(setting('custom_css')) }}
        @endif
    </style>

    @if (setting('is_google_analytics_activated') && setting('tracking_code'))
        {!! base64_decode(setting('tracking_code')) !!}
    @endif
    @if (setting('custom_header_script'))
        {!! base64_decode(setting('custom_header_script')) !!}
    @endif
    @if (setting('is_facebook_pixel_activated') && setting('facebook_pixel_id'))
        {!! base64_decode(setting('facebook_pixel_id')) !!}
    @endif
</head>

<body>
    @include('website.layouts.header')
    @yield('base.content')
    @include('website.layouts.footer')

    <!-- JS -->
    <script src="{{ static_asset('website') }}/js/jquery-3.6.0.min.js"></script>
    <script src="{{ static_asset('website') }}/js/bootstrap.min.js"></script>
    <script src="{{ static_asset('website') }}/js/bootstrap.bundle.min.js"></script>
    <script src="{{ static_asset('website') }}/js/swiper-bundle.min.js"></script>
    <script src="{{ static_asset('website') }}/js/odometer.min.js"></script>
    <script src="{{ static_asset('website') }}/js/appear.min.js"></script>
    <script src="{{ static_asset('website') }}/js/select2.min.js"></script>
    <script src="{{ static_asset('website') }}/js/wow.min.js"></script>
    <script src="{{ static_asset('website') }}/js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    @if (setting('custom_footer_script'))
        {!! base64_decode(setting('custom_footer_script')) !!}
    @endif

    {!! Toastr::message() !!}

    @if (session()->has('error'))
        <script>
            toastr.error("{{ session('error') }}")
        </script>
    @endif
    @if (session()->has('danger'))
        <script>
            toastr.error("{{ session('danger') }}")
        </script>
    @endif
    @if (session()->has('success'))
        <script>
            toastr.success("{{ session('success') }}")
        </script>
    @endif
    @stack('script')

</body>

</html>
