<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.Apex_Candlstick_Chart'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Apexcharts
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Apex Candlestick Charts
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Basic Candlestick Chart</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="basic_candlestick" data-colors='["--vz-success", "--vz-primary"]' class="apex-charts"
                        dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Candlestick Synced with Brush Chart (Combo)</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div>
                        <div id="combo_candlestick" data-colors='["--vz-info", "--vz-primary"]' class="apex-charts"
                            dir="ltr"></div>
                        <div id="combo_candlestick_chart" data-colors='["--vz-success", "--vz-primary"]'
                            class="apex-charts" dir="ltr"></div>
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
                    <h4 class="card-title mb-0">Category X-Axis</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="category_candlestick" data-colors='["--vz-success-rgb, 0.65", "--vz-danger-rgb, 0.65"]'
                        class="apex-charts" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

    </div>
    <!-- end row -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/libs/apexcharts/apexcharts.min.js')); ?>"></script>
    <script src="https://apexcharts.com/samples/assets/ohlc.js"></script>
    <!-- for Category x-axis chart -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/dayjs/1.8.17/dayjs.min.js"></script>
    <script src="<?php echo e(URL::asset('assets/js/pages/apexcharts-candlestick.init.js')); ?>"></script>

    <script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\bui_linh_project\project_in_github\minimal-theme\resources\views/charts-apex-candlestick.blade.php ENDPATH**/ ?>