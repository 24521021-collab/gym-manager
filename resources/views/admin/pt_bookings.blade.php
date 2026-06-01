@extends('layout.admin_layout')
@section('content')
    <header>
        <h2 class="font-headline text-3xl text-white uppercase tracking-tight">ĐIỀU PHỐI LỊCH HẸN HLV</h2>
        <p class="text-gray-400 text-xs mt-1">Giám sát các ca đặt lịch tập PT 1-kèm-1 của khách hàng</p>
    </header>

    <div class="bg-[#1A1A1A] border border-white/10 rounded-xl p-4 shadow-md mb-4 mt-4">
        <form id="bookingSearchForm" onsubmit="event.preventDefault(); loadBookings();" class="grid grid-cols-1 md:grid-cols-7 gap-4">
            <div class="md:col-span-3">
                <input type="text" id="bookingSearchInput" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-primary transition-colors" placeholder="Tìm tên PT hoặc hội viên...">
            </div>
            <div class="md:col-span-3">
                <select id="bookingStatusFilter" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-primary transition-colors [color-scheme:dark]" onchange="loadBookings()">
                    <option value="" class="bg-[#1A1A1A]">-- Tất cả trạng thái --</option>
                    <option value="pending" class="bg-[#1A1A1A]">Đang chờ duyệt</option>
                    <option value="confirmed" class="bg-[#1A1A1A]">Đã xác nhận</option>
                    <option value="completed" class="bg-[#1A1A1A]">Hoàn thành</option>
                    <option value="cancelled" class="bg-[#1A1A1A]">Đã hủy</option>
                </select>
            </div>
            <div class="md:col-span-1">
                <button type="submit" class="w-full h-full bg-primary hover:bg-red-700 text-white text-xs font-bold uppercase tracking-wider py-2.5 rounded-xl transition-all shadow-md shadow-primary/10">Tìm kiếm</button>
            </div>
        </form>
    </div>

    <div class="bg-[#1A1A1A] rounded-xl border border-white/10 overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="text-gray-500 font-bold uppercase text-[10px] tracking-wider border-b border-white/10 bg-black/40">
                        <th class="p-4 pl-6">Huấn Luyện Viên</th>
                        <th class="p-4">Hội Viên Đặt Lịch</th>
                        <th class="p-4">Khung Giờ</th>
                        <th class="p-4">Ngày Hẹn</th>
                        <th class="p-4 text-right pr-6">Trạng Thái Ca Tập</th>
                    </tr>
                </thead>
                <tbody id="bookingTableBody" class="divide-y divide-white/5">
                    <tr><td colspan="5" class="p-10 text-center text-gray-500 italic">Đang tải dữ liệu...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="bg-black/20 border-t border-white/10 py-4 flex justify-center">
            <div id="bookingPagination"></div>
        </div>
    </div>

<script>
function escapeHtml(text) {
    if (!text) return '';
    return text.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function loadBookings(page = 1) {
    const search = document.getElementById('bookingSearchInput').value;
    const status = document.getElementById('bookingStatusFilter').value;
    let url = "{{ route('admin.pt-bookings') }}" + `?page=${page}&search=${encodeURIComponent(search)}&status=${status}`;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            renderBookingTable(data.data);
            renderPagination(data.links);
        })
        .catch(err => console.error("Lỗi tải lịch hẹn:", err));
}

function renderBookingTable(bookings) {
    const tbody = document.getElementById('bookingTableBody');
    if (!bookings.length) {
        tbody.innerHTML = '<tr><td colspan="5" class="p-10 text-center text-gray-500 italic">Không có lịch đặt nào được tìm thấy.</td></tr>';
        return;
    }

    const statusConfig = {
        pending:   { class: 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20', label: 'Đang chờ duyệt' },
        confirmed: { class: 'bg-green-500/10 text-green-400 border-green-500/20', label: 'Đã xác nhận' },
        cancelled: { class: 'bg-red-500/10 text-red-400 border-red-500/20', label: 'Đã hủy' },
        completed: { class: 'bg-blue-500/10 text-blue-400 border-blue-500/20', label: 'Hoàn thành' }
    };

    tbody.innerHTML = bookings.map(booking => {
        const status = statusConfig[booking.status] || { class: 'bg-gray-500/10 text-gray-400 border-gray-500/20', label: booking.status };
        const ptName = booking.pt ? booking.pt.full_name : 'Không xác định';
        const customerName = booking.customer ? booking.customer.full_name : 'N/A';
        
        // Định dạng thời gian
        const startTime = booking.start_time.substring(0, 5);
        const endTime = booking.end_time.substring(0, 5);
        const bookingDate = new Date(booking.booking_date).toLocaleDateString('vi-VN', {
            weekday: 'long', year: 'numeric', month: '2-digit', day: '2-digit'
        });

        return `
            <tr class="hover:bg-white/5 transition-colors">
                <td class="p-4 pl-6 font-bold text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                    ${escapeHtml(ptName)}
                </td>
                <td class="p-4 text-gray-300 font-bold">${escapeHtml(customerName)}</td>
                <td class="p-4 text-gray-400 text-xs font-mono">
                    ${startTime} - ${endTime}
                </td>
                <td class="p-4 text-gray-400 text-xs font-mono uppercase">
                    ${bookingDate}
                </td>
                <td class="p-4 text-right pr-6">
                    <span class="px-2 py-0.5 border text-[10px] font-bold rounded uppercase ${status.class}">
                        ${status.label}
                    </span>
                </td>
            </tr>`;
    }).join('');
}

function renderPagination(links) {
    const container = document.getElementById('bookingPagination');
    if (!links || links.length <= 3) { container.innerHTML = ''; return; }
    
    container.innerHTML = `<div class="flex items-center gap-1">` +
        links.map(link => {
            const page = link.url ? new URL(link.url).searchParams.get('page') : 1;
            if (!link.url) {
                return `<span class="bg-black/20 text-gray-600 border border-white/5 text-xs font-bold px-3 py-1.5 rounded-lg opacity-40 cursor-not-allowed">${link.label}</span>`;
            }
            if (link.active) {
                return `<span class="bg-primary text-white text-xs font-bold px-3 py-1.5 rounded-lg border border-primary">${link.label}</span>`;
            }
            return `<a href="#" onclick="event.preventDefault(); loadBookings(${page})" class="bg-black/40 text-gray-400 border border-white/10 hover:bg-white/5 hover:text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-all">${link.label}</a>`;
        }).join('') + `</div>`;
}

document.addEventListener('DOMContentLoaded', () => loadBookings());
</script>
@endsection
