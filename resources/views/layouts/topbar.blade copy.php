<header id="page-topbar">
    <div class="layout-width">
        <div class="navbar-header">
            <div class="d-flex">
                <!-- LOGO -->
                <div class="ms-1 header-item d-none d-sm-flex">
                    <div class="form-check form-switch form-switch-success" style="margin-bottom: 10px;">
                        <input type="checkbox" class="form-check-input" value=""
                            onclick="configTicket()" id="customSwitchsizesm30040"
                            {{ session('show_customer_classification') ? 'checked' : '' }}>
                        <label class="form-check-label" for="customSwitchsizesm30040"><b style="color: #fff">Hiển thị giá vé khi in</b></label>
                    </div>
                </div>
                <div class="navbar-brand-box horizontal-logo">
                    <a class="logo logo-dark" href="{{ route('home') }}">
                        <span class="logo-sm">
                            <img alt="" height="22"
                                src="{{ config('kztek_config.url_public') . 'assets/images/logo-sm.png' }}">
                        </span>
                        <span class="logo-lg">
                            <img alt="" height="17"
                                src="{{ config('kztek_config.url_public') . 'assets/images/logo-dark.png' }}">
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
                            <img alt="Header Avatar" class="rounded-circle header-profile-user"
                                src="{{ url('assets/images/user-default.jpg') }}">
                            <span class="text-start ms-xl-2">
                                <span
                                    class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{ Auth::user()->name }}</span>

                            </span>
                        </span>
                        <div class="dropdown-content">
                            <a class="dropdown-item " href="{{ route('user_edit', Auth::user()->id) }}?menu=1">
                                <i class="ri-edit-box-line"></i>
                                <span key="t-logout">Cập nhật tài khoản</span>
                            </a>

                            <a class="dropdown-item " href="javascript:void();"
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
        top: 51px;
        right: -20px;
        width: 165px;
    }

    .topbar-user:hover .dropdown-content {
        display: block;
    }

    .dropdown-item {
        padding: 10px;
        text-decoration: none;
        color: #333;
        display: block;
    }

    .dropdown-item:hover {
        background-color: #ddd;
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
</style>
<script>
    function copyToClipboard() {
        const textArea = document.createElement("textarea");
        textArea.value = main.token;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand("Copy");
        } catch (err) {}
        document.body.removeChild(textArea);
    }

    function configTicket() {
        $.ajax({
            url: "{{ route('home.configTicket') }}",
            type: 'get',
            success: function(result) {
                console.log(result);
            },
        })
    }
</script>
