<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>@yield('title')</title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="NCNV">
        <meta name="author" content="The Red Team">
        @section('style')
        <!-- <link href="assets/less/styles.less" rel="stylesheet/less" media="all">  -->
        <link href="{{asset('assets/css/theme.css?=140')}}" rel="stylesheet">
        <link href="{{asset('assets/css/main.css')}}" rel="stylesheet">
        @show
    </head>
    <body class="">
        @yield('content')
    </body>
</html>
