@php
    $auth = Auth::user();
    if (!empty($auth)) {
        $auth_name = $auth->name;
        $auth_avt = $auth->url_user_avatar ?? url('assets/images/user-default.jpg');
    } else {
        $auth_name = $auth->name;
        $auth_avt = url('assets/images/user-default.jpg');
    }
    $config = getConfigForLogin();
    // dd($config);
@endphp
<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                    id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <!-- Dark Logo-->
                <div class="d-flex align-items-center">
                    <a href="{{ route('home') }}" class="d-flex align-items-center">
                        <span class="logo-lg">
                            <img src="{{ $config['link_logo_system'] ?? url(config('software.logo_dark')) }}" alt=""
                                style="max-width: 150px; max-height: 50px; object-fit:contain">
                        </span>
                    </a>
                </div>

                <div class="navbar-brand-box horizontal-logo">
                    <a class="logo logo-dark" href="{{ route('home') }}">
                        <span class="logo-sm">
                            <img alt="" height="22" src="{{ url('assets/images/logo-sm.png') }}">
                        </span>
                        <span class="logo-lg">
                            <img alt="" height="17" src="{{ url('assets/images/logo-dark.png') }}">
                        </span>
                    </a>
                    <a class="logo logo-light" href="{{ route('home') }}">
                        <span class="logo-sm">
                            <img alt="" height="22" src="{{ url('assets/images/logo-sm.png') }}">
                        </span>
                        <span class="logo-lg">
                            <img alt="" height="17" src="{{ url('assets/images/logo-light.png') }}">
                        </span>
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="ms-sm-3 header-item topbar-user">
                    <div class="topbar-user dropdown">
                        <span class="d-flex align-items-center">
                            <img alt="Header Avatar" class="rounded-circle header-profile-user" src="{{ $auth_avt }}">
                            <span class="text-start ms-xl-2">
                                <span
                                    class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ $auth_name }}</span>
                            </span>
                        </span>
                        <div class="dropdown-menu dropdown-content c-dropdown">
                            <a class="dropdown-item c-dropdown-item"
                                href="{{ route('user_edit', Auth::user()->id) }}?menu=1">
                                <i class="mdi mdi-account-edit"></i>
                                <span key="t-logout">Cập nhật tài khoản</span>
                            </a>

                            <a class="dropdown-item c-dropdown-item" href="javascript:void();"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="bx bx-power-off font-size-16 align-middle me-1"></i>
                                <span key="t-logout">Thoát</span>
                            </a>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" id="logout-form" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<style>
    .header {
        background-color: #333;
        color: #fff;
        padding: 10px;
    }

    .topbar-user {
        position: relative;
        cursor: pointer;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        top: 300px;
        right: -10px;
        width: 165px;
    }

    .topbar-user:hover .dropdown-content {
        display: block;
    }

    .topbar-user .dropdown-menu {
        top: 45px !important;
    }
</style>
<style>
    .logout_class {
        margin-left: 2px !important;
        margin-left: 2px !important;
        border-color: #fff0;
        color: #cbdee7;
        /* background-color: #3b4655; */

    }

    .logout_class:hover {
        border-color: #fff0;
        background-color: #4e5d71;
        color: #cbdee7;
    }

    [data-layout=vertical][data-sidebar-size=sm-hover] .footer,
    [data-layout=vertical][data-sidebar-size=sm] .footer {
        left: 0px;
        transition: 0.7s ease;

    }

    .footer {
        transition: 0.7s ease;
    }
</style>
<script>
    document.querySelectorAll(".nav-link[data-toggle='custom-collapse']").forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            const isOpen = target.classList.contains("show");

            // Đóng tất cả menu khác
            document.querySelectorAll(".menu-dropdown").forEach(m => m.classList.remove("show"));
            document.querySelectorAll(".nav-link[data-toggle='custom-collapse']").forEach(l => {
                l.setAttribute("aria-expanded", "false");
            });

            // Toggle menu hiện tại
            if (!isOpen) {
                target.classList.add("show");
                this.setAttribute("aria-expanded", "true");
            }
        });
    });


    document.addEventListener('DOMContentLoaded', function () {
        // const overlay = document.querySelector('.vertical-overlay');
        const sidebar = document.querySelector('.app-menu');
        const openBtn = document.querySelector('#topnav-hamburger-icon');
        const closeBtn = document.getElementById('sidebarCloseBtn');

        if (!sidebar) return;

        // Mở sidebar
        openBtn?.addEventListener('click', function () {
            document.documentElement.setAttribute('data-sidebar-size', 'lg'); // mở rộng sidebar
            sidebar.classList.remove('collapsed'); // mở sidebar
            // overlay.classList.add('show'); // hiển thị overlay
        });

        // Đóng sidebar
        closeBtn?.addEventListener('click', function () {

            document.documentElement.setAttribute('data-sidebar-size', 'sm'); // thu nhỏ sidebar
            sidebar.classList.add('collapsed'); // đóng sidebar
            // overlay.classList.remove('show'); // ẩn overlay
        });

        document.addEventListener('click', function (event) {
            const isClickInside = sidebar.contains(event.target);
            const isClickToggle = openBtn?.contains(event.target);

            // Nếu sự kiện click nằm ngoài sidebar
            if (!isClickInside && !isClickToggle && !sidebar.classList.contains('collapsed')) {
                document.documentElement.setAttribute('data-sidebar-size', 'sm');
                sidebar.classList.add('collapsed');
            }
        });


    });

    function copyToClipboard() {
        const textArea = document.createElement("textarea");
        textArea.value = main.token;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand("Copy");
        } catch (err) { }
        document.body.removeChild(textArea);
    }


</script>