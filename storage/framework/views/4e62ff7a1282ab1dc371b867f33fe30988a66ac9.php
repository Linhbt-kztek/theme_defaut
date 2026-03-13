<!doctype html>
<?php ($config_login = getConfigForLogin()); ?>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" data-layout="vertical" data-topbar="light"
    data-sidebar="light" data-sidebar-size="sm">

<head>
    <meta charset="utf-8" />
    <title><?php echo e($config_login['title_web'] ?? config('software.title')); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Phần mềm bán vé phát triển bởi Cty Cổ Phần Kztek" name="description" />
    <meta content="Themesbrand" name="author" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>" />
    <!-- App favicon -->

    <link rel="shortcut icon" href="<?php echo e($config_login['link_logo_system'] ?? url(config('software.logo'))); ?>">
    <link rel="icon" href="<?php echo e($config_login['link_logo_system'] ?? url(config('software.logo'))); ?>">
    <?php echo $__env->make('layouts.head-css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo \Livewire\Livewire::styles(); ?>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="<?php echo e(url('assets/libs/moment/moment.min.js')); ?>"></script>

    <script src="<?php echo e(url('/')); ?>/assets/libs/dropzone/dropzone.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/assets/libs/filepond/filepond.min.js "></script>
    <script src="<?php echo e(url('/')); ?>/assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/assets/libs/filepond-plugin-image-exif-orientation/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="<?php echo e(url('/')); ?>/assets/libs/filepond-plugin-file-encode/filepond-plugin-file-encode.min.js"></script>

</head>
<?php ($token_passport = auth()->user()->createToken('hrm')->accessToken ?? null); ?> 
<script>
    /*
     * Biến khai báo cấu hình mặc định - const variable system
     * */
    const apiUrl = "<?php echo e(config('kztek_config.url_api')); ?>";
    const publicClientUrl = "<?php echo e(config('kztek_config.url_public')); ?>";
    const clientUrl = "<?php echo e(url('')); ?>";
    const token = '<?php echo e($token_passport); ?>';
    const headersClient = {
        "Access-Control-Allow-Origin": "*",
        "Authorization": "Bearer " + token,
        "Accept": "application/json",
        "sender": "web",
        "ip-address": "xxx.xxx.xxx",
    };
    var currentPathServer = "";
</script>

<?php $__currentLoopData = config('layout_libary')['css']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <link rel="stylesheet" href=" <?php echo e(url($item)); ?>" type="text/css">
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php $__currentLoopData = config('layout_libary')['js']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <script src="<?php echo e(url($item)); ?>"></script>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<body>

    <!-- Begin page -->
    <div id="layout-wrapper">
        <?php echo $__env->make('layouts.topbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <?php echo $__env->make('layouts.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">
            <div class="page-content">
                <div class="container-fluid">
                    <?php echo $__env->yieldContent('content'); ?>
                    <?php echo \Livewire\Livewire::scripts(); ?>


                </div>

                <div id="overlay-loader-layout" class="overlay">
                    <div class="loader"></div>
                </div>
                <!-- container-fluid -->
            </div>
            <!-- End Page-content -->
            <?php echo $__env->make('layouts.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </div>
        <!-- end main content-->
    </div>
    <!-- END layout-wrapper -->
</body>

<!-- JAVASCRIPT -->

<script src="<?php echo e(url('js/layout.js')); ?>"></script>
<script>
    $(document).ready(function () {
        main.token = '<?php echo e(csrf_token()); ?>';
    });


    <?php if(\Session::has('success')): ?>
        main_layout.alert_main("<?php echo e(\Session::get('success')); ?>");
    <?php endif; ?>
    // Khi người dùng click mở modal từ trong offcanvas
    document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.addEventListener('click', function () {
            // Đóng offcanvas trước
            const offcanvasEl = document.querySelector('.offcanvas.show');
            if (offcanvasEl) {
                const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
                offcanvas.hide();
            }

            // Sau một chút mới mở modal
            setTimeout(() => {
                const targetModal = document.querySelector(this.dataset.openModal);
                const modal = new bootstrap.Modal(targetModal);
                modal.show();
            }, 400);
        });
    });

</script>

<?php echo $__env->yieldContent('script'); ?>

<link rel="stylesheet" href="<?php echo e(url('assets/css/font-layout.css')); ?>">
<style>
    .input-group.input-group-sm>.multiselect-native-select .multiselect {
        padding: .25rem .5rem;
        font-size: .875rem;
        line-height: 1.5;
        padding-right: 1.75rem;
        height: calc(2em);
    }

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

    #layout-wrapper {
        transition: all 0.5s ease;
    }

    [data-sidebar-size="sm"] .app-menu,
    [data-sidebar-size="lg"] .app-menu {
        transition: width 0.7s
    }

    [data-layout=vertical][data-sidebar-size=sm] .navbar-menu {
        width: 0px !important;
    }

    @media (min-width: 768px) {

        [data-layout=vertical][data-sidebar-size=sm-hover] #page-topbar,
        [data-layout=vertical][data-sidebar-size=sm] #page-topbar {
            left: 0px;
        }

        [data-layout=vertical][data-sidebar-size=sm] .main-content {
            margin-left: 0px;
        }
    }
</style>

<!-- App js -->

<?php echo $__env->make('layouts.vendor-scripts', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<script>
    $(document).ready(function () {
        main.token = '<?php echo e(csrf_token()); ?>';
    })
</script>

</html><?php /**PATH C:\laragon\www\theme_defaut\resources\views/layouts/master.blade.php ENDPATH**/ ?>