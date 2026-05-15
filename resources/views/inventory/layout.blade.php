@extends('layouts.master')

@section('title')
    Quản lý kho
@endsection

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single,
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            min-height: 38px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #eff2f7;
            border-color: #d7dce2;
            color: #212529;
        }
    </style>
@endsection

@section('content')
    @include('inventory.partials.alerts')
    @yield('inventory_content')
@endsection

@section('script-bottom')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            $('.inventory-select2').select2({
                width: '100%',
                allowClear: true,
                placeholder: function () {
                    return $(this).data('placeholder') || 'Lựa chọn';
                }
            });
        });
    </script>
    @yield('inventory_script_bottom')
@endsection
