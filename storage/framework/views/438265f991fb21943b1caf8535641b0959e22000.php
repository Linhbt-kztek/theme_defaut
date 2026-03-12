<!-- ========== App Menu ========== -->
<div class="app-menu bg-white navbar-menu shadow-md">
    <!-- LOGO -->
    <div class="navbar-brand-box d-flex oo">
        <!-- Dark Logo-->
        <a href="index" class="logo logo-dark">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('assets/images/logo-dark.png')); ?>" alt="" height="15">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('assets/images/logo-dark.png')); ?>" alt="" height="30">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index" class="logo logo-light">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('assets/images/logo-sm.png')); ?>" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('assets/images/logo-light.png')); ?>" alt="" height="30">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar" class="position-relative">
        <div class="container-fluid">
            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav bg-white" id="navbar-nav">
                 <?php echo $__env->make('components.left-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </ul>
        <div class=" text-center w-100 license overflow-hidden">
                <p class="mb-0 text-muted  overflow-hidden">Phiên bản &copy;
                    <script>document.write(new Date().getFullYear())</script>
                <p class="mb-0 text-muted  overflow-hidden">Powered by Kztek Software</p>
            </div>
        </div>

        <!-- Sidebar -->

    </div>
    <div class="text-bg position-absolute bottom-0 left-0 h-auto">
        <p class="m-0"><span class="mb-3">K</span><br><span>Z</span></p>
    </div>

</div>

</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
<link rel="stylesheet" href="<?php echo e(url('assets/css/sidebar.css?v=') . time()); ?>"><?php /**PATH C:\laragon\www\theme_defaut\resources\views/layouts/new-sidebar.blade.php ENDPATH**/ ?>