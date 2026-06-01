@extends('layout.frontend')
@section('content')
    <header class="border-b border-white/10 pb-6 space-y-1 max-w-7xl mx-auto px-4 md:px-8">
        <div class="flex items-center gap-2 text-primary font-headline text-xs font-bold uppercase tracking-widest">
            <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span> KOR Group Session
        </div>
        <h1 class="font-headline text-3xl md:text-4xl text-white uppercase tracking-tight font-extrabold">Lớp Học Nhóm Linh Hoạt</h1>
        <p class="text-sm text-gray-400 max-w-xl">Hệ thống giám sát và quét mã tham gia các lớp tập nhóm được chốt lịch chủ động giữa PT và học viên.</p>
    </header>

    {{-- Thanh công cụ: Bộ lọc Category và Ô tìm kiếm nhanh --}}
    <div class="flex flex-wrap items-center justify-between gap-4 pb-2 mt-4 max-w-7xl mx-auto px-4 md:px-8">
        <div class="flex flex-wrap items-center gap-2">
            <button onclick="updateFilter('all', this)" class="filter-btn whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold bg-primary text-white shadow-md transition-all">Tất cả</button>
            <button onclick="updateFilter('yoga', this)" class="filter-btn whitespace-nowrap px-4 py-2 rounded-full text-xs font-medium bg-white/5 text-gray-400 border border-white/5 hover:text-white transition-all">Yoga & Pilates</button>
            <button onclick="updateFilter('cardio', this)" class="filter-btn whitespace-nowrap px-4 py-2 rounded-full text-xs font-medium bg-white/5 text-gray-400 border border-white/5 hover:text-white transition-all">Cardio / Spinning</button>
            <button onclick="updateFilter('boxing', this)" class="filter-btn whitespace-nowrap px-4 py-2 rounded-full text-xs font-medium bg-white/5 text-gray-400 border border-white/5 hover:text-white transition-all">Kickboxing & Combat</button>
        </div>

        {{-- Thanh tìm kiếm được tích hợp từ logic classes1 --}}
        <div class="w-full md:w-72">
            <div class="relative">
                <span class="material-symbols-outlined absolute left-4 top-2.5 text-gray-500 text-sm">search</span>
                <input type="text" id="classSearchInput" onkeyup="handleSearch()" placeholder="Tìm tên lớp học..." class="w-full bg-black/20 border border-white/10 rounded-full text-white pl-10 pr-4 py-2.5 text-xs focus:outline-none focus:border-primary transition-all">
            </div>
        </div>
    </div>

    {{-- Khối thông báo Alert hệ thống (nếu có) từ classes1 --}}
    @if(session('success'))
        <div class="simple-alert bg-green-500/10 border border-green-500/20 text-green-400 text-xs p-4 rounded-xl mt-4 flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button type="button" class="btn-close text-white opacity-50 hover:opacity-100" onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="simple-alert bg-red-500/10 border border-red-500/20 text-red-400 text-xs p-4 rounded-xl mt-4 flex items-center justify-between">
            <span>{{ session('error') }}</span>
            <button type="button" class="btn-close text-white opacity-50 hover:opacity-100" onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif

    {{-- Vùng hiển thị danh sách lớp học nhóm --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6 max-w-7xl mx-auto px-4 md:px-8" id="classes-container">
        
        <div class="col-span-full text-center py-20 text-gray-500 animate-pulse italic">Đang tải danh sách lớp học...</div>
    </div>
    {{-- Hiển thị thanh điều hướng phân trang --}}
        <div class="mt-8 flex justify-center">
            {{ $classes->links() }}
        </div>
    
    {{-- Thông báo trống --}}
    <div id="emptySearchMessage" class="hidden text-center py-20 bg-[#1A1A1A] rounded-2xl border border-white/5 mt-6">
        <span class="material-symbols-outlined text-5xl text-gray-700 block mb-3">event_busy</span>
        <p class="text-gray-400 text-sm italic">Không tìm thấy lớp học phù hợp với yêu cầu của bạn.</p>
    </div>
@endsection

{{-- NHÚNG CÁC THƯ VIỆN HỖ TRỢ XỬ LÝ LOGIC TỪ CLASSES1 --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let allData = { classes: [], bookedClassIds: [] };
    let currentCategory = 'all';
    let currentSearch = '';
    let searchTimer;
    // Bảo mật: Hàm lọc ký tự đặc biệt ngăn chặn XSS
    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text ? String(text).replace(/[&<>"']/g, m => map[m]) : '';
    }

    // LOGIC: Tải dữ liệu bằng Fetch API - Tận dụng hàm lọc từ Controller, now accepts page parameter
    async function fetchClasses(page = 1) {
        try {
            const response = await fetch(`{{ route('classes.index') }}?category=${currentCategory}&search=${encodeURIComponent(currentSearch)}&page=${page}&ajax_call=1`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            allData = await response.json();
            renderClasses();
        } catch (error) {
            console.error("Fetch Error:", error);
            document.getElementById('classes-container').innerHTML = '<p class="col-span-full text-center text-primary text-xs mt-4">Lỗi tải dữ liệu hệ thống!</p>';
        }
    }

    function updateFilter(category, btn) {
        currentCategory = category;
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(b => b.className = "filter-btn whitespace-nowrap px-4 py-2 rounded-full text-xs font-medium bg-white/5 text-gray-400 border border-white/5 hover:text-white transition-all");
        btn.className = "filter-btn whitespace-nowrap px-4 py-2 rounded-full text-xs font-bold bg-primary text-white shadow-md transition-all";
        fetchClasses(); // Gọi server để lọc
    } 

    function handleSearch() {
        clearTimeout(searchTimer);
        currentSearch = document.getElementById('classSearchInput').value.trim();
        // Đợi 300ms sau khi ngừng gõ mới gửi request để tối ưu hiệu năng
        searchTimer = setTimeout(() => {
            fetchClasses();
        }, 300);
    }

    function renderClasses() {
        const container = document.getElementById('classes-container');
        const emptyMsg = document.getElementById('emptySearchMessage');
        container.innerHTML = '';
        const filtered = allData.classes.data; // Correctly access the 'data' array from the paginator object
        if (filtered.length === 0) {
            emptyMsg.classList.remove('hidden');
            return;
        }
        emptyMsg.classList.add('hidden');
        
        filtered.forEach(c => {
            const remaining = c.max_capacity - c.booked_count;
            const isFull = remaining <= 0;
            const isBooked = allData.bookedClassIds.includes(c.id);
            const percent = c.max_capacity > 0 ? Math.round((c.booked_count / c.max_capacity) * 100) : 0;
            const barColor = isFull ? 'bg-primary' : (percent >= 80 ? 'bg-yellow-500' : 'bg-emerald-500');
            
            const html = `
                <div class="class-item bg-gradient-to-b from-[#1E1E1E] to-[#141414] border border-white/10 rounded-2xl overflow-hidden shadow-2xl hover:border-primary/40 transition-all duration-300 flex flex-col group">
                    <div class="h-44 w-full overflow-hidden relative">
                        <img class="w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-700" 
                             src="/images/classes/${c.image || 'default-class.jpg'}" alt="${escapeHtml(c.name)}">
                        <div class="absolute inset-0 bg-gradient-to-t from-[#141414] via-transparent to-transparent"></div>
                        ${isBooked ? '<span class="absolute top-4 right-4 z-20 text-[10px] font-bold bg-emerald-500 text-white px-3 py-1 rounded-md uppercase tracking-wider">Đã đăng ký</span>' : ''}
                    </div>
                    <div class="p-5 flex-grow space-y-3">
                        <h3 class="class-name font-headline text-lg text-white uppercase font-bold group-hover:text-primary transition-colors">${escapeHtml(c.name)}</h3>
                        <div class="bg-black/30 p-3 rounded-lg border border-white/5">
                            <div class="flex flex-col gap-1.5 text-[11px] text-gray-400">
                                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-primary">person</span> PT: <strong class="text-white font-normal">${escapeHtml(c.pt?.user?.full_name || 'Hệ thống KOR')}</strong></span>
                                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-primary">payments</span> Giá: <strong class="text-white font-normal">${new Intl.NumberFormat('vi-VN').format(c.price)}đ</strong></span>
                                <span class="flex items-center gap-2"><span class="material-symbols-outlined text-sm text-primary">calendar_month</span> Thời lượng: <strong class="text-white font-normal">${c.total_sessions} buổi</strong></span>
                            </div>
                        </div>
                        
                        <p class="text-gray-400 text-xs line-clamp-4 italic">${escapeHtml(c.description || 'Chưa có mô tả chi tiết cho lớp học này.')}</p>

                        <div class="space-y-1.5 pt-4">
                            <div class="flex justify-between text-[10px] font-bold uppercase tracking-wider">
                                <span class="text-gray-500">Sức chứa</span>
                                <span class="${isFull ? 'text-primary' : 'text-emerald-500'}">${c.booked_count}/${c.max_capacity}</span>
                            </div>
                            <div class="w-full h-1 bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full ${barColor} transition-all duration-500" style="width: ${percent}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 pt-0">
                        ${isBooked ? `
                            <button class="w-full border border-white/10 text-gray-500 text-xs font-bold py-3 rounded-xl uppercase tracking-wider" disabled>Đã có trong lịch tập</button>
                        ` : (isFull ? `
                            <button class="w-full bg-white/5 text-gray-700 text-xs font-bold py-3 rounded-xl uppercase tracking-wider" disabled>Đã hết chỗ</button>
                        ` : `
                            <form action="{{ route('classes.store') }}" method="POST" class="form-book-class">
                                @csrf
                                <input type="hidden" name="class_id" value="${c.id}">
                                <button type="submit" class="w-full bg-primary hover:bg-red-700 text-white font-headline text-sm uppercase py-3 rounded-xl transition-all shadow-lg shadow-primary/20 flex items-center justify-center gap-2 tracking-wider">
                                    Đăng ký suất tập
                                </button>
                            </form>
                        `)}
                    </div>
                </div>`;
            container.insertAdjacentHTML('beforeend', html);
        });
    }

    /**
     * LOGIC 3: AJAX ĐĂNG KÝ & THÔNG BÁO ĐƠN GIẢN
     */
    function showSimpleToast(message, isError = false) {
        const toast = document.createElement('div');
        toast.className = `fixed bottom-10 left-1/2 -translate-x-1/2 z-[9999] px-6 py-3 rounded-full text-white text-xs font-bold shadow-2xl transition-all duration-500 animate-bounce ${isError ? 'bg-red-600' : 'bg-emerald-600'}`;
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 500);
        }, 3000);
    }

    $(document).ready(function() {
        fetchClasses();

        $(document).on('submit', '.form-book-class', function(e) {
            e.preventDefault();
            
            let form = $(this);
            let submitBtn = form.find('button[type="submit"]');
            
            submitBtn.prop('disabled', true).addClass('opacity-50');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: form.serialize(),
                success: function(res) {
                    if (res.success) {
                        showSimpleToast(res.message || "Đã thêm vào giỏ hàng!");
                        setTimeout(() => {
                            window.location.href = res.redirect_url;
                        }, 1000);
                    } else {
                        submitBtn.prop('disabled', false).removeClass('opacity-50');
                        showSimpleToast(res.message || "Có lỗi xảy ra", true);
                    }
                },
                error: function(xhr) {
                    submitBtn.prop('disabled', false).removeClass('opacity-50');
                    if (xhr.status === 401) {
                        window.location.href = "{{ route('login') }}";
                    } else {
                        let error = xhr.responseJSON?.error || 'Không thể đăng ký!';
                        showSimpleToast(error, true);
                    }
                }
            });
        });

        // Tự động đóng thông báo sau 4 giây 
        setTimeout(() => {
            $('.alert').fadeOut('slow', function() {
                $(this).remove();
            });
        }, 4000);
    });

    document.addEventListener('DOMContentLoaded', () => {
        fetchClasses(); // Initial load of classes and pagination
    });
</script>