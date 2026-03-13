@extends('layouts.master-without-nav')
@section('title')
    @lang('translation.signin')
@endsection
@section('content')
    <style>

        .auth-one-bg .bg-overlay {
            background: -webkit-gradient(linear,left top,right top,from(#364574),to(#405189));
            background: linear-gradient(to right,#364574,#405189);
            opacity: .9;
        }

        .bg-overlay {
            position: absolute;
            height: 100%;
            width: 100%;
            right: 0;
            bottom: 0;
            left: 0;
            top: 0;
            opacity: .7;
            background-color: #000;
        }

        .auth-page-wrapper .footer {
            left: 0;
            background-color: transparent;
            color: #212529;
        }

        .footer {
            left: 0 !important;
            bottom: 0;
            padding: 20px calc(1.5rem / 2);
            position: absolute;
            right: 0;
            color: var(--vz-footer-color);
             height: 60px;
            background-color: transparent;
        }
    </style>
    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg"  id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-sm-5 mb-4 text-white-50">
                            {{-- <p class="mt-3 fs-15 fw-medium">{{env('TITLE_COMPANY')}}</p> --}}
                        </div>
                    </div>
                </div>
                <!-- end row -->

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">ĐĂNG NHẬP</h5>
                                    <p class="text-muted">{{env('TITLE_COMPANY')}}</p>
                                </div>
                                <div class="p-2 mt-4">
                                    <form action="{{ route('login') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Tài khoản</label>
                                            <input type="text" class="form-control @error('user_name') is-invalid @enderror" value="{{ old('user_name') }}" id="username" name="user_name" placeholder="Enter username">
                                            @error('user_name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="float-end">
                                                <a href="auth-pass-reset-basic" class="text-muted">Quên mật khẩu? </a>
                                            </div>
                                            <label class="form-label" for="password-input">Mật khẩu</label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5 @error('password') is-invalid @enderror" name="password" placeholder="Enter password" id="password-input" value="">
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
                                                @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
                                            <label class="form-check-label" for="auth-remember-check">Nhớ mật khẩu</label>
                                        </div>
                                        <br/>
                                        @error('login')
                                        <div  class="text-center bg-danger text-white mb-3 mb-1">
                                            {{ $message }}
                                        </div>
                                        @enderror

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="submit">Đăng nhập</button>
                                        </div>

                                        <div class="mt-4 text-center">
                                            <div class="signin-other-title">
                                                <h5 class="fs-13 mb-4 title">Truy cập</h5>
                                            </div>
                                        </div>



                                </div>
                                </form>
                            </div>
                        </div>
                        <!-- end card body -->
                    </div>
                    <!-- end card -->

                </div>
            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end auth page content -->

    <!-- footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <p class="mb-0 text-muted">&copy; <script>document.write(new Date().getFullYear())</script>  Bản quyền thuộc về <i class="mdi mdi-heart text-danger"></i> <a href="http://kztek.net" target="_blank">Kztek.net </a> Vietnam</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end Footer -->
    </div>
@endsection
@section('script')
    <script src="{{ config('kztek_config.url_public').('assets/libs/particles.js/particles.js.min.js') }}"></script>
    <script src="{{ config('kztek_config.url_public').('assets/js/pages/particles.app.js') }}"></script>
    <script src="{{ config('kztek_config.url_public').('assets/js/pages/password-addon.init.js') }}"></script>

@endsection
