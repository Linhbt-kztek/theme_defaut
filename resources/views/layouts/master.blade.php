<!doctype html>
@php($config_login = getConfigForLogin())
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-topbar="light"
    data-sidebar="light" data-sidebar-size="sm">

<head>
    <meta charset="utf-8" />
    <title>{{ $config_login['title_web'] ?? config('software.title') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Phần mềm bán vé phát triển bởi Cty Cổ Phần Kztek" name="description" />
    <meta content="Themesbrand" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- App favicon -->

    <link rel="shortcut icon" href="{{ $config_login['link_logo_system'] ?? url(config('software.logo')) }}">
    <link rel="icon" href="{{ $config_login['link_logo_system'] ?? url(config('software.logo')) }}">
    @include('layouts.head-css')
    @livewireStyles
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ url('assets/libs/moment/moment.min.js') }}"></script>

    <script src="{{url('/')}}/assets/libs/dropzone/dropzone.min.js"></script>
    <script src="{{url('/')}}/assets/libs/filepond/filepond.min.js "></script>
    <script src="{{url('/')}}/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js"></script>
    <script src="{{url('/')}}/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js"></script>
    <script src="{{url('/')}}/assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="{{url('/')}}/assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js"></script>

</head>
@php($token_passport = auth()->user()->createToken('hrm')->accessToken ?? null) 
<script>
    /*
     * Biến khai báo cấu hình mặc định - const variable system
     * */
    const apiUrl = "{{ config('kztek_config.url_api') }}";
    const publicClientUrl = "{{ config('kztek_config.url_public') }}";
    const clientUrl = "{{ url('') }}";
    const token = '{{ $token_passport }}';
    const headersClient = {
        "Access-Control-Allow-Origin": "*",
        "Authorization": "Bearer " + token,
        "Accept": "application/json",
        "sender": "web",
        "ip-address": "xxx.xxx.xxx",
    };
    var currentPathServer = "";
</script>

@foreach (config('layout_libary')['css'] as $item)
    <link rel="stylesheet" href=" {{ url($item) }}" type="text/css">
@endforeach

@foreach (config('layout_libary')['js'] as $item)
    <script src="{{ url($item) }}"></script>
@endforeach

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        @include('layouts.topbar')
        @include('layouts.sidebar')

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    @yield('content')
                    @livewireScripts

                </div>

                <div id="overlay-loader-layout" class="overlay">
                    <div class="loader"></div>
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            @include('layouts.footer')

        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
</body>

<!-- JAVASCRIPT -->
{{--
<script src="{{ url('js/app.js') }}"></script> --}}
<script src="{{ url('js/layout.js') }}"></script>
<script>
    $(document).ready(function () {
        main.token = '{{ csrf_token() }}';
    });


    @if (\Session::has('success'))
        main_layout.alert_main("{{ \Session::get('success') }}");
    @endif
    // Khi người dùng click mở modal từ trong offcanvas
    document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.addEventListener('click', function () {
            // Đóng offcanvas trước
            const offcanvasEl = document.querySelector('.offcanvas.show');
            if (offcanvasEl) {
                const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                offcanvas.hide();
            }

            // Sau một chút mới mở modal
            setTimeout(() => {
                const targetModal = document.querySelector(this.dataset.openModal);
                const modal = new bootstrap.Modal(targetModal);
                modal.show();
            }, 400);
        });
    });

</script>

@yield('script')

<link rel="stylesheet" href="{{ url('assets/css/font-layout.css') }}">
<style>
    .input-group.input-group-sm>.multiselect-native-select .multiselect {
        padding: .25rem .5rem;
        font-size: .875rem;
        line-height: 1.5;
        padding-right: 1.75rem;
        height: calc(2em);
    }

    .overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        z-index: 9999;
    }

    .loader {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-top: -25px;
        margin-left: -25px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes rotation {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    #layout-wrapper {
        transition: all 0.5s ease;
    }

    [data-sidebar-size="sm"] .app-menu,
    [data-sidebar-size="lg"] .app-menu {
        transition: width 0.7s
    }

    [data-layout=vertical][data-sidebar-size=sm] .navbar-menu {
        width: 0px !important;
    }

    @media (min-width: 768px) {

        [data-layout=vertical][data-sidebar-size=sm-hover] #page-topbar,
        [data-layout=vertical][data-sidebar-size=sm] #page-topbar {
            left: 0px;
        }

        [data-layout=vertical][data-sidebar-size=sm] .main-content {
            margin-left: 0px;
        }
    }
</style>

<!-- App js -->
{{--
<script src="{{ url('public/assets/js/app.min.js') }}"></script> --}}
@include('layouts.vendor-scripts')

<script>
    $(document).ready(function () {
        main.token = '{{ csrf_token() }}';
    })
</script>

</html>