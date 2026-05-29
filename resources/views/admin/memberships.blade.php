@extends('layout.admin_layout')
@section('content')
    <header>
        <h2 class="font-headline text-3xl text-white uppercase tracking-tight">QUẢN LÝ HỘI VIÊN</h2>
        <p class="text-gray-400 text-xs mt-1">Giám sát trạng thái gói tập và gửi thông báo gia hạn cho khách hàng</p>
    </header>

    <div class="bg-[#1A1A1A] border border-white/10 rounded-xl p-4 shadow-md mb-4 mt-4">
        <form id="searchForm" onsubmit="event.preventDefault(); loadMemberships();" class="grid grid-cols-1 md:grid-cols-7 gap-4">
            <div class="md:col-span-6">
                <input type="text" id="searchInput" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-primary transition-colors" placeholder="Tìm tên hoặc email hội viên...">
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
                        <th class="p-4 pl-6">Hội viên</th>
                        <th class="p-4">Hạng gói</th>
                        <th class="p-4">Ngày bắt đầu</th>
                        <th class="p-4">Ngày hết hạn</th>
                        <th class="p-4 text-center">Trạng thái</th>
                        <th class="p-4 text-right pr-6">Thông báo</th>
                    </tr>
                </thead>
                <tbody id="membershipTableBody" class="divide-y divide-white/5">
                    <tr><td colspan="6" class="p-10 text-center text-gray-500 italic">Đang tải dữ liệu hội viên từ hệ thống...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="bg-black/20 border-t border-white/10 py-4 flex justify-center">
            <div id="membershipPagination"></div>
        </div>
    </div>

<script>
function formatDate(dateString) {
    if (!dateString) return '';
    const d = new Date(dateString);
    if (isNaN(d.getTime())) return dateString;
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

function escapeHtml(text) {
    if (!text) return '';
    return text.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

function loadMemberships(page = 1) {
    const searchKeyword = document.getElementById('searchInput').value;
    let url = "{{ route('memberships.index') }}"; 
    const params = new URLSearchParams();
    if (searchKeyword) params.append('search', searchKeyword);
    if (page > 1) params.append('page', page);
    if (params.toString()) url += `?${params.toString()}`;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(res => res.json())
    .then(data => {
        const memberships = data.data; 
        const tbody = document.getElementById('membershipTableBody');
        
        if(!memberships || memberships.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="p-10 text-center text-gray-500 italic">Không tìm thấy hồ sơ đăng ký nào.</td></tr>`;
            document.getElementById('membershipPagination').innerHTML = '';
            return;
        }
        
        let html = '';
        const today = new Date();
        memberships.forEach(m => {
            const userName = m.user ? escapeHtml(m.user.full_name || m.user.name) : 'N/A';
            const userEmail = m.user ? escapeHtml(m.user.email) : '';
            const packageName = m.package ? escapeHtml(m.package.package_name) : 'N/A';
            
            let statusBadge = '';
            let endDate = new Date(m.end_date);
            let diffTime = endDate.getTime() - today.getTime();
            let diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if(m.status === 'Active' && diffDays <= 7 && diffDays > 0) {
                statusBadge = '<span class="px-2 py-0.5 border text-[10px] font-bold rounded uppercase bg-orange-500/10 text-orange-400 border-orange-500/20">Sắp hết hạn</span>';
            } else if(m.status === 'Active') {
                statusBadge = '<span class="px-2 py-0.5 border text-[10px] font-bold rounded uppercase bg-green-500/10 text-green-400 border-green-500/20">Đang hoạt động</span>';
            } else if(m.status === 'Expired') {
                statusBadge = '<span class="px-2 py-0.5 border text-[10px] font-bold rounded uppercase bg-red-500/10 text-red-400 border-red-500/20">Đã hết hạn</span>';
            } else if(m.status === 'Cancelled') {
                statusBadge = '<span class="px-2 py-0.5 border text-[10px] font-bold rounded uppercase bg-gray-500/10 text-gray-400 border-gray-500/20">Đã hủy</span>';
            } else {
                statusBadge = `<span class="px-2 py-0.5 border text-[10px] font-bold rounded uppercase bg-gray-500/10 text-gray-400 border-gray-500/20">${escapeHtml(m.status)}</span>`;
            }

            html += `
                <tr class="hover:bg-white/5 transition-colors" id="membership-row-${m.id}">
                    <td class="p-4 pl-6 font-bold text-white">
                        ${userName} <br>
                        <span class="text-gray-400 text-xs">${userEmail}</span>
                    </td>
                    <td class="p-4 text-gray-300 font-bold">${packageName}</td>
                    <td class="p-4 text-gray-400 text-xs font-mono">${formatDate(m.start_date)}</td>
                    <td class="p-4 text-gray-400 text-xs font-mono">${formatDate(m.end_date)}</td>
                    <td class="p-4 text-center">${statusBadge}</td>
                    <td class="p-4 text-right pr-6">
                        <button onclick="sendExpirationNotification(${m.id})" class="bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 text-xs font-bold uppercase px-3 py-1.5 rounded-lg transition-all">
                            <span class="material-symbols-outlined text-sm align-middle">notifications</span> Gửi thông báo
                        </button>
                    </td>
                </tr>`;
        });
        
        tbody.innerHTML = html;
        window.cachedMemberships = memberships;
        renderPagination(data.links); 
    })
    .catch(err => {
        document.getElementById('membershipTableBody').innerHTML = `<tr><td colspan="6" class="p-10 text-center text-red-500 italic">Lỗi tải dữ liệu: ${err.message}</td></tr>`;
    });
}

function renderPagination(links) {
    const container = document.getElementById('membershipPagination');
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
            return `<a href="#" onclick="event.preventDefault(); loadMemberships(${page})" class="bg-black/40 text-gray-400 border border-white/10 hover:bg-white/5 hover:text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-all">${link.label}</a>`;
        }).join('') + `</div>`;
}

async function sendExpirationNotification(membershipId) {
    if (!confirm("Bạn có chắc chắn muốn gửi thông báo gia hạn cho hội viên này không?")) return;

    const url = "{{ route('admin.members.sendExpirationNotification', ':id') }}".replace(':id', membershipId);

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();

        if (data.success) {
            alert(data.message);
        } else {
            alert(data.message || "Có lỗi xảy ra khi gửi thông báo.");
        }
    } catch (error) {
        console.error('Lỗi gửi thông báo:', error);
        alert("Không thể kết nối đến máy chủ để gửi thông báo.");
    }
}

document.addEventListener('DOMContentLoaded', () => loadMemberships());
</script>
@endsection
                       