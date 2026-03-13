<!DOCTYPE html>
<html>

<head>

</head>

<body>
    <div>
        <h4>Phòng quản trị và vận hành xin thông báo!</h4>
        <b>Cán bộ:</b> <span>{{ $staff->name }}</span>
        <br>
        <b>Đơn vị công tác:</b> <span>{{ $staff->agency->name }}</span>
        <br>
        <b>Nội dung thông báo:</b> Thông báo tài khoản thành viên cho người dùng thuộc hệ thống "Phòng họp thông minh".
        <br>
        <b>Tài khoản:</b> <span>{{ $staff->user->user_name }}</span>
        <br>
        <b>Mật khẩu:</b> 123456
        <br>
        <i><b>Lưu ý:</b> Tài khoản vào mật khẩu chỉ bao gồm ký tự, không chưa dấu cách! Cán bộ nhân viên nên thay đổi
            mật khẩu sau khi đăng nhập lần đầu!</i>
        <br>
        <p>Link tải ứng dụng thành viên : <a href="#">link tải android</a> hoặc <a href="#">link tải IOS</a>.</p>
        <p>Mọi thông tin liên quan cần giải đáp, vui lòng liên hệ phòng <b>Quản trị & Vận hành</b>.</p>
        <b>Trận trọng!</b>
    </div>
</body>

</html>