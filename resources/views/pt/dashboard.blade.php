
<html class="dark" lang="vi">
    <head>
        <meta charset="utf-8"/>
        <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
        <title>Cổng Huấn Luyện Viên - KOR GYM</title>
        <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
        <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
        <script src="{{ asset('assets/js/tailwind-config.js') }}"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    </head>
<body class="bg-background text-on-background min-h-screen antialiased selection:bg-primary-container selection:text-white pb-12" style="font-family: 'Inter', sans-serif;">

    <nav class="sticky top-0 z-50 bg-[#131313]/90 backdrop-blur-xl border-b border-white/10 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 md:px-8 h-16 flex items-center justify-between gap-4">
            <a href="{{ route('trang_chu') }}" class="font-headline text-2xl font-bold text-primary tracking-tighter uppercase italic" style="font-family: 'Oswald', sans-serif;">KOR GYM</a>
        
            <div class="hidden md:flex gap-8 items-center font-headline text-lg uppercase tracking-tight">
            <span class="text-primary border-b-2 border-primary pb-1 font-bold" style="font-family: 'Oswald', sans-serif;">Bảng Điều Khiển PT</span>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 bg-black/40 border border-white/10 px-3 py-1 rounded-xl text-xs text-gray-400 font-mono">
                <span class="w-1.5 h-1.5 rounded-full bg-green-400 animate-pulse"></span> Ca trực: Online
            </div>
            <a href="#" class="w-8 h-8 rounded-full border border-primary border-2 overflow-hidden block hover:opacity-80 transition-opacity">
                <img alt="PT Avatar" class="w-full h-full object-cover" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->full_name) }}&background=random&color=fff"/>
            </a>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 md:px-8 py-10 space-y-8">
    
    <header class="border-b border-white/10 pb-6 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2 text-primary font-headline text-xs font-bold uppercase tracking-widest" style="font-family: 'Oswald', sans-serif;">
                <span class="w-2 h-2 rounded-full bg-primary"></span> KOR Trainer Hub
            </div>
            <h1 class="font-headline text-3xl md:text-4xl text-white uppercase tracking-tight font-extrabold" style="font-family: 'Oswald', sans-serif;">Huấn Luyện Viên: {{ Auth::user()->full_name }}</h1>
            <p class="text-sm text-gray-400 max-w-xl">Quản lý lịch dạy tự chốt cá nhân, chấm điểm danh nhóm và giám sát dữ liệu sức khỏe người học.</p>
        </div>
    </header>

    <section class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-4 shadow-xl flex items-center gap-4">
            <span class="material-symbols-outlined text-green-400 bg-green-500/10 p-2.5 rounded-xl text-xl">payments</span>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider" style="font-family: 'Oswald', sans-serif;">Hoa hồng lịch riêng</p>
                <p class="text-lg font-bold text-white font-mono mt-0.5">{{ number_format($privateCommission ?? 0) }}đ</p>
            </div>
        </div>
        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-4 shadow-xl flex items-center gap-4">
            <span class="material-symbols-outlined text-purple-400 bg-purple-500/10 p-2.5 rounded-xl text-xl">group</span>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider" style="font-family: 'Oswald', sans-serif;">Hoa hồng lớp nhóm</p>
                <p class="text-lg font-bold text-white font-mono mt-0.5">{{ number_format($classCommission ?? 0) }}đ</p>
            </div>
        </div>
        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-4 shadow-xl flex items-center gap-4 border-l-4 border-l-primary">
            <span class="material-symbols-outlined text-primary bg-primary/10 p-2.5 rounded-xl text-xl">monetization_on</span>
            <div>
                <p class="text-[10px] text-primary font-bold uppercase tracking-wider" style="font-family: 'Oswald', sans-serif;">Tổng hoa hồng tạm tính</p>
                <p class="text-xl font-bold text-primary font-mono mt-0.5">{{ number_format($totalCommission ?? 0) }}đ</p>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <section class="col-span-12 lg:col-span-8 space-y-6">
            
            @forelse($classes ?? [] as $class)
            <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 shadow-xl space-y-5">
                <div class="flex justify-between items-center border-b border-white/10 pb-3">
                    <h2 class="font-headline text-base uppercase tracking-wider text-white font-bold" style="font-family: 'Oswald', sans-serif;">Lớp học nhóm đảm nhận dạy</h2>
                    <span class="bg-purple-500/20 text-purple-400 border border-purple-500/30 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider font-mono">Ca: 08:00 AM</span>
                </div>

                <div class="flex flex-col sm:flex-row justify-between sm:items-center bg-black/30 p-4 rounded-xl border border-white/5 gap-4">
                    <div class="flex items-start gap-3 text-xs">
                        <span class="material-symbols-outlined text-purple-400 bg-purple-500/10 p-2 rounded-lg">groups</span>
                        <div class="space-y-1">
                            <h4 class="font-bold text-white uppercase text-xs tracking-wide" style="font-family: 'Oswald', sans-serif;">{{ $class->name }}</h4>
                            <p class="text-[10px] text-gray-500 font-mono">Phân loại: Group Session • ID: #{{ $class->id }}</p>
                        </div>
                    </div>
                    <button onclick="openNoteModal('{{ $class->name }}')" class="bg-primary hover:bg-red-700 text-white text-xs font-bold uppercase px-4 py-2.5 rounded-xl transition-all shadow-md flex items-center justify-center gap-1.5 font-headline tracking-wide" style="font-family: 'Oswald', sans-serif;">
                        <span class="material-symbols-outlined text-base">edit_note</span> Ghi nhận ca dạy nhóm
                    </button>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-1" style="font-family: 'Oswald', sans-serif;">Danh sách học viên trong lớp (Tối đa {{ $class->max_capacity }} người)</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @forelse($class->bookings as $booking)
                        <div class="bg-black/20 p-3 rounded-xl border border-white/5 space-y-1.5 text-xs">
                            <div class="flex justify-between items-start">
                                <div class="flex flex-col">
                                    <span class="font-bold text-white" style="font-family: 'Oswald', sans-serif;">{{ optional($booking->user)->full_name }}</span>
                                    <span class="text-[9px] text-gray-400 font-mono">{{ optional($booking->user)->email }}</span>
                                </div>
                                <span class="text-[9px] bg-purple-500/10 text-purple-400 border border-purple-500/20 px-1.5 py-0.5 rounded uppercase font-bold">Member</span>
                            </div>
                            <div class="grid grid-cols-3 gap-1 text-[10px] font-mono text-gray-400 bg-[#131313] p-1.5 rounded-md text-center">
                                <div>Cao: <span class="text-white font-bold">{{ optional(optional($booking->user)->latestBodyMetric)->height ?? 'N/A' }}cm</span></div>
                                <div>Nặng: <span class="text-white font-bold">{{ optional(optional($booking->user)->latestBodyMetric)->weight ?? 'N/A' }}kg</span></div>
                                <div>Mỡ: <span class="text-primary font-bold">{{ optional(optional($booking->user)->latestBodyMetric)->body_fat_percentage ?? 'N/A' }}%</span></div>
                            </div>
                        </div>
                        @empty
                            <p class="text-gray-500 text-xs col-span-2">Chưa có học viên nào đăng ký lớp này.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-8 shadow-xl text-center">
                <p class="text-gray-500 italic">Bạn hiện chưa được phân công quản lý lớp học nhóm nào.</p>
            </div>
            @endforelse

            {{-- KHỐI NHẬT KÝ HUẤN LUYỆN - THAY THẾ CHO PHẦN HÌNH ẢNH --}}
            <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 shadow-xl space-y-5">
                <div class="flex justify-between items-center border-b border-white/10 pb-3">
                    <h2 class="font-headline text-base uppercase tracking-wider text-white font-bold" style="font-family: 'Oswald', sans-serif;">Nhật ký huấn luyện hàng ngày</h2>
                    <button onclick="openNoteModal()" class="text-primary text-[10px] font-bold uppercase hover:underline">Viết nhật ký mới +</button>
                </div>

                <div id="logs-list-container" class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                        @fragment('logs_list')
                        @forelse($logs as $log)
                        {{-- Đoạn fragment này dùng để render ra item đơn lẻ --}}
                        @fragment('log_item')
                        <div class="bg-black/20 p-4 rounded-xl border border-white/5 hover:border-primary/30 transition-all group animate-in fade-in slide-in-from-top-4 duration-300 break-words" id="log-{{ $log->id }}">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-primary text-lg">history_edu</span>
                                    <h4 class="font-bold text-white uppercase text-xs tracking-wide truncate max-w-[150px]" style="font-family: 'Oswald', sans-serif;" title="{{ $log->title }}">{{ $log->title }}</h4>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="text-[9px] text-gray-500 font-mono">{{ \Carbon\Carbon::parse($log->log_date)->format('d/m/Y') }}</span>
                                    @php
                                        $statusColors = [
                                            'completed' => 'bg-green-500/10 text-green-400 border-green-500/20',
                                            'upcoming' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                            'draft' => 'bg-gray-500/10 text-gray-400 border-gray-500/20'
                                        ];
                                        $statusLabels = [
                                            'completed' => 'Hoàn thành',
                                            'upcoming' => 'Sắp tới',
                                            'draft' => 'Bản nháp'
                                        ];
                                    @endphp
                                    <span class="px-2 py-0.5 border text-[8px] font-bold rounded uppercase {{ $statusColors[$log->status] ?? $statusColors['draft'] }}">
                                        {{ $statusLabels[$log->status] ?? $log->status }}
                                    </span>
                                </div>
                            </div>
                            <p class="text-[11px] text-gray-400 leading-relaxed italic whitespace-pre-line break-words">"{{ $log->content }}"</p>
                        </div>
                        @endfragment
                    @empty
                    <div id="empty-logs-placeholder" class="text-center py-8">
                        <span class="material-symbols-outlined text-4xl text-gray-700 block mb-2">history_edu</span>
                        <p class="text-gray-500 text-xs italic">Chưa có nhật ký huấn luyện nào được ghi nhận.</p>
                    </div>
                    @endforelse
                    @endfragment
                </div>
            </div>
        </section>

        <section class="col-span-12 lg:col-span-4 space-y-6">
            
            <div class="bg-[#1A1A1A] rounded-2xl p-6 border border-white/10 shadow-2xl space-y-4">
                <div class="flex justify-between items-center border-b border-white/10 pb-3">
                    <h2 class="font-headline text-base uppercase tracking-wider text-white font-bold" style="font-family: 'Oswald', sans-serif;">Lịch hẹn PT Cá Nhân</h2>
                    <span class="bg-primary/20 text-primary border border-primary/30 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider font-mono">Yêu cầu hẹn</span>
                </div>
                
                <div class="space-y-3">
                    @forelse($privateBookings as $booking)
                   <div class="bg-black/20 p-4 rounded-xl border border-white/5 space-y-3 hover:border-primary/30 transition-colors">
                        <div class="flex items-start gap-3 text-xs">
                            <div class="space-y-1">
                                <h4 class="font-bold text-white uppercase text-xs tracking-wide" style="font-family: 'Oswald', sans-serif;">Hội viên: {{ optional($booking->customer)->full_name }}</h4>
                                <p class="text-[10px] text-gray-500 font-mono">Thời gian đề xuất: {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i A') }} • {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</p>
                                <p class="text-[10px] text-gray-500 font-mono">Email: {{ optional($booking->customer)->email }}</p>
                                <p class="text-[10px] text-gray-400">Ghi chú: <span class="text-primary font-bold">{{ $booking->note ?? 'Không có' }}</span></p>
                                {{-- Hiển thị trạng thái hiện tại của lịch hẹn --}}
                                @if($booking->status === 'pending')
                                    <span class="px-2 py-0.5 border text-[10px] font-bold rounded uppercase bg-yellow-500/10 text-yellow-400 border-yellow-500/20">Đang chờ duyệt</span>
                                @elseif($booking->status === 'confirmed')
                                    <span class="px-2 py-0.5 border text-[10px] font-bold rounded uppercase bg-green-500/10 text-green-400 border-green-500/20">Đã xác nhận</span>
                                @elseif($booking->status === 'cancelled')
                                    <span class="px-2 py-0.5 border text-[10px] font-bold rounded uppercase bg-red-500/10 text-red-400 border-red-500/20">Đã hủy</span>
                                @elseif($booking->status === 'completed')
                                    <span class="px-2 py-0.5 border text-[10px] font-bold rounded uppercase bg-blue-500/10 text-blue-400 border-blue-500/20">Đã hoàn thành</span>
                                @endif
                            </div>
                        </div>
                        
                        {{-- Các nút hành động chỉ hiển thị cho lịch hẹn đang chờ duyệt --}}
                        @if($booking->status === 'pending')
                            <div class="flex gap-2 pt-1">
                                <button onclick="updateBookingStatus({{ $booking->id }}, 'cancelled', '{{ optional($booking->customer)->full_name }}')" class="flex-1 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white text-[11px] font-bold uppercase rounded-lg transition-colors text-center" style="font-family: 'Oswald', sans-serif;">
                                    Từ chối
                                </button>
                                <button onclick="updateBookingStatus({{ $booking->id }}, 'confirmed', '{{ optional($booking->customer)->full_name }}')" class="flex-1 py-2 bg-primary hover:bg-red-700 text-white text-[11px] font-bold uppercase rounded-lg transition-all text-center shadow-md shadow-primary/10" style="font-family: 'Oswald', sans-serif;">
                                    Chấp nhận
                                </button>
                            </div>
                        @endif
                    </div>
                    @empty
                    <p class="text-gray-500 text-xs text-center">Không có yêu cầu đặt lịch riêng nào đang chờ.</p>
                    @endforelse
                    <div class="bg-[#131313] p-3 rounded-xl border border-white/5 space-y-1 text-xs">
                        <p class="font-bold text-white uppercase text-[10px]" style="font-family: 'Oswald', sans-serif; tracking-wide">Sổ tay lưu ý ca trực PT</p>
                        <p class="text-[11px] text-gray-500 leading-relaxed">Kiểm tra kĩ form hông và siết core của idol Sơn Tùng trước khi duyệt ca hẹn và up tạ nặng bài kéo gầm chân.</p>
                    </div>
                </div>
            </div>

            <div class="bg-[#1A1A1A] rounded-2xl overflow-hidden border border-white/10 shadow-2xl relative h-48 group">
                <img class="w-full h-full object-cover opacity-40 transition-transform duration-500 group-hover:scale-105" src="https://images.unsplash.com/photo-1517838277536-f5f99be501cd?q=80&w=400" alt="PT Banner Motivation">
                <div class="absolute inset-0 bg-gradient-to-t from-[#131313] via-transparent to-transparent flex flex-col justify-end p-4">
                    <p class="font-['Oswald'] text-sm uppercase text-white font-bold tracking-wide">KOR Motivation Hub</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">"Kỷ luật là nền tảng vững chắc nhất đi tới mọi thành công hình thể."</p>
                </div>
            </div>
        </section>

    </div>
</main>
<div id="note-modal" class="fixed inset-0 z-[9999] bg-black/85 backdrop-blur-md hidden flex items-center justify-center p-4">
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 max-w-md w-full space-y-4 shadow-2xl">
        <h3 class="font-headline text-lg text-white uppercase tracking-wide font-bold border-b border-white/10 pb-3" style="font-family: 'Oswald', sans-serif;">Ghi Nhật Ký Huấn Luyện</h3>
        
        <form id="logForm" class="space-y-4">
            <div class="space-y-1">
                <label class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Đối tượng huấn luyện (Lớp/Học viên)</label>
                <select id="log-title" class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-xs text-white focus:ring-primary focus:border-primary outline-none">
                    <option value="">-- Chọn lớp hoặc học viên --</option>
                    @foreach($logTargets ?? [] as $target)
                        <option value="{{ $target }}">{{ $target }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Ngày ghi</label>
                    <input type="date" id="log-date" value="{{ date('Y-m-d') }}" class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-xs text-white outline-none">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Trạng thái</label>
                    <select id="log-status" class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-xs text-white outline-none">
                        <option value="completed">Hoàn thành</option>
                        <option value="upcoming">Sắp tới</option>
                        <option value="draft">Bản nháp</option>
                    </select>
                </div>
            </div>
            
            <div class="space-y-1">
                <label class="text-[10px] text-gray-400 uppercase font-bold tracking-wider">Nội dung chi tiết giáo án/nhận xét</label>
                <textarea id="log-content" rows="4" class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-sm text-white focus:ring-1 focus:ring-primary placeholder:text-gray-600 outline-none break-words whitespace-normal" placeholder="Ví dụ: Hoàn thành 4 set Bench Press 80kg, học viên siết core tốt..."></textarea>
            </div>
            
            <div class="flex gap-2 pt-2">
                <button type="button" onclick="closeNoteModal()" class="flex-1 py-3 bg-white/5 hover:bg-white/10 text-white text-xs font-bold uppercase rounded-xl border border-white/10 transition-colors">Hủy</button>
                <button type="button" onclick="saveTrainingLog()" class="flex-1 py-3 bg-primary hover:bg-red-700 text-white text-xs font-bold uppercase rounded-xl transition-colors tracking-wide font-headline" style="font-family: 'Oswald', sans-serif;">Lưu nhật ký</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        loadTrainingLogs();
    });

    async function loadTrainingLogs() {
        const container = document.getElementById('logs-list-container');
        try {
            const res = await fetch('{{ route("pt.dashboard") }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await res.json();
            if (result.success) {
                if (result.html.trim() === '') {
                    container.innerHTML = `
                        <div id="empty-logs-placeholder" class="text-center py-8">
                            <span class="material-symbols-outlined text-4xl text-gray-700 block mb-2">history_edu</span>
                            <p class="text-gray-500 text-xs italic">Chưa có nhật ký huấn luyện nào được ghi nhận.</p>
                        </div>`;
                } else {
                    container.innerHTML = result.html;
                }
            }
        } catch (e) {
            console.error('Error loading logs:', e);
        }
    }

    function openNoteModal(title = '') {
        document.getElementById('logForm').reset();
        if(title) document.getElementById('log-title').value = title;
        document.getElementById('note-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeNoteModal() {
        document.getElementById('note-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    async function saveTrainingLog() {
        const data = {
            title: document.getElementById('log-title').value,
            content: document.getElementById('log-content').value,
            log_date: document.getElementById('log-date').value,
            status: document.getElementById('log-status').value,
            _token: document.querySelector('meta[name="csrf-token"]').content
        };
        if(!data.title || !data.content) return alert("Vui lòng nhập đủ tiêu đề và nội dung!");
        try {
            const res = await fetch('{{ route("pt.logs.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify(data)
            });
            const result = await res.json();
            if(res.ok) {
                
                closeNoteModal();
                document.getElementById('logForm').reset(); // Reset form sau khi lưu
                alert(result.message); // Thêm thông báo thành công

                // Tải lại toàn bộ danh sách nhật ký để đảm bảo cập nhật và sắp xếp đúng
                await loadTrainingLogs();
            } else {
                let errorMessage = "Không thể lưu nhật ký.";
                if (result.message) { // Laravel's default validation error message (e.g., 422)
                    errorMessage = result.message;
                } else if (result.errors) { // Specific validation errors object
                    errorMessage = Object.values(result.errors).flat().join('\n'); // Join all error messages
                }
                alert("Lỗi: " + errorMessage);
            }
        } catch(e) { console.error(e); }
    }

    // New function to handle booking status update
    async function updateBookingStatus(bookingId, status, customerName) {
        if (!confirm(`Bạn có chắc chắn muốn ${status === 'confirmed' ? 'chấp nhận' : 'từ chối'} lịch hẹn của ${customerName} không?`)) {
            return;
        }

        try {
            const response = await fetch(`/pt/bookings/${bookingId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', // Ensure CSRF token is included
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ status: status })
            });

            const result = await response.json();

            if (response.ok) {
                alert(result.message);
                location.reload(); // Reload the page to reflect changes
            } else {
                alert('Lỗi: ' + (result.message || 'Không thể cập nhật trạng thái.'));
            }
        } catch (error) {
            console.error('Error updating booking status:', error);
            alert('Đã xảy ra lỗi khi cập nhật trạng thái lịch hẹn.');
        }
    }
</script>
