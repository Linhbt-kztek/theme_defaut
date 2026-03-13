<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-layout="vertical" data-sidebar="dark" data-topbar="dark"
    data-sidebar-size="lg">

<head>
    <meta charset="utf-8" />
    <title>{{ !empty($config_web['title_web']) ? $config_web['title_web'] : 'Phần mềm bán vé' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <!-- App favicon -->
    <link rel="shortcut icon"
        href="{{ !empty($config_web['link_logo']) ? $config_web['link_logo'] : url('assets/images/logo-light.png') }}">
    @include('layouts.head-css')
    @livewireStyles
</head>
<style>
    .page-title-box {
        padding: 15px 1.5rem;
    }

    .page-title-box h4 {
        font-size: 20px !important;
    }
</style>
<script>
    @php
        $user = Illuminate\Support\Facades\Auth::guard('web')->user();
        $token_passport = $user->createToken('bao-tang-vu-tru')->accessToken;
    @endphp
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
    const userLogin = {
        'name': "{{ session('user_name') }}",
        {{-- 'avatar': "{{session('user_avatar') }}", --}} 'id': "{{ session('user_id') }}",
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
@yield('script')
<link rel="stylesheet" href="{{ url('assets/css/font-layout.css') }}">
<style>
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
</style>
<!-- App js -->
{{-- <script src="{{ url('public/assets/js/app.min.js') }}"></script> --}}
@include('layouts.vendor-scripts')
<script src="{{ mix('js/app.js') }}"></script>
<script src="{{ url('js/layout.js') }}"></script>
<script>
    $(document).ready(function() {
        main.token = '{{ csrf_token() }}';
    });

    @if (\Session::has('success'))
        main_layout.alert_main("{{ \Session::get('success') }}");
    @endif
</script>

</html>
