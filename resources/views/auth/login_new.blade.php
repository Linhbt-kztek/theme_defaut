@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.signin')
@endsection
@section('content')

@php($config_login = getConfigForLogin(true))


<div class="background"></div>
<section class="home">
    <div class="content">

        <center><img class="logo" width="15%"
                src="{{ !empty($config_login['link_logo_login']) ? $config_login['link_logo_login'] : url(config('software.logo_light')) }}"
                alt=""></center>
        <h2> Welcome!</h2>
        <h3>{{ !empty($config_login['title_login']) ? $config_login['title_login'] : config('software.site_name') }}
        </h3>
        <pre>{{ !empty($config_login['description_login']) ? $config_login['description_login'] : config('software.pre') }}</pre>
        <div class="icon">
            <i class="fa-brands fa-instagram"></i>
            <i class="fa-brands fa-facebook"></i>
            <i class="fa-brands fa-twitter"></i>
            <i class="fa-brands fa-github"></i>
        </div>

    </div>
    <form action="{{ route('login') }}" method="POST">
        @csrf
        <div class="login">
            <h2>Đăng nhập</h2>
            <div class="input">
                <input type="text" class="input1 @error('user_name') is-invalid @enderror" placeholder="Tài khoản"
                    value="{{ old('user_name') }}" id="username" name="user_name" required>
                <i class="fa-solid fa-user"></i>
            </div>
            @error('user_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <div class="input">
                <input id="password" type="password" class="input1 @error('password') is-invalid @enderror"
                    placeholder="Mật khẩu" name="password" value="" required>
                <i class="fa-solid fa-lock"></i>
            </div>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            @error('login')
                <div class="text-center text-white mb-3 mb-1">
                    {{ $message }}
                </div>
            @enderror
            <div class="check mt-0">
                <label> <input class="showOrHide" type="checkbox" /> Hiện mật khẩu</label>
            </div>
            <div class="button">
                <button class="btn" type="submit"> Đăng nhập </button>
            </div>
            <div style="color: #fff">
                <p style="margin-bottom: 0rem;">Nếu bạn không thể đăng nhập hoặc quên mật khẩu!</p>
                <p>Vui lòng liên hệ tới người phụ trách đề được hỗ trợ.</p>
            </div>



        </div>
    </form>
</section>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    .background {
        width: 100%;
        height: 100vh;
        background-image: url("{!! $config_login['link_banner_login'] ?? '' !!}");
        background-position: center;
        background-size: cover;

    }

    .home {
        position: absolute;
        top: 50%;
        left: 50%;
        width: 75%;
        height: 75%;
        transform: translate(-50%, -50%);
        background-image: url("{!! $config_login['link_banner_login'] ?? '' !!} ");
        background-position: center;
        background-size: cover;
        display: flex;
        margin-top: 10px;
        border: 1px solid black;
        border-radius: 10px;
        border: none;
    }

    .content {
        display: flex;
        flex-direction: column;
        width: 60%;
        padding: 100px 0;
    }

    .content a {
        position: relative;
        text-decoration: none;
        color: #fff;
        font-size: 3em;
        font-weight: 700;
        top: -40px;
        left: 80px;
    }

    .content h2 {
        font-size: 3.5em;
        text-align: center;
        color: #fff;
    }

    .content h3 {
        font-size: 2em;
        text-align: center;
        color: #fff;
    }

    .content pre {
        margin-top: 20px;
        text-align: center;
        font-size: 1em;
        color: #fff;
    }

    .content .icon {
        margin-top: 20px;
        font-size: 1.5em;
        display: flex;
        justify-content: center;
    }

    .content .icon i {
        margin-left: 20px;
        color: #fff;
    }

    .login {
        width: 450px;
        position: relative;
        padding: 40% 30px;
        backdrop-filter: blur(20px);
        height: 100%;
    }

    .login h2 {
        font-size: 2em;
        text-align: center;
        margin-bottom: 20px;
        color: #fff;
    }

    .login .input {
        position: relative;
        width: 100%;
        height: 30px;
        margin-bottom: 40px
    }

    .login .input .input1 {
        font-size: 16px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: transparent;
        border: none;
        outline: none;
        border-bottom: 2px solid #fff;
        color: #fff;
        width: 100%;
        height: 100%;
    }

    ::placeholder {
        color: #fff;
        font-size: 18px;
    }

    .login .input i {
        position: relative;
        right: -370px;
        bottom: 27px;
        color: #fff;
    }

    .check {
        display: flex;
        justify-content: space-between;
        margin-bottom: 40px;
        color: #fff;
    }

    .check a {
        text-decoration: none;
        color: #fff;
    }

    .check a:hover {
        text-decoration: underline;
    }

    .login .button {
        width: 100%;
        height: 40px;
        margin-bottom: 15px;
    }

    button {
        width: 100%;
        height: 40px;
        background-color: crimson;
        border: none;
        outline: none;
        font-size: 20px;
        font-weight: 700;
        border-radius: 7px;
        color: #fff;

    }

    .btn {
        color: #939393;
    }

    .btn:hover {
        color: #fff
    }

    button:active {
        font-size: 25px;
    }

    .login .sign-up {
        display: flex;
        justify-content: center;
    }

    .login .sign-up a {
        text-decoration: none;
        color: #fff;
        font-weight: 700;
    }

    .login .sign-up p {
        color: #fff;
    }

    .sign-up a:hover {
        text-decoration: underline;
    }

    .logo {
        max-width: 200%;
        margin-bottom: 50px;
    }

    input {
        padding: 5px;
        -webkit-box-sizing: border-box;
        /* Safari/Chrome, other WebKit */
        -moz-box-sizing: border-box;
        /* Firefox, other Gecko */
        box-sizing: border-box;
        /* Opera/IE 8+ */
    }
</style>
@endsection
@section('script')
    {{--
    <script src="{{ config('kztek_config.url_public').('assets/libs/particles.js/particles.js.min.js') }}"></script>
    <script src="{{ config('kztek_config.url_public').('assets/js/pages/particles.app.js') }}"></script>
    <script src="{{ config('kztek_config.url_public').('assets/js/pages/password-addon.init.js') }}"></script> --}}
    <script src="https://kit.fontawesome.com/c9f5871d83.js" crossorigin="anonymous"></script>
    <script src="{{ url('/assets/js/jquery.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('.showOrHide').click(function (e) {
                var target = e.currentTarget;
                $(target).hasClass('show') ? hidePassword($(target)) : showPassword($(target));
            })

            function hidePassword(e) {
                e.removeClass('show').addClass('hide');
                $('#password').attr('type', 'password');
            }

            function showPassword(e) {
                e.removeClass('hide').addClass('show');
                $('#password').attr('type', 'text');
            }
        });
    </script>
@endsection