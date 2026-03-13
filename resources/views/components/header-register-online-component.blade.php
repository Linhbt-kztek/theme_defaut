<link rel="stylesheet" href="{{ url('template/css/header.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400..700&display=swap" rel="stylesheet">
<style>
    .title_logo {
        font-family: "Dancing Script", cursive;
        font-optical-sizing: auto;
        font-weight: <weight>;
        font-style: normal;
        color: #e9bf72;
    }

    .home-image {
        vertical-align: sub;
    }
</style>
<header id="header">
    <div class="container">
        <div class="header">
            <a href="{{ route('register_online.index') }}" class="header__logo">
                <div class="txt" style="font-size: 20px">
                    <img class="home-image" src="{{ url('images/icon/home.png') }}" alt=""
                        style="width: 35px" />
                    <strong style="color: #00749b;">Trang chủ</strong>
                </div>
            </a>

            <div class="header__main">
                <div class="header__nav d-none d-xl-flex"><a href="{{ route('register_online.index') }}"
                        class="header__logo">
                        @if (!empty($config_web['link_logo']))
                            <a href="{{ route('register_online.index') }}" class="header__logo">
                                <h1 class="m-0 p-0">
                                    <img src="{{ $config_web['link_logo'] }}" alt="" style="width:2.5em;" />
                                </h1> 
                            </a>
                        @endif
                </div>

                <div class="header__action">
                    <form class="header__action--search">
                        <input type="text" id="searchOrderCode" placeholder="Nhập mã đơn hàng để tra cứu.." />
                        <button type="button" class="btn-search" onclick="search()">
                            <i class="ri-search-line" style="color: #fff"></i>
                        </button>
                    </form>
                    <div class="header__action--user">
                        <a href="#services-order-id">
                            <div class="cart">
                                <div class="cart-icon"><img src="{{ url('template/images/header-icon-cart.png') }}"
                                        alt=""></div>
                                <span id="cart-count">0</span>
                            </div>
                        </a>
                        <div class="users__logged">
                            @if (!auth()->user() || auth()->user()->is_classification_customer != '1')
                                <button type="button" class="btn btn-light btn-sm"
                                    onclick="$('#loginModal').modal('show')">
                                    <b style="font-size: 12px;">Đăng nhập</b>
                                </button>
                            @else
                                <button class="avatar" href="#">
                                    <img class="rounded-circle" width="23" height="23"
                                        src="{{ auth()->user()->url_user_avatar ?? url('images/default-user.jpg') }}" alt="" />
                                </button>

                                <div class="users__logged--actions">
                                    <div class="author">
                                        <img width="35" height="35" class="rounded-circle"
                                            src="{{ auth()->user()->url_user_avatar ?? url('images/default-user.jpg') }}" alt="" />
                                        {{ auth()->user()->name }}
                                    </div>
                                    <div class="list">
                                        <a href="{{ route('user.profile') }}">Thông tin tài khoản</a>
                                        <a href="{{ route('register_online.orderHistory') }}">Lịch sử giao dịch</a>
                                        <a href="{{ route('user.logout') }}">Đăng xuất</a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</header>
<div id="loginModal" class="modal fade bs-example-modal-lg" tabindex="-1" aria-labelledby="myLargeModalLabel"
    style="display: none;" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="form-big-group">
                <div class="layout-two-cols">
                    <form action="{{ route('user.processLogin') }}" method="post">
                        @csrf
                        <div class="box-content">
                            <div class="modal-header" style="border-bottom: none !important;">
                                <h4 class="modal-title" id="myModalLabel" style="color: rgb(43, 43, 43)">Đăng nhập</h4>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="wrap-content">
                                <div class="form-row">
                                    <div class="col-lg-12 col-12">
                                        <div class="form-group">
                                            <label for="">Tài khoản:</label>
                                            <input required name="user_name" id="user_name" type="text"
                                                class="form-control">
                                            <span class="error__note"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-12">
                                        <div class="form-group">
                                            <label for="">Mật khẩu:</label>
                                            <input required name="password" type="password" id="password"
                                                class="form-control">
                                            <span class="error__note"></span>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-12">
                                        <a href="#" style="float: right">Quên mật khẩu!</a>
                                    </div>

                                    <div class="col-lg-12 col-12">
                                        <button type="button" class="btn" id="btnLogin" onclick="login()"
                                            style="width: 100%; background-color: #a70000;color: #fff;">Đăng nhập
                                        </button>
                                    </div>
                                </div>
                                {{-- <div class="col-lg-12 col-12">
                                    <a href="#" style="float: right">Quên mật khẩu!</a>
                                </div>
                                <div class="col-lg-12 col-12">
                                    <button type="submit" class="btn" id="btnLogin" onclick="login()"
                                        style="width: 100%; background-color: #a70000;color: #fff;">Đăng nhập
                                    </button>
                                </div> --}}
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    headerDoom = {
        urlSearch: "{{ route('register_online.orderSearch') }}"
    }

    @if (session('error'))
        setTimeout(() => {
            registerOnlineV2.alert_main("{{ session('error') }}", "error", 'center')
        }, 1000)
    @endif

    function login() {

        $('.error_message').remove()

        let account = {
            user_name: $('#user_name').val(),
            password: $('#password').val(),
        }

        if (account.user_name == '' || account.user_name === undefined) {
            $('#user_name').parent().append(
                '<span class="text-danger error_message" style="font-size: 13px">Vui lòng nhập trường này!</span>')
            $('#btnLogin').attr('type', 'button')

        }

        if (account.password == '' || account.user_name === undefined) {
            $('#password').parent().append(
                '<span class="text-danger error_message" style="font-size: 13px">Vui lòng nhập trường này!</span>')
            $('#btnLogin').attr('type', 'button')

        }
        if (account.user_name && account.password) {
            $('#btnLogin').attr('type', 'submit')
        }
    }
</script>

<script src="{{ url('/template/js/pages/header.js') }}"></script>
