<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.vector'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('css'); ?>
    <link href="<?php echo e(URL::asset('assets/libs/jsvectormap/jsvectormap.min.css')); ?>" rel="stylesheet" type="text/css" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Maps
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Vector Maps
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Markers</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="world-map-line-markers" data-colors='["--vz-light"]' style="height: 420px"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">World Vector Map with Markers</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="world-map-markers" data-colors='["--vz-light"]' style="height: 350px" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">World Vector Map with Image Markers</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="world-map-markers-image" data-colors='["--vz-light"]' style="height: 350px" dir="ltr"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">USA Vector Map</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="usa-vectormap" data-colors='["--vz-primary"]' style="height: 350px"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Canada Vector Map</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="canada-vectormap" data-colors='["--vz-primary"]' style="height: 350px"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->

    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Russia Vector Map</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="russia-vectormap" data-colors='["--vz-primary"]' style="height: 350px"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Spain Vector Map</h4>
                </div><!-- end card header -->

                <div class="card-body">
                    <div id="spain-vectormap" data-colors='["--vz-primary"]' style="height: 350px"></div>
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <script src="<?php echo e(URL::asset('assets/libs/jsvectormap/jsvectormap.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/js/pages/vector-maps.init.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('/assets/js/app.min.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\bui_linh_project\project_in_github\minimal-theme\resources\views/maps-vector.blade.php ENDPATH**/ ?>