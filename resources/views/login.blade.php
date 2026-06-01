
<form method="POST" action="{{ route('login.post') }}">
    @csrf
    <h2>ĐĂNG NHẬP</h2>

    <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required placeholder="name@example.com">
    </div>
    <div class="mb-3">
        <label class="form-label">Mật khẩu</label>
        <input type="password" name="password" class="form-control" required placeholder="*********">
    </div>

    <div class="d-grid mt-4">
        <button type="checkbox" name='remember' class="btn btn-primary fw-bold">ĐĂNG NHẬP</button>
    </div>

    <div class="text-center mt-3">
        <small>Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a></small>
    </div>
</form>