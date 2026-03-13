{{--<!DOCTYPE html>--}}
{{--<html>--}}
{{--<head>--}}
{{--    <meta http-equiv="refresh" content="5;url={{ route('login') }}">--}}
{{--</head>--}}
{{--<body>--}}
{{--<h1>Trang bạn tìm kiếm không tồn tại. Bạn sẽ được chuyển hướng về trang đăng nhập trong vòng 5 giây.</h1>--}}
{{--</body>--}}
{{--</html>--}}

{{--<!DOCTYPE html>--}}
{{--<html>--}}
{{--<head>--}}
{{--</head>--}}
{{--<body>--}}
{{--<h1>Trang bạn tìm kiếm không tồn tại. Bạn sẽ được chuyển hướng về trang đăng nhập trong vòng 3 giây.</h1>--}}

<script>
    // Sử dụng setTimeout để chờ đợi 5 giây
   // setTimeout(function () {

            window.location.href = "{{ route('login') }}";

   // }, 3000); // 5000 milliseconds = 5 seconds
</script>
{{--</body>--}}
{{--</html>--}}
