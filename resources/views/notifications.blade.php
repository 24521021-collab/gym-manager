@extends('layout.frontend')
@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <header class="border-b border-white/10 pb-4 flex justify-between items-center">
        <div>
            <h1 class="font-headline text-2xl text-white uppercase tracking-tight">Trung tâm thông báo</h1>
            <p class="text-xs text-gray-400 mt-1">Cập nhật nhắc lịch tập luyện và trạng thái đơn hàng của bạn</p>
        </div>
        <div class="flex gap-4">
            @if($notifications->where('is_read', false)->count() > 0)
                <button onclick="markAllAsRead()" class="text-[10px] text-primary font-bold hover:underline uppercase">Đánh dấu đã đọc</button>
            @endif
            <form action="{{ route('notifications.clearRead') }}" method="POST" onsubmit="return confirm('Dọn dẹp tất cả thông báo đã đọc?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-[10px] text-gray-500 font-bold hover:text-white uppercase">Dọn dẹp hộp thư</button>
            </form>
        </div>
    </header>

    <div class="space-y-3" id="notification-box">
        @forelse($notifications as $noti)
            @php
                $icon = 'notifications';
                $color = 'primary';
                if($noti->type == 'booking') { $icon = 'calendar_month'; $color = 'primary'; }
                if($noti->type == 'order') { $icon = 'local_shipping'; $color = 'blue-500'; }
                if($noti->type == 'class') { $icon = 'school'; $color = 'emerald-500'; }
                if($noti->type == 'membership') { $icon = 'warning'; $color = 'yellow-500'; }
            @endphp
            <div id="noti-{{ $noti->id }}" class="noti-item bg-[#1A1A1A] border-l-4 border-{{ $color }} p-4 rounded-r-xl border border-white/5 flex gap-4 items-start transition-all {{ $noti->is_read ? 'opacity-50' : '' }}">
                <span class="material-symbols-outlined text-{{ $color }} bg-{{ $color }}/10 p-2 rounded-lg">{{ $icon }}</span>
                <div class="flex-grow space-y-1">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xs font-bold text-white uppercase">{{ $noti->title }}</h3>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] text-gray-500 font-mono">{{ $noti->created_at->diffForHumans() }}</span>
                            <button onclick="deleteNotification({{ $noti->id }})" class="text-gray-600 hover:text-primary transition-colors">
                                <span class="material-symbols-outlined text-sm">delete</span>
                            </button>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400">{{ $noti->content }}</p>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 py-10 italic">Bạn chưa có thông báo nào.</p>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
</div>

<script>
    // Hàm xử lý gửi yêu cầu đánh dấu đã đọc qua AJAX
    async function markAllAsRead() {
        const response = await fetch("{{ route('notifications.markAllRead') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        if(response.ok) {
            document.querySelectorAll('.noti-item').forEach(item => {
                item.classList.add('opacity-50');
                item.style.borderLeftColor = 'rgba(255,255,255,0.1)';
            });
        }
    }

    // Hàm xóa thông báo tức thì (Bảo mật: Server sẽ kiểm tra user_id)
    async function deleteNotification(id) {
        if(!confirm('Xóa thông báo này?')) return;
        
        const response = await fetch(`/notifications/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        if(response.ok) {
            const element = document.getElementById(`noti-${id}`);
            element.style.transform = 'translateX(20px)';
            element.style.opacity = '0';
            setTimeout(() => element.remove(), 300);
        }
    }
</script>
@endsection