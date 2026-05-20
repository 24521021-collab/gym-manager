<!DOCTYPE html>
<html>
<head>
    <style>
        .container { font-family: Arial, sans-serif; padding: 20px; line-height: 1.6; }
        .password-box { background: #f4f4f4; padding: 15px; font-size: 20px; font-weight: bold; color: #d9534f; display: inline-block; border-radius: 5px; margin: 10px 0; }
        .footer { font-size: 12px; color: #888; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Xin chào, {{ $name }}!</h2>
        <p>Chúng tôi đã nhận được yêu cầu cấp lại mật khẩu cho tài khoản của bạn.</p>
        <p>Đây là mật khẩu mặc định của bạn:</p>
        <div class="password-box">{{ $password }}</div>
        <p>Vui lòng đăng nhập và <strong>thay đổi mật khẩu ngay lập tức</strong> tại Trang cá nhân để bảo mật tài khoản.</p>
        <p>Trân trọng,<br>Đội ngũ quản trị hệ thống Gym.</p>
        <div class="footer">Nếu bạn không yêu cầu cấp mật khẩu, vui lòng bỏ qua email này.</div>
    </div>
</body>
</html>
