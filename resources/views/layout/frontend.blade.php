<html class="dark" lang="vi">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'KOR GYM')</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background min-h-screen antialiased selection:bg-primary-container selection:text-white pb-24 md:pb-12">

<nav class="bg-background/90 backdrop-blur-xl border-b border-white/10 sticky top-0 left-0 w-full z-50 flex justify-between items-center px-4 md:px-8 h-16 shadow-lg">
    <div class="flex items-center gap-8">
        <a class="font-headline text-2xl font-bold text-primary tracking-tighter uppercase italic" href="/">KOR GYM</a>
        
        {{-- MENU ĐIỀU HƯỚNG CHÍNH - ĐÃ ĐƯỢC XỬ LÝ ACTIVE STATE CHỈ ĐỔI MÀU CHỮ --}}
        <div class="hidden md:flex gap-6 items-center">
            <a class="{{ request()->is('products*') ? 'text-primary' : 'text-gray-400' }} hover:text-primary transition-colors font-headline text-lg uppercase tracking-tight" href="/products">
                Cửa hàng
            </a>
            <a class="{{ request()->is('classes*') ? 'text-primary' : 'text-gray-400' }} hover:text-primary transition-colors font-headline text-lg uppercase tracking-tight" href="/classes">
                Lớp học
            </a>
            <a class="{{ request()->is('booking*') || request()->routeIs('booking.pt.index') ? 'text-primary' : 'text-gray-400' }} hover:text-primary transition-colors font-headline text-lg uppercase tracking-tight" href="{{ route('booking.pt.index') }}">
                Đặt Lịch
            </a>
            <a class="{{ request()->is('posts*') ? 'text-primary' : 'text-gray-400' }} hover:text-primary transition-colors font-headline text-lg uppercase tracking-tight" href="{{ route('posts.index') }}">
                Kiến thức
            </a>
        </div>
    </div>
    
    <div class="flex items-center gap-3">
        @guest
            <button class="text-sm font-medium text-white border border-white/20 hover:bg-white/10 px-4 py-1.5 rounded-xl transition-colors" data-bs-toggle="modal" data-bs-target="#loginModal">Đăng nhập</button>
            <button class="text-sm font-bold bg-primary hover:bg-red-700 text-white px-4 py-1.5 rounded-xl transition-colors" data-bs-toggle="modal" data-bs-target="#registerModal">Đăng ký</button>
        @else
            <a href="/cart" class="text-primary hover:bg-white/5 p-2 rounded-full transition-colors relative flex items-center justify-center">
                <span class="material-symbols-outlined">shopping_cart</span>
                @if(session('cart') && count(session('cart')) > 0)
                    <span class="absolute -top-1 -right-1 bg-red-600 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full shadow-[0_0_8px_rgba(227,24,55,0.8)]">
                        {{ count(session('cart')) }}
                    </span>
                @else
                    <span class="absolute top-1 right-1 w-2 h-2 bg-primary rounded-full shadow-[0_0_8px_rgba(227,24,55,0.8)]"></span>
                @endif
            </a>
            
            <a href="{{ route('notifications') }}" class="text-primary hover:bg-white/5 p-2 rounded-full transition-colors flex items-center justify-center relative">
                <span class="material-symbols-outlined">notifications</span>
                {{-- Hiển thị chấm đỏ nếu có thông báo chưa đọc --}}
                @if(App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->exists())
                    <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 bg-red-600 rounded-full border-2 border-[#1A1A1A] shadow-[0_0_10px_rgba(220,38,38,0.8)] animate-pulse"></span>
                @endif
            </a>

            <a href="{{ route('profile') }}" class="flex items-center gap-2 py-1 group">
                <img alt="Avatar" class="w-8 h-8 rounded-full border border-white/20 object-cover group-hover:border-primary transition-colors" 
                     src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=random&color=fff"/>
                <span class="hidden md:inline text-sm font-medium text-gray-300 group-hover:text-white transition-colors">{{ Auth::user()->full_name }}</span>
            </a>
        @endguest
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 md:px-8 py-8">
    @yield('content')
</main>

@guest
    <div id="loginModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-sm p-4">
        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
            <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-white" onclick="toggleTailwindModal('loginModal')">
                <span class="material-symbols-outlined">close</span>
            </button>
            <h5 class="font-headline text-xl font-bold text-white mb-6 uppercase tracking-tight">Đăng Nhập Hệ Thống</h5>
            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Email</label>
                    <input type="email" name="email" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary placeholder:text-gray-600" required placeholder="admin@gmail.com">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Mật khẩu</label>
                    <input type="password" name="password" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary placeholder:text-gray-600" required placeholder="********">
                </div>
                <div class="flex justify-between items-center pt-1">
                    <a href="{{ route('auth.google') }}" class="inline-flex items-center gap-2 bg-red-600/10 hover:bg-red-600/20 border border-red-500/20 text-red-400 text-xs font-bold py-2 px-4 rounded-xl transition-colors">
                        <i class="fab fa-google"></i> Login via Google
                    </a>
                    <a href="#" class="text-xs text-gray-400 hover:text-white transition-colors" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal" data-bs-dismiss="modal">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="w-full py-3 bg-primary hover:bg-red-700 text-white font-headline text-sm uppercase rounded-xl shadow-md font-bold tracking-wider transition-all mt-4">Đăng Nhập</button>
            </form>
        </div>
    </div>

    <div id="registerModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-sm p-4">
        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
            <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-white" onclick="toggleTailwindModal('registerModal')">
                <span class="material-symbols-outlined">close</span>
            </button>
            <h5 class="font-headline text-xl font-bold text-white mb-6 uppercase tracking-tight">Tạo Tài Khoản Mới</h5>
            <form action="{{ route('register') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Tên của bạn</label>
                    <input type="text" name="full_name" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary placeholder:text-gray-600" required placeholder="Ví dụ: Nguyễn Văn An">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Email / Số điện thoại</label>
                    <input type="email" name="email" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary placeholder:text-gray-600" required placeholder="name@example.com">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Mật khẩu</label>
                    <input type="password" name="password" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary placeholder:text-gray-600" required placeholder="********">
                </div>
                <div class="pt-1">
                    <a href="{{ route('auth.google') }}" class="inline-flex items-center gap-2 bg-red-600/10 hover:bg-red-600/20 border border-red-500/20 text-red-400 text-xs font-bold py-2 px-4 rounded-xl transition-colors w-full justify-center">
                        <i class="fab fa-google"></i> Đăng ký bằng tài khoản Google
                    </a>
                </div>
                <button type="submit" class="w-full py-3 bg-yellow-500 hover:bg-yellow-600 text-black font-headline text-sm uppercase rounded-xl shadow-md font-bold tracking-wider transition-all mt-4">Đăng Ký Tài Khoản</button>
            </form>
        </div>
    </div>

    <div id="forgotPasswordModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/75 backdrop-blur-sm p-4">
        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl max-w-md w-full p-6 shadow-2xl relative">
            <button type="button" class="absolute top-4 right-4 text-gray-400 hover:text-white" onclick="toggleTailwindModal('forgotPasswordModal')">
                <span class="material-symbols-outlined">close</span>
            </button>
            <h5 class="font-headline text-xl font-bold text-white mb-3 uppercase tracking-tight">Khôi Phục Mật Khẩu</h5>
            <p class="text-xs text-gray-400 mb-5 leading-relaxed">Nhập email của bạn, hệ thống tự động sẽ xử lý gửi thông tin cấp lại mật khẩu mới mặc định.</p>
            <form onsubmit="sendDefaultPassword(event)" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Email của bạn</label>
                    <input type="email" id="forgot_email" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary placeholder:text-gray-600" placeholder="example@gmail.com" required>
                </div>
                <div id="forgot-msg" class="text-xs transition-all"></div>
                <button type="submit" id="btnForgot" class="w-full py-3 bg-primary hover:bg-red-700 text-white font-headline text-sm uppercase rounded-xl shadow-md font-bold tracking-wider transition-all mt-2">Gửi Yêu Cầu</button>
            </form>
        </div>
    </div>
@endguest
@stack('scripts')
<script>
    function toggleTailwindModal(id) {
        const modal = document.getElementById(id);
        if (modal) {
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            } else {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }
        }
    }

    // Lắng nghe các thuộc tính dữ liệu bootstrap của mã nguồn cũ để ánh xạ chính xác
    document.addEventListener('click', function(e) {
        const target = e.target.closest('[data-bs-toggle="modal"]');
        if (target) {
            e.preventDefault();
            const openModal = e.target.closest('.fixed:not(.hidden)');
            if (openModal) {
                openModal.classList.remove('flex');
                openModal.classList.add('hidden');
            }
            const targetId = target.getAttribute('data-bs-target').replace('#', '');
            toggleTailwindModal(targetId);
        }
    });
    async function sendDefaultPassword(event) {
        event.preventDefault(); // Chặn việc reload lại trang của form mặc định
        
        const email = document.getElementById('forgot_email').value;
        const msg = document.getElementById('forgot-msg');
        const btn = document.getElementById('btnForgot');
        const originalText = btn.innerText;

        // Thiết lập trạng thái đang gửi (Loading) cho nút và dòng thông báo
        btn.disabled = true;
        btn.innerText = "Đang xử lý...";
        msg.className = "mt-2 text-xs text-gray-400";
        msg.innerText = "Hệ thống đang kết nối máy chủ, vui lòng đợi...";

        try {
            const response = await fetch("{{ route('password.forgot.post') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email: email })
            });

            const data = await response.json();

            if (data.success) {
                // Xử lý khi Backend trả về trạng thái THÀNH CÔNG (Đã đổi giao diện sang chuẩn Tailwind)
                msg.className = "mt-2 text-xs text-green-400 font-medium bg-green-500/10 p-2.5 rounded-xl border border-green-500/20";
                msg.innerText = data.message || "Mật khẩu mới mặc định đã được gửi vào hòm thư Email của bạn!";
                
                // Đợi 2.5 giây cho người dùng đọc thông báo rồi tự động đóng cửa sổ modal
                setTimeout(() => {
                    toggleTailwindModal('forgotPasswordModal');
                    document.getElementById('forgot_email').value = '';
                    msg.innerText = '';
                }, 2500);

            } else {
                // Xử lý khi Backend báo lỗi (Ví dụ: Email không tồn tại)
                msg.className = "mt-2 text-xs text-red-400 font-medium bg-red-500/10 p-2.5 rounded-xl border border-red-500/20";
                msg.innerText = data.message || "Email này chưa được đăng ký trên hệ thống KOR.";
            }
        } catch (error) {
            // Xử lý khi mất mạng hoặc Server bị sập (Lỗi HTTP 500, v.v...)
            msg.className = "mt-2 text-xs text-red-400 font-medium bg-red-500/10 p-2.5 rounded-xl border border-red-500/20";
            msg.innerText = "Lỗi kết nối máy chủ, vui lòng thử lại sau.";
        } finally {
            // Khôi phục lại trạng thái ban đầu của nút bấm sau khi xử lý xong
            btn.disabled = false;
            btn.innerText = originalText;
        }
    }
</script>
</body>
</html>