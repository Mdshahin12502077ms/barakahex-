<!doctype html>
<html lang="{{App::getLocale() ?? 'en'}}" dir="{{ isRtl() }}">

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') | {{ setting('system_name') != '' ? setting('system_name') : env('APP_NAME') }}</title>
    <!-- Fav Icon  -->
    @php
        @$icon = setting('admin_favicon');

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
        <link rel="apple-touch-icon" sizes="80X80"
            href="{{ $icon != [] && @is_file_exists($icon['image_57x57_url']) ? static_asset($icon['image_57x57_url']) : static_asset('images/default/favicon/favicon-80X80.png') }}">
        <link rel="apple-touch-icon" sizes="40x40"
            href="{{ $icon != [] && @is_file_exists($icon['image_40x40_url']) ? static_asset($icon['image_40x40_url']) : static_asset('images/default/favicon/favicon-40x40.png') }}">
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

    <link rel="manifest" href="{{ static_asset('images/default/favicon/manifest.json')}}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ static_asset('admin/images/favicon/ms-icon-144x144.png')}}">
    <meta name="theme-color" content="#ffffff">
    <!-- CSS Files -->
    <!--====== LineAwesome ======-->
    <link rel="stylesheet" href="{{ static_asset('admin/css/line-awesome.min.css') }}">
    <!--====== select2 CSS ======-->
    <link rel="stylesheet" href="{{ static_asset('admin/css/select2.min.css') }}">
    <!--====== Nestable CSS ======-->
    <link rel="stylesheet" href="{{ static_asset('admin/css/nestable.css') }}">
    <!--====== Summernote CSS ======-->
    <link rel="stylesheet" href="{{ static_asset('admin/css/summernote-lite.min.css') }}">
    <!--====== datatable ======-->
    <link rel="stylesheet" href="{{ static_asset('admin/css/timeline.css') }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/material-design-iconic-font.min.css') }}">
    <link href="{{ static_asset('admin/css/jquery.dataTables.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ static_asset('admin/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/app.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/admin-rtl.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ static_asset('admin/css/responsive.min.css') }}">
    <link rel="stylesheet" href="{{ static_asset('admin/flatpickr/flatpickr.css') }}">

    @stack('css')
    @if(app()->getLocale() == 'bn')
        <link href="https://fonts.maateen.me/solaiman-lipi/font.css" rel="stylesheet">
        <style>
            :root {
                --body-fonts: 'SolaimanLipi', Arial, sans-serif !important;
                --heading-font: 'SolaimanLipi', Arial, sans-serif !important;
            }

            /*html * ,.secondary-font, .heading-font {*/
            /*    font-family: 'SolaimanLipi', Arial, sans-serif !important;*/
            /*    !*font-weight: normal !important;*!*/
            /*}*/
        </style>
    @else
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --body-fonts: 'jost', sans-serif !important;
                --heading-font: 'jost', sans-serif !important;
            }
        </style>
    @endif
</head>

<body>
    <input type="hidden" class="base_url" value="{{ url('/') }}">
    @yield('base.content')
    <script src="{{ static_asset('admin/js/jquery.min.js') }}"></script>
    <!--====== Bootstrap & Popper JS ======-->
    <script src="{{ static_asset('admin/js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js"></script>


    <script src="https://js.pusher.com/beams/1.0/push-notifications-cdn.js"></script>


    <script>
        window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        axios.defaults.withCredentials = true;
        var path = {!! json_encode(url('/')) !!};
    </script>

    <script src="{{ static_asset('admin/js/jquery.dataTables.min.js') }}"></script>
    {{--
    <script src="{{ static_asset('admin/datatables-bs5/datatables-bootstrap5.js') }}"></script> --}}
    <script src="{{ static_asset('admin/js/dataTables.responsive.min.js') }}"></script>
    <!--====== NiceScroll ======-->
    <script src="{{ static_asset('admin/js/jquery.nicescroll.min.js') }}"></script>
    <!--====== Summernote JS ======-->
    <script src="{{ static_asset('admin/js/summernote-lite.min.js') }}"></script>
    <!--====== select2 JS ======-->
    <script src="{{ static_asset('admin/js/select2.min.js') }}"></script>
    <!--====== Chart JS ======-->
    <script src="{{ static_asset('admin/js/chart.min.js') }}"></script>
    @stack('js_asset')
    <script src="{{ static_asset('admin/flatpickr/flatpickr.js') }}"></script>
    <!--====== MainJS ======-->
    <script src="{{ static_asset('admin/js/app.js') }}"></script>
    <!--============= toastr=======-->
    <script src="{{ static_asset('admin/js/toastr.min.js') }}"></script>
    {!! Toastr::message() !!}
    <script src="{{ static_asset('admin/js/sweetalert211.min.js') }}"></script>
    @if (auth()->check() && auth()->user()->role_id > 1)
        <script src="{{ static_asset('admin/js/OneSignalSDK.js') }}" defer></script>
    @endif
    @stack('js')
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
    @if ($errors->any())
        <script>
            @foreach ($errors->all() as $error)
                toastr.error("{{ $error }}");
            @endforeach
        </script>
    @endif

    @php
        if (Sentinel::check()) {
            $route = '';
            $auth = Sentinel::getUser() ?? jwtUser();
            ;
            $notification_count = App\Models\NotificationUser::where('user_id', $auth->id)->where('is_read', 0)->count();
        }
    @endphp

    @if (setting('is_pusher_notification_active') && Sentinel::check())
        <script src="{{ static_asset('admin/js/pusher.min.js') }}"></script>
        <script>
            var routeUrl = "{{ $route }}";
            let notificationCount = {{ $notification_count }};
            const pusher = new Pusher('{{ setting('pusher_app_key') }}', {
                cluster: '{{ setting('pusher_app_cluster') }}',
                encrypted: true
            });
            const channel = pusher.subscribe('notification-send-{{ Sentinel::getUser()->id }}');
            channel.bind('App\\Events\\PusherNotification', (data) => {
                var imageUrl = data.image ? data.image : "{{ static_asset('admin/images/default/user32x32.jpg') }}";
                var notificationHtml = `
                    <li>
                        <a class="dropdown-item" href="${routeUrl.replace('__notification_id__', data.id)}" style="text-align:left">
                            <div class="notification-content d-flex justify-content-between">
                                <div class="notification-img inst-avtar">
                                    <img src="${imageUrl}" alt="">
                                </div>
                                <div class="notification-text">
                                    <h6>${data.created_by}</h6>
                                    <p>${data.details}</p>
                                </div>
                                <span class="notification-time" style="text-align:right">${data.created_at}</span>
                            </div>
                        </a>
                    </li>`;
                $('.pusher-notification').append(notificationHtml);
                toastr[data.message_type](data.message);
                notificationCount++;
                $('.has_notifications').text(notificationCount);
                $('.has_notifications').show();
            });
        </script>
    @endif
    <script>
        $.each($('ul.sub-menu'), function (index, item) {
            if ($(item).find('li').length == 0) {
                // $(item).parents('li').hide();
            }
        })
        flatpickr(".date-range", {
            mode: "range",
            dateFormat: "Y-m-d",
        });

        flatpickr(".date-picker", {
            dateFormat: "Y-m-d",
            enableTime: false,
            time_24hr: false,
        });
    </script>
    {{-- Ajax Select2 Search Global Function --}}
    <script>
        const delivery_man_search_url = "{{ route('get-delivery-man-live') }}";
        const getLiveSearch = (searchUrl, placeholder = 'Select Value') => {
            return {
                placeholder: placeholder,
                minimumInputLength: 2,
                ajax: {
                    type: "GET",
                    dataType: 'json',
                    url: searchUrl,
                    data: function (params) {
                        return {
                            q: params.term
                        }
                    },
                    delay: 400,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        }
                    },
                    cache: true
                }
            }
        }
    </script>
    @stack('script')
    @isset($dataTable)
        {{ $dataTable->scripts() }}
    @endisset
    @isset($datatable)
        {{ $datatable->scripts() }}
    @endisset
    <script>
        $(document).ready(function () {
            var dataTableBuilder = $('#dataTableBuilder');
            if (dataTableBuilder.length) {
                var length = $('#dataTableBuilder_length');
                var search = $('#dataTableBuilder_filter');
                search.find('label').addClass('mb-0');
                search.addClass('d-flex');
                search.appendTo('#data_table_option_container');
                length.appendTo('#data_table_option_container');
                var dataTableButtons = $('.dt-buttons');
                if (dataTableButtons.length && dataTableButtons.html().length === 0) {
                    dataTableButtons.remove();
                }
                $('tfoot').remove();
            }
        });
    </script>
    <script src="{{ static_asset('admin/js/custom.js')}}"></script>
</body>

</html>