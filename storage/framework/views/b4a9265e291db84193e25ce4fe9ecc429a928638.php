<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.echarts'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Charts
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Echarts
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Line Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-line" data-colors='["--vz-primary"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Stacked Line Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-line-stacked"
                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-secondary", "--vz-info"]'
                        class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Area Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-area" data-colors='["--vz-primary"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Stacked Area Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-area-stacked"
                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-light", "--vz-info"]'
                        class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Step Line</h4>
                </div>
                <div class="card-body">
                    <div id="chart-step-line" data-colors='["--vz-primary", "--vz-gray-300", "--vz-info"]'
                        class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Line Y Category</h4>
                </div>
                <div class="card-body">
                    <div id="chart-line-y-category" data-colors='["--vz-primary"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Basic Bar</h4>
                </div>
                <div class="card-body">
                    <div id="chart-bar" data-colors='["--vz-primary"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Bar Label</h4>
                </div>
                <div class="card-body">
                    <div id="chart-bar-label-rotation"
                        data-colors='["--vz-primary", "--vz-info", "--vz-success", "--vz-gray-300"]' class="e-charts">
                    </div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Horizontal Bar</h4>
                </div>
                <div class="card-body">
                    <div id="chart-horizontal-bar" data-colors='["--vz-primary", "--vz-info"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Stacked Horizontal Bar</h4>
                </div>
                <div class="card-body">
                    <div id="chart-horizontal-bar-stacked"
                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-gray-300", "--vz-info"]'
                        class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Pie Charts</h4>
                </div>
                <div class="card-body">
                    <div id="chart-pie"
                        data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-gray-300", "--vz-info"]'
                        class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Doughnut Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-doughnut"
                        data-colors='["--vz-primary", "--vz-primary-rgb, 0.80", "--vz-primary-rgb, 0.70", "--vz-primary-rgb, 0.60", "--vz-primary-rgb, 0.45"]'
                        class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Basic Scatter Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-scatter" data-colors='["--vz-primary"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Candlestick Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-candlestick" data-colors='["--vz-primary", "--vz-info"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->

    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Graph Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-graph" data-colors='["--vz-primary"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Treemap Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-treemap" data-colors='["--vz-primary", "--vz-info"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Sunburst Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-sunburst" data-colors='["--vz-info", "--vz-primary"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Parallel Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-parallel" data-colors='["--vz-primary"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Sankey Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-sankey"
                        data-colors='["--vz-info", "--vz-success", "--vz-warning", "--vz-danger", "--vz-primary"]'
                        class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Funnel Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-funnel"
                        data-colors='["--vz-primary", "--vz-secondary, "--vz-info", "--vz-success", "--vz-wrning"]'
                        class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Gauge Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-gauge" data-colors='["--vz-primary"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Heatmap Chart</h4>
                </div>
                <div class="card-body">
                    <div id="chart-heatmap" data-colors='["--vz-primary", "--vz-info"]' class="e-charts"></div>
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/libs/echarts/echarts.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/js/pages/echarts.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\bui_linh_project\project\minimal-theme\resources\views/charts-echarts.blade.php ENDPATH**/ ?>