<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymPro - Đánh thức sức mạnh trong bạn</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    
    <style>
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            height: 80vh;
            display: flex;
            align-items: center;
            color: white;
        }
        .card-gym:hover {
            transform: translateY(-10px);
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .avatar-img {
            width: 35px;
            height: 35px;
            object-fit: cover;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow border-bottom border-secondary">
        <div class="container">
            <a class="navbar-brand fw-bold text-warning" href="#">GYMPRO</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link active" href="#">Trang chủ</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pricing">Gói tập</a></li>
                    <li class="nav-item"><a class="nav-link" href="{{ route('classes.index') }}">Lớp học</a></li> 
                </ul>
                <div class="mt-3">

        </div>
                <div class="navbar-nav ms-auto d-flex align-items-center">
                    @guest
                        <button class="btn btn-outline-light me-2" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</button>
                        <button class="btn btn-warning fw-bold" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng ký</button>
                    @else
                    <div class="d-flex align-items-center">
                        <a href="{{ route('products.index') }}" class="btn btn-link text-white position-relative me-3" style="text-decoration: none;">
                            <i class="fa fa-shopping-cart" style="font-size: 20px;"></i>
                            @if(session('cart') && count(session('cart')) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 10px;">
                            {{ count(session('cart')) }}
                        </span>
                    </div>
                     @endif
                            </a>
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=random&color=fff" 
                                     class="rounded-circle avatar-img me-2" alt="user">
                                <span class="text-white fw-medium">{{ Auth::user()->full_name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><a class="dropdown-item" href="{{route ('body.metric') }}"><i class="fas fa-user-circle me-2 text-muted"></i> Hồ sơ cá nhân</a></li>
                                <li><a class="dropdown-item" href="{{route('my.membership')}}"><i class="fas fa-dumbbell me-2 text-muted"></i> Gói tập của tôi</a></li>
                                <li><a class="dropdown-item" href="{{route('orders.index')}}"><i class="fas fa-shopping-basket me-2 text-muted"></i> giỏ hàng của tôi</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{route('logout')}}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger border-0 bg-transparent w-100 text-start">
                                            <i class="fas fa-sign-out-alt me-2"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <header class="hero-section text-center">
        <div class="container">
            <h1 class="display-3 fw-bold">NO PAIN, NO GAIN</h1>
            <p class="lead">Hệ thống phòng tập hiện đại nhất khu vực với đội ngũ PT chuyên nghiệp.</p>
            <a href="#pricing" class="btn btn-warning btn-lg fw-bold px-5">TẬP NGAY HÔM NAY</a>
        </div>
    </header>

    <section id="pricing" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">CÁC GÓI TẬP LINH HOẠT</h2>
                <p class="text-muted">Chọn gói phù hợp để bắt đầu hành trình thay đổi bản thân</p>
            </div>
            
            <div class="row g-4">
                @if(isset($goiTaps) && $goiTaps->count() > 0)
                    @foreach($goiTaps as $item)
                    <!-- hiển thị gói tập-->
                    <div class="col-md-4">
                        <div class="card h-100 border-0 card-gym text-center shadow-sm">
                            <div class="card-body py-5 px-4">
                                <h3 class="fw-bold text-dark">{{ $item->package_name }}</h3>
                                <h2 class="text-warning my-4">{{ number_format($item->price) }} VNĐ</h2>
                                <p class="badge bg-secondary mb-3">{{ $item->duration_days }} Ngày tập luyện</p>
                                <p class="text-muted small">{{ $item->description }}</p>
                                <!--btn-register" data-id="{{ $item->id }}"*-- java nút lưu gói tập-->
                                <button class="btn btn-dark w-100 mt-3 py-2 fw-bold btn-register" data-id="{{ $item->id }}">ĐĂNG KÝ NGAY</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-5">
                        <p class="text-muted italic">Đang cập nhật các gói tập mới nhất...</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @guest
        <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">TẠO TÀI KHOẢN MỚI</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">Tên của bạn</label>
                                <input type="text" name="full_name" class="form-control" required placeholder="Ví dụ: Nguyễn Văn An">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Email / Số điện thoại</label>
                                <input type="email" name="email" class="form-control" required placeholder="name@example.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Mật khẩu</label>
                                <input type="password" name="password" class="form-control" required placeholder="********">
                            </div>
                                <a href="{{ route('auth.google') }}" class="btn btn-danger w-50 mb-2">
                                     <i class="fab fa-google"></i> Đăng nhập bằng Google
                                </a>
                             <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-warning fw-bold py-2">ĐĂNG KÝ TÀI KHOẢN</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">ĐĂNG NHẬP HỆ THỐNG</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form action="{{ route('login.post') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-medium">Email</label>
                                <input type="email" name="email" class="form-control" required placeholder="admin@gmail.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-medium">Mật khẩu</label>
                                <input type="password" name="password" class="form-control" required placeholder="********">
                            </div>
                                <a href="{{ route('auth.google') }}" class="btn btn-danger w-50 mb-2">
                                    <i class="fab fa-google"></i> Đăng nhập bằng Google
                                </a>
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-dark fw-bold py-2">ĐĂNG NHẬP</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endguest

    <footer class="bg-dark text-white py-4 mt-5 text-center border-top border-secondary">
        <p class="mb-0">&copy; 2026 GymPro Fitness Center. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
// lệnh xử lý nút đăng ký gói tập//
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.btn-register').on('click', function() {
        // 1. Lấy ID của gói tập từ cái nút vừa bấm
        let packageId = $(this).data('id');

        // 2. Gửi yêu cầu đăng ký lên Server
        $.ajax({
            url: "{{ route('membership.register') }}", // Đường dẫn đã khai báo trong web.php
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}", // Bắt buộc phải có để bảo mật
                package_id: packageId
            },
            success: function(res) {
                if(res.success) {
                    alert(res.message);
                    // 3. Chuyển hướng khách sang trang "Gói tập của tôi" để xem kết quả
                    window.location.href = "{{ route('my.membership') }}";
                }
            },
            error: function(xhr) {
                if(xhr.status === 401) {
                    alert('Bạn cần đăng nhập để đăng ký gói tập!');
                    window.location.href = "/login";
                } else {
                    alert('Có lỗi xảy ra, vui lòng thử lại.');
                }
            }
        });
    });
});
</script>