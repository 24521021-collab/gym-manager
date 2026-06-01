@extends('layout.frontend')
@section('content')
{{-- 2. VÙNG NỘI DUNG CHÍNH (Dàn theo dạng 1 cột dọc chồng các box chuẩn mẫu) --}}
<main class="max-w-2xl mx-auto px-4 py-8 flex-1 w-full space-y-6">
    
    <h1 class="font-headline text-2xl font-extrabold text-white uppercase tracking-tight mb-4">Hồ sơ cá nhân</h1>

    {{-- Ô 1: THÔNG TIN HỘI VIÊN --}}
    <div class="bg-[#141414] border border-white/10 rounded-2xl p-6 shadow-xl text-center space-y-6">
        <div class="flex flex-col items-center text-center space-y-3 pb-6 border-b border-white/10">
            <div class="w-24 h-24 rounded-full border-2 border-primary p-1 overflow-hidden">
            <img alt="Avatar Hội Viên" class="w-full h-full rounded-full border-2 border-primary object-cover p-1 bg-black/40" 
                 src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=E31837&color=fff"/>
            </div>
            <div>
                <h2 class="font-headline text-2xl text-white uppercase tracking-wide">{{ Auth::user()->full_name }}</h2>
                <p class="text-xs text-primary font-bold uppercase tracking-widest mt-1">Mã Hội viên: KOR-{{ Auth::id() }}</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs font-body text-left">
            <div class="bg-black/20 p-3.5 rounded-xl border border-white/5 space-y-1">
                <span class="text-gray-500 font-bold uppercase tracking-wider block text-[10px]">Họ và tên hội viên</span>
                <span class="text-white font-medium">{{ Auth::user()->full_name }}</span>
            </div>
            <div class="bg-black/20 p-3.5 rounded-xl border border-white/5 space-y-1">
                <span class="text-gray-500 font-bold uppercase tracking-wider block text-[10px]">Địa chỉ Email</span>
                <span class="text-white font-medium">{{ Auth::user()->email }}</span>
            </div>
            <div class="bg-black/20 p-3.5 rounded-xl border border-white/5 space-y-1">
                <span class="text-gray-500 font-bold uppercase tracking-wider block text-[10px]">Hạng thẻ đăng ký</span>
                <span class="text-primary-container font-bold uppercase">{{ Auth::user()->membership->package->package_name ?? 'Chưa có' }} ({{ Auth::user()->membership->package->duration_days ?? 'N/A' }} Ngày)</span>
            </div>
            <div class="bg-black/20 p-3.5 rounded-xl border border-white/5 space-y-1">
                <span class="text-gray-500 font-bold uppercase tracking-wider block text-[10px]">Cơ sở tập luyện</span>
                <span class="text-white font-medium">KOR GYM - Chi nhánh Thủ Đức</span>
            </div>
        </div>

        {{-- Logout button, integrated with Laravel's logout route --}}
        <div class="pt-2 border-t border-white/10 flex flex-col gap-3">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full bg-transparent hover:text-primary text-gray-500 text-xs font-bold uppercase tracking-wider py-2 transition-colors">Đăng xuất tài khoản</button>
            </form>
        </div>
    </div>

    {{-- Ô 2: BẢO MẬT & ĐỔI MẬT KHẨU --}}
    <div class="bg-[#141414] border border-white/10 rounded-2xl p-6 shadow-xl space-y-4">
        <div class="flex items-center gap-2 border-b border-white/5 pb-3">
            <span class="material-symbols-outlined text-primary">lock</span>
            <h3 class="text-base font-bold text-white uppercase tracking-wider">Bảo mật & Đổi mật khẩu</h3>
        </div>

        <form id="changePasswordForm" action="{{ route('password.change') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" required class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary placeholder:text-gray-600" placeholder="Nhập mật khẩu cũ">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Mật khẩu mới</label>
                <input type="password" name="new_password" required class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary placeholder:text-gray-600" placeholder="Tối thiểu 8 ký tự">
            </div>
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Xác nhận mật khẩu mới</label>
                <input type="password" name="new_password_confirmation" required class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary placeholder:text-gray-600" placeholder="Nhập lại mật khẩu mới">
            </div>

            <div id="password-msg" class="text-xs transition-all min-h-[16px]"></div>

            <div class="flex items-center justify-between pt-2">
                <button type="button" onclick="scrollToForgotBox()" class="text-xs text-gray-400 hover:text-white underline transition-colors">
                    Quên mật khẩu hiện tại?
                </button>
                <button type="submit" id="btnSavePassword" class="bg-primary hover:bg-red-700 text-white font-bold text-xs uppercase py-2.5 px-6 rounded-xl transition-all shadow-md tracking-wider">
                    Cập nhật mật khẩu
                </button>
            </div>
        </form>
    </div>

    {{-- Ô 3: KHU VỰC QUÊN MẬT KHẨU (TÁCH BIỆT XUỐNG Ô DƯỚI CÙNG) --}}
    <div id="forgotBox" class="bg-[#141414] border border-white/10 rounded-2xl p-6 shadow-xl space-y-4">
        <div class="flex items-center gap-2 border-b border-white/5 pb-3">
            <span class="material-symbols-outlined text-yellow-500">help_center</span>
            <h3 class="text-base font-bold text-white uppercase tracking-wider">Quên Mật Khẩu?</h3>
        </div>
        
        <p class="text-xs text-gray-400 leading-relaxed">
            Hệ thống sẽ gửi một liên kết đặt lại mật khẩu an toàn trực tiếp đến địa chỉ email tài khoản của bạn để tiến hành khôi phục.
        </p>

        <form id="forgotPasswordForm" action="{{ route('password.forgot.post') }}" method="POST" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1.5">Địa chỉ Email nhận liên kết</label>
                <input type="email" id="forgot_email" name="email" value="{{ Auth::user()->email }}" required class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white focus:outline-none focus:border-primary" placeholder="name@example.com">
            </div>

            <div id="forgot-msg" class="text-xs transition-all min-h-[16px]"></div>

            <button type="submit" id="btnSendForgot" class="w-full py-2.5 bg-yellow-500 hover:bg-yellow-600 text-black font-bold text-xs uppercase rounded-xl shadow-md tracking-wider transition-all">
                Gửi Yêu Cầu Khôi Phục
            </button>
        </form>
    </div>

</main>
{{-- 3. FOOTER COPYRIGHT --}}
<footer class="border-t border-white/5 py-4 bg-black/40">
    <p class="text-center text-[11px] text-gray-600 tracking-wider">© {{ date('Y') }} KOR GYM LUXURY FITNESS. All Rights Reserved.</p>
</footer>
@endsection
{{-- 4. JAVASCRIPT ĐIỀU HƯỚNG VÀ XỬ LÝ API AJAX KHÔNG LOAD LẠI TRANG --}}
@push('scripts')
<script>
    // Cuộn mượt xuống ô Quên mật khẩu khi ấn link
    function scrollToForgotBox() {
        const email = "{{ Auth::user()->email }}";
        document.getElementById('forgot_email').value = email;
        document.getElementById('forgotBox').scrollIntoView({ behavior: 'smooth' });
    }

    // AJAX Xử lý Đổi Mật Khẩu
    document.getElementById('changePasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const msg = document.getElementById('password-msg');
        const btn = document.getElementById('btnSavePassword');
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerText = "Đang xử lý...";
        msg.innerText = "";

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            const data = await response.json();

            if (response.ok && data.success) {
                msg.className = "text-emerald-500 font-bold italic text-xs";
                msg.innerText = data.message || "Cập nhật mật khẩu thành công!";
                form.reset(); // Xóa sạch dữ liệu trong form sau khi đổi thành công
            } else {
                msg.className = "text-red-500 font-bold italic text-xs";
                if (data.errors) {
                    // Lấy lỗi đầu tiên trong danh sách validation errors trả về từ server
                    const firstError = Object.values(data.errors)[0][0];
                    msg.innerText = firstError;
                } else {
                    msg.innerText = data.message || "Mật khẩu cũ không đúng hoặc dữ liệu không hợp lệ.";
                }
            }
        } catch (error) {
            msg.className = "text-red-500 font-bold italic text-xs";
            msg.innerText = "Lỗi kết nối máy chủ đường truyền.";
        } finally {
            btn.disabled = false;
            btn.innerText = "Cập nhật mật khẩu";
        }
    });

    // AJAX Xử lý Gửi Link Khôi Phục Mật Khẩu
    document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const form = this;
        const msg = document.getElementById('forgot-msg');
        const btn = document.getElementById('btnSendForgot');
        const formData = new FormData(form);

        btn.disabled = true;
        btn.innerText = "Đang gửi link khôi phục...";
        msg.innerText = "";

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });
            const data = await response.json();

            if (response.ok) {
                msg.className = "text-emerald-500 font-bold italic text-xs";
                msg.innerText = data.message || "Hệ thống đã gửi liên kết khôi phục vào Email của bạn.";
            } else {
                msg.className = "text-red-500 font-bold italic text-xs";
                if (data.errors) {
                    const firstError = Object.values(data.errors)[0][0];
                    msg.innerText = firstError;
                } else {
                    msg.innerText = data.message || "Email không tồn tại trong hệ thống.";
                }
            }
        } catch (error) {
            msg.className = "text-red-500 font-bold italic text-xs";
            msg.innerText = "Không thể gửi yêu cầu, vui lòng thử lại sau.";
        } finally {
            btn.disabled = false;
            btn.innerText = "Gửi Yêu Cầu Khôi Phục";
        }
    });
</script>
@endpush