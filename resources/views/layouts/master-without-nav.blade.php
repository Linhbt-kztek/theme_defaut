<!doctype html>

@php($config_login = getConfigForLogin(true))
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-topbar="light">

<head>
    <meta charset="utf-8" />
    <title>{{ !empty($config_login['title_web']) ? $config_login['title_web'] : 'Phần mềm bán vé' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon"
        href="{{ !empty($config_login['link_logo_system']) ? $config_login['link_logo_system'] : url('assets/images/logo-light.png') }}">
    @include('layouts.head-css')
</head>

@yield('body')

@yield('content')

@include('layouts.vendor-scripts')
</body>

</html>
