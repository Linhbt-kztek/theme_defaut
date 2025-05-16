
<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.signin'); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
    <div class="container-fluit auth-bg">
        <div class=" pt-5 ">
            <!-- auth page bg -->
            <!-- auth page content -->
            <div class="auth-page-content">
                <div class="container w-75">
                    <!-- end row -->
                    <div class="row w-100 d-flex justify-content-center ">
                        <div class="w-100 mt-4">
                            <div class="card-body p-5 grid">
                                <div class="d-flex">
                                    <div class=" border-end w-50">
                                        <div
                                            class="col-lg-12 h-100 d-flex flex-column  justify-content-between text-center">
                                            <div class="d-flex justify-content-center mt-2">
                                                <img class="w-fit" src="<?php echo e(URL::asset('assets/images/logo-dark.png')); ?>"
                                                    class="rounded me-2" alt="..." height="80">
                                            </div>
                                            <div class="mt-4">
                                                <p class="web-name mb-0 ">Kztek HRM</p>
                                                <p class="text-blue">Chào mừng đến với hệ thống quản lý nhân sự!</p>
                                            </div>

                                            <div class=" mt-5 text-xs">
                                                <p class="mb-0 text-muted">Phiên bản &copy;
                                                    <script>document.write(new Date().getFullYear())</script>
                                                <p class="mb-0 text-muted">Powered by Kztek Software</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2 ps-5 w-50 grid">
                                        <h5 class="text-4xl fw-bold text-left "><span
                                                class="text-orange me-1">Welcome</span><span class="text-blue">Back!</span>
                                        </h5>
                                        <div class="py-2 mt-4 col">
                                            <p class="text-left text-lg text-uppercase text-blue fw-bold ">Đăng nhập</p>

                                            <form action="<?php echo e(route('login')); ?>" method="POST">
                                                <?php echo csrf_field(); ?>
                                                <div class="mb-3 position-relative">
                                                    <input type="text"
                                                        class="form-control-x <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                        value="<?php echo e(old('email', 'admin@themesbrand.com')); ?>"
                                                        id="username" name="email" placeholder="Tên đăng nhập">
                                                     
                                                    <i class="ri-user-fill position-absolute top-50 start-0 translate-middle-y ps-3 icon-scale "></i>
                                                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                        <span class="invalid-feedback" role="alert">
                                                            <strong><?php echo e($message); ?></strong>
                                                        </span>
                                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                </div>

                                                <div class="mb-3">
                                                    <div class="position-relative auth-pass-inputgroup mb-3">
                                                            <input type="password"
                                                                class="form-control-x ps-5 pe-5 <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                                                name="password" placeholder="Mật khẩu" id="password-input"
                                                                value="123456">
                                                       
                                                          <i class="ri-key-2-fill position-absolute top-50 start-0 translate-middle-y ps-3 icon-scale "></i>

                                                        <button
                                                            class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted"
                                                            type="button" id="password-addon"><i
                                                                class="ri-eye-fill align-middle icon-scale "></i></button>
                                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                                            <span class="invalid-feedback" role="alert">
                                                                <strong><?php echo e($message); ?></strong>
                                                            </span>
                                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                                    </div>
                                                </div>

                                                <div class="form-check p-0">
                                                    <input class="check-input" type="checkbox" value=""
                                                        id="auth-remember-check">
                                                    <label class="form-check-label" for="auth-remember-check">Lưu mật
                                                        khẩu</label>
                                                </div>

                                                <div class="mt-4">
                                                    <button class="btn btn-orange w-100" type="submit">Đăng nhập</button>
                                                </div>
                                                <div class="text-center text-xs mt-3 text-blue">
                                                    <p class="m-0">Nếu bạn không thể đăng nhập hoặc quên mật khẩu,</p>
                                                    <p class="m-0">Vui lòng liên hệ với người phụ trách để được hỗ trợ</p>
                                                </div>
                                            </form>
                                        </div>
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
        </div>
    </div>


<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <link rel="stylesheet" href="<?php echo e(url('assets/css/login.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(url('assets/css/custom.css')); ?>">

    <script src="<?php echo e(URL::asset('assets/libs/particles.js/particles.js.min.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/js/pages/particles.app.js')); ?>"></script>
    <script src="<?php echo e(URL::asset('assets/js/pages/password-addon.init.js')); ?>"></script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master-without-nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon\www\theme_defaut\resources\views/auth/login.blade.php ENDPATH**/ ?>