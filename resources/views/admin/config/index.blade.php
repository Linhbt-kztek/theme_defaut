@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('content')
    <style>
        .tablist_class .nav-link {
            /* color: #313131; */
        }

        .tab-content .title_content {
            /* background-color: #a3a3a3; */
            /* color: #fff !important; */
            text-align: center;
            font-size: 15px;
        }

        .tablist_class .active {
            color: black !important;
            font-size: 13px !important;
            background-color: #ffffff !important;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
        }
    </style>
    @include('components.breadcrumb')
    @php
        $tab = [
            [
                'href' => 'website',
                'name' => 'website',
                'class' => 'ri-global-line',
                'file' => 'include.setting',
            ],
            // [
            //     'href' => 'listDeviceIp',
            //     'name' => 'Danh sách máy bán hàng',
            //     'class' => 'ri-alarm-line',
            //     'file' => 'include.listDeviceIp',
            // ],
        ];

        // if (auth()->user()->can('config_invoice')) {
        //     $tab[] = [
        //         'href' => 'invoice',
        //         'name' => 'Cấu hình hóa đơn điện tử',
        //         'class' => 'ri-file-list-2-line',
        //         'file' => 'include.invoice',
        //     ];
        // }
    @endphp
    <div class="row">
        <div class="">
            <div class="">
                <ul class="nav tablist_class" role="tablist" id="myTabConfig">
                    @foreach ($tab as $key => $value)
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#{{ $value['href'] }}" role="tab"
                                aria-selected="true">
                                <i class="{{ $value['class'] }} align-middle me-1"></i>
                                {{ $value['name'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <!-- Tab panes -->
                <div class="tab-content text-muted">
                    @foreach ($tab as $key => $value)
                        <div class="tab-pane" id="{{ $value['href'] }}" role="tabpanel">
                            @include('admin.config.' . $value['file'])
                        </div>
                    @endforeach
                </div>
            </div><!-- end card-body -->
        </div>
    </div>
@endsection
@section('script')
    <script>
        @if (!empty(session('alert-success')))
            setTimeout(() => {
                main_layout.alert_main("{{ session('alert-success') }}", 'success')
            }, 150)
        @endif

        @if (!empty(session('alert-error')))
            setTimeout(() => {
                main_layout.alert_main("{{ session('alert-error') }}", 'error')
            }, 150)
        @endif
    </script>

    <script>
        // Lưu tab được chọn vào sessionStorage khi chuyển tab (bị trùng với menu tổng ngoài đó)
        $('#myTabConfig .nav-link').on('click', function() {
            const selectedTabConfig = $(this).attr('href'); // Lấy ID của tab
            sessionStorage.setItem('selectedTabConfig', selectedTabConfig);
        });

        // Khi trang được reload, mở tab đã lưu trong sessionStorage
        $(document).ready(function() {
            const selectedTabConfig = sessionStorage.getItem('selectedTabConfig');

            if (selectedTabConfig) {
                $('#myTabConfig .nav-link[href="' + selectedTabConfig + '"]').tab('show');
            } else {
                $('#myTabConfig .nav-link:first').tab('show');
            }
        });
    </script>
@endsection
