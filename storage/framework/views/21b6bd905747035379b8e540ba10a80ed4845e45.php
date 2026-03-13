<!-- ========== App Menu ========== -->
<?php
    $config = getConfigForLogin();
?>
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- LOGO -->

        <div class="d-flex justify-content-between position-relative align-items-center"
            style="height: 65px; weight:100% !important">

            <a href="<?php echo e(route('home')); ?>" class="d-flex align-items-center" style="width: fit-content; height: 100%;">
                <img src="<?php echo e($config['link_logo_system'] ?? url(config('software.logo_dark'))); ?>" alt=""
                    style="max-width: 60%; max-height: 60px;">
            </a>
            <button type="button" id="sidebarCloseBtn" class="btn btn-sm p-0 fs-18 header-item sidebar-close-btn"
                aria-label="Đóng menu">
                <i class=" ri-arrow-left-line"></i>
            </button>
        </div>
    </div>

    <div id="scrollbar">

        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                <?php echo $__env->make('components.left-menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const closeBtn = document.getElementById('sidebarCloseBtn');
        if (!closeBtn) return;

        closeBtn.addEventListener('click', function (e) {
            e.stopPropagation(); // tránh kích hoạt sự kiện khác
            e.preventDefault();

            // Ép sidebar về trạng thái "sm" (đóng)
            try {
                // documentElement (html) là nơi template dùng data-sidebar-size
                document.documentElement.setAttribute('data-sidebar-size', 'sm');

                // ẩn overlay nếu có
                // const overlay = document.querySelector('.vertical-overlay');
                // if (overlay) overlay.style.display = 'none';

                // remove any class that forces the overlay/menu open
                document.body.classList.remove('vertical-sidebar-enable');

                // set hamburger icon to closed state
                document.querySelector('.hamburger-icon')?.classList.remove('open');

                // optional: if a theme script sets inline style width on .navbar-menu, remove it
                const navMenu = document.querySelector('.navbar-menu');
                if (navMenu) {
                    navMenu.style.width = ''; // remove inline override
                }
            } catch (err) {
                console.error('Close sidebar error', err);
            }
        });
    });
</script>



<style>
    .navbar-brand-box .logo {
        margin-top: 5px;
    }

    a.active {
        color: #ffffff !important;
        font-size: 18px !important;
        font-weight: 500 !important;
        background-color: #0f1c2f !important;
    }


    .navbar-menu .navbar-nav .nav-link {
        font-size: 17px;
        color: #eeeeee;
    }

    .navbar-menu .navbar-nav .nav-link:hover {
        font-size: 28px;
        color: #4e84fe;
    }

    .navbar-menu {
        background: #fff;
    }



    #scrollbar::-webkit-scrollbar {
        display: none;
    }

    #scrollbar {
        width: 100%;
        overflow-y: auto;
        scrollbar-width: none;
        height: 80vh;
        padding: 10px;
    }

    /* Add transition for sidebar */
    .app-menu {
        transition: all 0.7s ease;
    }

    /* Add transition for main content */
    .main-content {
        transition: margin-left 0.7s ease;
    }

    #page-topbar {
        transition: 0.7s ease;
    }

    li {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    [data-layout=vertical][data-sidebar-size=sm] .navbar-brand-box {
        background: #0f1c2f00;
    }

    /* background-color: #3a3263;
    border-radius: 10px; */
</style>
<!-- <div class="vertical-overlay"></div> --><?php /**PATH C:\laragon\www\theme_defaut\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>