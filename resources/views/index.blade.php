@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Home
        @endslot
        @slot('title')
            Index
        @endslot
    @endcomponent



    <div class="row">
        <div class="col">
            <div class="h-100">
                {{-- @include('home_include.fist')
                @include('home_include.second') --}}

                {{-- @include('home_include.thirt') --}}
            </div> <!-- end .h-100-->

        </div> <!-- end col -->
    </div>
@endsection
@section('script')
    <script src="{{ config('kztek_config.url_public') . 'assets/libs/swiper/swiper.min.js' }}"></script>
    <link rel="stylesheet" href="{{ config('kztek_config.url_public') . 'assets/css/ticket/index.css' }}">
    <script>
        @if (!empty(session('alert-success')))
            // sweetSuccess('{{ session('alert-success') }}');
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: "{{ session('alert-success') }}",
                showConfirmButton: false,
                timer: 1500,
                showCloseButton: false
            });
        @endif
        @if (!empty(session('alert-error')))
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: "{{ session('alert-error') }}",
                showConfirmButton: false,
                timer: 1500,
                showCloseButton: false
            });
        @endif
    </script>
@endsection
