@extends('layouts.master')
@section('title')
    @lang('translation.Apex_Column_Chart')
@endsection
@section('content')
    @component('components.breadcrumb')
        @slot('li_1')
            Apexcharts
        @endslot
        @slot('title')
            Apex Column Charts
        @endslot
    @endcomponent
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Basic Column Charts</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="column_chart" data-colors='["--vz-info", "--vz-primary", "--vz-success"]' class="apex-charts"
                        dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Column with Data Labels</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="column_chart_datalabel" data-colors='["--vz-primary"]' class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Stacked Column Charts</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="column_stacked" data-colors='["--vz-primary", "--vz-success", "--vz-info", "--vz-gray-200"]'
                        class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Stacked Column 100</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="column_stacked_chart" data-colors='["--vz-primary", "--vz-info", "--vz-success"]'
                        class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Column with Markers</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="column_markers" data-colors='["--vz-primary", "--vz-success"]' class="apex-charts"
                        dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Column with Rotated Labels</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="column_rotated_labels" data-colors='["--vz-primary"]' class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Column with Nagetive Values</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="column_nagetive_values" data-colors='["--vz-primary", "--vz-info", "--vz-success"]'
                        class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Range Column Chart</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="column_range" data-colors='["--vz-primary", "--vz-info"]' class="apex-charts" dir="ltr">
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Dynamic Loaded Chart</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="dynamicloadedchart-wrap" dir="ltr">
                        <div id="chart-year"
                            data-colors='["--vz-primary", "--vz-success", "--vz-gray-200", "--vz-secondary", "--vz-primary-rgb, 0.6", "--vz-info"]'
                            class="apex-charts"></div>
                        <div id="chart-quarter" class="apex-charts"></div>
                    </div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Distributed Columns Chart</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="column_distributed"
                        data-colors='["--vz-primary", "--vz-success", "--vz-light", "--vz-secondary", "--vz-info", "--vz-primary-rgb, 0.5", "--vz-danger-rgb, 0.5", "--vz-warning-rgb, 0.5"]'
                        class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/apexcharts-column.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
@endsection
