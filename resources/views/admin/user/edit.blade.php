@extends('layouts.master')
@section('title')
    @lang('translation.dashboards')
@endsection
@section('content')
    @include('components.breadcrumb')
    <div class="row">
        <div class="col">
            <form action="{{ !empty($user) ? url('admin/user_update/' . @$user['id']) : url('admin/user_create') }}"
                enctype="multipart/form-data" method="post">
                @csrf
                @if (!empty($user))
                    {{ method_field('PUT') }}
                @endif


                <input name="id_user" type="hidden" value="{{ @$user['id'] }}" />

                <input name="file_link_image_old" type="hidden" value="{{ @$user['user_avatar'] }}" />

                <div class="card">
                    <div class="card-body border border-dashed border-start-0 border-end-0 row">

                        <!-- bên trái -->
                        <div class="col-6">
                            <div class="row mb-3">

                                <div class="col-6">
                                    <!-- tên -->
                                    <div class="form_input">
                                        <label class="form-label">Tên <span
                                                style="color:red;font-size:15px;font-weight:bold">*</span></label>
                                        <input class="form-control" name="name" type="text"
                                            value="{{ old('name', isset($user['name']) ? $user['name'] : '') }}"
                                            placeholder="">
                                        @if ($errors->has('name'))
                                            <div class="bg-danger text-white text-center py-1">
                                                <span>{{ $errors->first('name') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <!-- tên -->
                                </div>

                                <div class="col-6">
                                    <!-- loại tài khoản -->
                                    <div class="form_input">
                                        <label class="form-label">Chọn loại tài khoản<span
                                                style="color:red;font-size:15px;font-weight:bold">*</span></label>

                                        <!-- nếu là update: ko cho sửa loại tài khoản -->
                                        {{--                                        @if (!empty($on_user)) --}}
                                        {{--                                            <input type="hidden" name="type" value="{{ @$user['type'] }}"> --}}
                                        {{--                                            <input type="hidden" name="on_user" value="1"> --}}
                                        {{--                                        @endif --}}
                                        <!-- nếu là update: ko cho sửa loại tài khoản -->

                                        <!-- nếu là update: ko cho sửa loại tài khoản -->
                                        @if (isset($user['id']) and $user['id'] != '')
                                            <input type="hidden" name="type" value="{{ @$user['type'] }}">
                                            <select onchange="showRoleBox()" class="form-select" name="type" disabled>
                                                <option value="1"
                                                    {{ old('type', @$user['type']) == 1 ? 'selected' : '' }}>
                                                    Tài
                                                    khoản
                                                    web
                                                </option>
                                                <option value="2"
                                                    {{ old('type', @$user['type']) == 2 ? 'selected' : '' }}>
                                                    Tài
                                                    khoản api
                                                </option>
                                            </select>
                                        @else
                                            <input type="hidden" name="type" value="1">
                                            <select onchange="showRoleBox()" class="form-select" name="type" disabled>
                                                <option value="1" selected>
                                                    Tài
                                                    khoản
                                                    web
                                                </option>
                                                <option value="2"
                                                    {{ old('type', @$user['type']) == 2 ? 'selected' : '' }}>
                                                    Tài
                                                    khoản api
                                                </option>
                                            </select>
                                        @endif


                                        @if ($errors->has('type'))
                                            <div class="bg-danger text-white text-center py-1">
                                                <span>{{ $errors->first('type') }}</span>
                                            </div>
                                        @endif

                                    </div>
                                    <!-- loại tài khoản -->
                                </div>

                                <div class="col-6">
                                    <!-- phone -->
                                    <div class="form_input">
                                        <label class="form-label">Điện thoại </label>
                                        <input class="form-control" name="phone" type="number"
                                            value="{{ old('phone', isset($user['phone']) ? $user['phone'] : '') }}"
                                            placeholder="">
                                        @if ($errors->has('phone'))
                                            <div class="bg-danger text-white text-center py-1">
                                                <span>{{ $errors->first('phone') }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <!-- phone -->
                                </div>

                                <div class="col-6 form_input" id="div_role">
                                    <label class="form-label">Chọn nhóm quyền<span
                                            style="color:red;font-size:15px;font-weight:bold">*</span></label>

                                    <!-- nếu là update: ko cho sửa loại tài khoản -->
                                    @if (isset($user['id']) and $user['id'] != '')
                                        <input type="hidden" name="role_id" value="{{ @$role_id }}">
                                        <select class="form-select" id="role_id" name="role_id" disabled>
                                            <option disabled value=''>Lựa chọn</option>
                                            @foreach ($roles as $item)
                                                <option {{ old('role_id') == $item->id ? 'selected' : '' }}
                                                    @if (!empty($user)) {{ @$user->getRoleNames()->first() == $item->name ? 'selected' : '' }} @endif
                                                    value='{{ $item->id }}'>
                                                    {{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select class="form-select" id="role_id" name="role_id">
                                            <option disabled value=''>Lựa chọn</option>
                                            @foreach ($roles as $item)
                                                <option {{ old('role_id') == $item->id ? 'selected' : '' }}
                                                    @if (!empty($user)) {{ @$user->getRoleNames()->first() == $item->name ? 'selected' : '' }} @endif
                                                    value='{{ $item->id }}'>
                                                    {{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif

                                    {{--                                        <select class="form-select" id="role_id" name="role_id" --}}
                                    {{--                                        {{ !empty($on_user) ? 'disabled' : '' }}> --}}
                                    {{--                                        <option disabled value=''>Lựa chọn</option> --}}
                                    {{--                                        @foreach ($roles as $item) --}}
                                    {{--                                            <option {{ old('role_id') == $item->id ? 'selected' : '' }} --}}
                                    {{--                                                    @if (!empty($user)) --}}
                                    {{--                                                        {{ @$user->getRoleNames()->first() == $item->name ? 'selected' : '' }} --}}
                                    {{--                                                    @endif --}}
                                    {{--                                                    value='{{ $item->id }}'> --}}
                                    {{--                                                {{ $item->name }}</option> --}}
                                    {{--                                        @endforeach --}}
                                    {{--                                    </select> --}}
                                    @if ($errors->has('role_id'))
                                        <div class="bg-danger text-white text-center py-1">
                                            <span>{{ $errors->first('role_id') }}</span>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-6">


                                    <div>
                                        <label class="form-label" for="gen-info-username-input">Ảnh</label>
                                        <?php if (isset($user['url_user_avatar'])) { ?>
                                        &nbsp;<br />
                                        <img src="{{ @$user['url_user_avatar'] }}"
                                            style="width:200px" />
                                        <?php } ?>
                                    </div>

                                    <div>
                                        <?php if (isset($user['url_user_avatar'])) { ?>
                                        <br />
                                        <label class="form-label" for="gen-info-username-input">Thay ảnh khác</label>
                                        <?php } ?>

                                        <input type="file" class="form-control" name="file_link_image" id="photo" />
                                        @if ($errors->has('file_link_image'))
                                            <div class="bg-danger text-white text-center py-1">
                                                <span>{{ $errors->first('file_link_image') }}</span>
                                            </div>
                                        @endif
                                        <br>
                                        <div class="holder">
                                            <img id="imgPreview" src="#" alt="pic" style="width:200px" />
                                        </div>
                                    </div>
                                </div>



                            </div>
                        </div>
                        <!-- bên trái -->


                        <!-- bên phải -->
                        <div class="col-6">
                            {{--                <div class="col-6" style="background-color:#F3F3F9"> --}}
                            <div class="row justify-content-center">
                                <div class="col-md-8 col-lg-6 col-xl-5"
                                    style=" box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);">
                                    <div class="card mt-4">

                                        <div class="card-body p-4">
                                            <div class="text-center mt-2">
                                                <h5 class="text-primary">Tài khoản đăng nhập</h5>
                                                {{--                                        <p class="text-muted">Sign in to continue to Velzon.</p> --}}
                                            </div>
                                            <div class="p-2 mt-4">
                                                {{--                                        <form action="index"> --}}

                                                <div class="mb-3">
                                                    <label for="username" class="form-label">Username <span
                                                            style='color:red;font-size:15px;font-weight:bold'>*</span></label>
                                                    <?php
                                                    //edit
                                                    if (isset($data->id) and $data->id != "") {
                                                        ?>
                                                    : <span
                                                        style="color: red;font-weight: bold">{{ $user->user_name }}</span>
                                                    <input type="hidden" name="user_name"
                                                        value="{{ $user->user_name }}">
                                                    <?php
                                                    } else {
                                                        //create
                                                        ?>
                                                    <input type="text" class="form-control" id="user_name"
                                                        name="user_name" placeholder="Enter username"
                                                        value="{{ old('user_name', isset($user->user_name) ? $user->user_name : '') }}">
                                                    <?php

                                                    }
                                                    ?>
                                                    @if ($errors->has('user_name'))
                                                        <div class="bg-danger text-white text-center py-1">
                                                            <span>{{ $errors->first('user_name') }}</span>
                                                        </div>
                                                    @endif


                                                </div>

                                                <!-- mật khẩu -->
                                                <div class="mb-3">

                                                    <label class="form-label" for="password-input">

                                                        <?php
                                                        
                                                        if (isset($user->id) and $user->id != '') {
                                                            echo 'Mật khẩu mới';
                                                        } else {
                                                            echo "Mật khẩu <span style='color:red;font-size:15px;font-weight:bold'>*</span>";
                                                        }
                                                        
                                                        ?></label>

                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                        <input type="password" class="form-control pe-5"
                                                            placeholder="Enter password" id="password" name="password">
                                                        <button
                                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted"
                                                            type="button" id="password-addon" onclick="showPassword()"><i class="ri-eye-off-fill align-middle"></i>
</button>
                                                    </div>
                                                    @if ($errors->has('password'))
                                                        <div class="bg-danger text-white text-center py-1">
                                                            <span>{{ $errors->first('password') }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <!-- mật khẩu -->


                                                {{--                                            <div class="form-check"> --}}
                                                {{--                                                <input class="form-check-input" type="checkbox" value="" --}}
                                                {{--                                                       id="auth-remember-check"> --}}
                                                {{--                                                <label class="form-check-label" for="auth-remember-check">Remember --}}
                                                {{--                                                    me</label> --}}
                                                {{--                                            </div> --}}

                                                {{--                                            <div class="mt-4"> --}}
                                                {{--                                                <button class="btn btn-success w-100" type="submit">Sign In</button> --}}
                                                {{--                                            </div> --}}

                                                {{--                                            <div class="mt-4 text-center"> --}}
                                                {{--                                                <div class="signin-other-title"> --}}
                                                {{--                                                    <h5 class="fs-13 mb-4 title">Sign In with</h5> --}}
                                                {{--                                                </div> --}}
                                                {{--                                                <div> --}}
                                                {{--                                                    <button type="button" --}}
                                                {{--                                                            class="btn btn-primary btn-icon waves-effect waves-light"><i --}}
                                                {{--                                                            class="ri-facebook-fill fs-16"></i></button> --}}
                                                {{--                                                    <button type="button" --}}
                                                {{--                                                            class="btn btn-danger btn-icon waves-effect waves-light"><i --}}
                                                {{--                                                            class="ri-google-fill fs-16"></i></button> --}}
                                                {{--                                                    <button type="button" --}}
                                                {{--                                                            class="btn btn-dark btn-icon waves-effect waves-light"><i --}}
                                                {{--                                                            class="ri-github-fill fs-16"></i></button> --}}
                                                {{--                                                    <button type="button" --}}
                                                {{--                                                            class="btn btn-info btn-icon waves-effect waves-light"><i --}}
                                                {{--                                                            class="ri-twitter-fill fs-16"></i></button> --}}
                                                {{--                                                </div> --}}
                                                {{--                                            </div> --}}
                                                {{--                                        </form> --}}
                                            </div>
                                        </div>
                                        <!-- end card body -->
                                    </div>
                                    <!-- end card -->

                                    {{--                            <div class="mt-4 text-center"> --}}
                                    {{--                                <p class="mb-0">Don't have an account ? <a href="auth-signup-basic" --}}
                                    {{--                                                                           class="fw-semibold text-primary text-decoration-underline"> --}}
                                    {{--                                        Signup </a></p> --}}
                                    {{--                            </div> --}}

                                </div>
                            </div>
                        </div>
                        <!-- bên phải -->
                    </div>

                    <div class="card-footer">
                        <div class="col-sm-auto">

                            <!-- nếu từ menu bên trên -->
                            @if (null !== request('menu') and request('menu') == '1')
                                <input type="hidden" name="from_menu" value="1" />
                            @else
                                <!-- nếu từ menu bên phải-->
                                <a class="btn btn-danger waves-effect waves-light" href="{{ url('admin/user') }}">Quay
                                    lại</a>
                            @endif

                            <button class="btn  btn-primary" type="submit">
                                {{ !empty($user) ? 'Cập nhật' : 'Xác nhận' }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
@endsection
<style>
    .image-container {
        position: relative;
        display: inline-block;
    }

    .button-inside-image {
        position: absolute !important;
        top: 0;
        right: 0;
        z-index: 1;
    }

    .form_input {
        margin-bottom: 20px;
    }

    .navbar-brand-box {
        margin-top: 10px;
        margin-bottom: 16px;
    }
</style>
@section('script')
    <script>
        @if (!empty(session('alert-error')))
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: "{{ session('alert-error') }}",
                showConfirmButton: false,
                timer: 1500,
                showCloseButton: false
            });
        @endif

        @if (!empty(session('alert-success')))
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: "{{ session('alert-success') }}",
                showConfirmButton: false,
                timer: 1500,
                showCloseButton: false
            });
        @endif
    </script>

    <!-- ====================================================================xử lý ảnh ==================================================================== -->

    <script>
        function delete_image() {
            $(".image-container").remove();
            $("#delete_image_id").append('<input name="delete_image" type="hidden" value="1">');
        }

        //ẩn hiện vai trò theo type là web hay api
        function showRoleBox() {
            var key = $('select[name=type]').val();
            // console.log($('#type').val());
            if (key != 1) {
                //=2 là api => ẩn vai trò
                $('#div_role').addClass('d-none');
                // $('#div_location').removeClass('d-none');
            } else {
                //=1 là web hiện vai trò lên
                $('#div_role').removeClass('d-none');
                // $('#div_location').addClass('d-none');

            }
        }

function showPassword() {
    var passwordInput = document.getElementById('password');
    var icon = document.querySelector('#password-addon i');

    if (passwordInput.type === 'password') {
        // Đang ẩn → sắp hiện → đổi icon thành "mắt mở"
        passwordInput.type = 'text';
        icon.classList.remove('ri-eye-fill');
        icon.classList.add('ri-eye-off-fill');
    } else {
        // Đang hiện → sắp ẩn → đổi icon thành "mắt gạch"
        passwordInput.type = 'password';
        icon.classList.remove('ri-eye-off-fill');
        icon.classList.add('ri-eye-fill');
    }
}


        $(document).ready(() => {
            //div hiển thị ảnh
            $("div.holder").hide();
            showRoleBox();

            //chọn ảnh
            $("#photo").change(function() {
                const file = this.files[0];
                if (file) {
                    $("div.holder").show();
                    let reader = new FileReader();
                    reader.onload = function(event) {
                        $("#imgPreview").attr("src", event.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>

    <!-- ====================================================================xử lý ảnh ==================================================================== -->
@endsection
