<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ route('home') }}" class="logo logo-dark">
            <img src="{{ !empty($config_web['link_logo']) ? $config_web['link_logo'] : url('assets/images/logo-light.png') }}"
                alt="" style="width: 210px;height: auto;">
        </a>
        <!-- Light Logo-->
        <a href="{{ route('home') }}" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ !empty($config_web['link_logo']) ? $config_web['link_logo'] : url('assets/images/logo-light.png') }}" alt=""
                    height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ !empty($config_web['link_logo']) ? $config_web['link_logo'] : url('assets/images/logo-light.png') }}" alt="" style="width: 30%;">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>
            <ul class="navbar-nav" id="navbar-nav">
                @include('components.left-menu')
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
<style>
    a.active {
        color: #fff !important;
        font-size: 18px !important;
        background-color: #0f1c2f !important;
    }

    a[aria-expanded='true'] {
        color: #fff !important;
    }

    .navbar-menu .navbar-nav .nav-link {
        font-size: 17px;
    }

    .navbar-menu .navbar-nav .nav-link:hover {
        font-size: 18px;
        color: #fff !important;
        background-color: #0f1c2f;
    }

    #scrollbar {
        overflow-y: auto;
        scrollbar-width: none;
        /* Firefox */
    }

    /* Hide scrollbar for Chrome, Safari, and Edge */
    #scrollbar::-webkit-scrollbar {
        display: none;
    }

    /* Style for the scrollbar track */
    #scrollbar {
        width: 100%;
        /* Adjust width as needed */
        height: 80vh;
        /* Adjust height as needed */
        /* background-color: #f0f0f0; */
        /* Adjust background color as needed */
        padding: 10px;
        /* Adjust padding as needed */
    }

    /* .nav-item{
        fon
    } */
</style>
{{-- <script>
    let menuLayout = {
        menuListLayout: []
    };

    document.addEventListener("DOMContentLoaded", function() {
        var menuLinks = document.querySelectorAll('.menu-link');

        menuLinks.forEach(function(link) {
            menuLayout.menuListLayout[link.getAttribute('href')] = link.getAttribute('aria-expanded');
            link.addEventListener('click', function() {
                if (menuLayout.menuListLayout[link.getAttribute('href')]) {
                    menuLayout.menuListLayout[link.getAttribute('href')] = false;
                } else {
                    link.setAttribute('aria-expanded', false);
                    // $('#sidebarDashboards2').removeClass('show');
                    setTimeout(() => {
                        $(link.getAttribute('href')).removeClass('show');
                        console.log(link.getAttribute('href'), 'logID');
                    }, 350);

                    menuLayout.menuListLayout[link.getAttribute('href')] = true;
                }
            });
        });
    });
</script> --}}
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
