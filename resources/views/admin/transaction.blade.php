
@extends('layout.admin_layout')
@section('content')
<header>
    <h2 class="font-headline text-3xl text-white uppercase tracking-tight">Lịch sử giao dịch</h2>
    <p class="text-gray-400 text-xs mt-1">Giám sát dòng tiền, doanh thu hóa đơn mua sắm và bộ lọc trạng thái đơn hàng</p>
</header>

<div class="flex flex-wrap gap-2 mb-4">
    <button class="filter-type-btn px-4 py-2.5 text-xs font-bold bg-primary text-white rounded-xl transition-all active" onclick="filterByType('', this)">Tất cả đơn hàng</button>
    <button class="filter-type-btn px-4 py-2.5 text-xs font-bold bg-[#1A1A1A] text-gray-400 hover:text-white border border-white/10 rounded-xl transition-all flex items-center gap-2" onclick="filterByType('product', this)">
            <span class="material-symbols-outlined text-sm">inventory_2</span> Đơn hàng Sản phẩm
    </button>
    <button class="filter-type-btn px-4 py-2.5 text-xs font-bold bg-[#1A1A1A] text-gray-400 hover:text-white border border-white/10 rounded-xl transition-all flex items-center gap-2" onclick="filterByType('package', this)">
            <span class="material-symbols-outlined text-sm">badge</span> Đơn hàng Gói tập
    </button>
    <button class="filter-type-btn px-4 py-2.5 text-xs font-bold bg-[#1A1A1A] text-gray-400 hover:text-white border border-white/10 rounded-xl transition-all flex items-center gap-2" onclick="filterByType('class', this)">
            <span class="material-symbols-outlined text-sm">school</span> Đơn hàng Lớp học
    </button>
</div>

    <div class="bg-[#1A1A1A] border border-white/10 rounded-xl p-4 shadow-md">
        <form id="orderSearchForm" onsubmit="event.preventDefault(); loadOrders();" class="grid grid-cols-1 md:grid-cols-7 gap-4">
            <div class="md:col-span-3">
                <input type="text" id="orderSearchInput" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-primary transition-colors" placeholder="Mã đơn hoặc tên khách...">
            </div>
            <div class="md:col-span-3">
                <select id="orderStatusFilter" class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-primary transition-colors [color-scheme:dark]" onchange="loadOrders()">
                    <option value="" class="bg-[#1A1A1A]">-- Tất cả trạng thái --</option>
                    <option value="Pending" class="bg-[#1A1A1A]" {{ request('status') == 'Pending' ? 'selected' : '' }}>Chờ thanh toán (Pending)</option>
                    <option value="Paid" class="bg-[#1A1A1A]" {{ request('status') == 'Paid' ? 'selected' : '' }}>Đã thanh toán (Paid)</option>
                    <option value="Cancelled" class="bg-[#1A1A1A]" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
                </select>
            </div>
            <div class="md:col-span-1">
                <button type="submit" class="w-full h-full bg-primary hover:bg-red-700 text-white text-xs font-bold uppercase tracking-wider py-2.5 rounded-xl transition-all shadow-md shadow-primary/10">Tìm kiếm</button>
            </div>
        </form>
    </div>

    <div class="bg-[#1A1A1A] rounded-xl border border-white/10 overflow-hidden shadow-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap align-middle">
                <thead>
                    <tr class="text-gray-500 font-bold uppercase text-[10px] tracking-wider border-b border-white/10 bg-black/40">
                        <th class="p-4 pl-6">Mã đơn</th>
                        <th class="p-4">Khách hàng</th>
                        <th class="p-4">Hình thức</th>
                        <th class="p-4 text-center">Số lượng</th>
                        <th class="p-4 text-right">Tổng tiền</th>
                        <th class="p-4">Trạng thái</th>
                        <th class="p-4 text-right pr-6">Thời gian</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody" class="divide-y divide-white/5">
                    <tr><td colspan="7" class="text-center py-8 text-gray-400 italic">Đang tải dữ liệu đơn hàng...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="bg-black/20 border-t border-white/10 py-4 flex justify-center">
            <div id="orderPagination"></div>
        </div>
    </div>
</div>

<div id="orderDetailModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/70 backdrop-blur-sm transition-all">
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden transform scale-100 transition-transform">
        <div class="px-6 py-4 bg-black/40 border-b border-white/10 flex justify-between items-center">
            <h5 class="font-headline text-sm text-white uppercase tracking-wider flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-base">receipt_long</span>
                Chi tiết đơn hàng #<span id="det_order_id"></span>
            </h5>
            <button type="button" onclick="closeOrderDetailModal()" class="text-gray-400 hover:text-white transition-colors flex items-center">
                <span class="material-symbols-outlined text-xl">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pb-3 border-b border-white/5 text-xs uppercase font-bold tracking-wider">
                <div class="text-gray-400">Khách hàng: <span id="det_user_name" class="text-white ms-1"></span></div>
                <div class="text-gray-400">Hình thức: <span id="det_payment_method" class="text-white ms-1"></span></div>
                <div class="text-gray-400">Trạng thái hiện tại: <span id="det_status_badge" class="ms-1"></span></div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-white/5 bg-black/20">
                <table class="w-full text-left border-collapse whitespace-nowrap text-xs">
                    <thead>
                        <tr class="text-gray-500 font-bold uppercase text-[10px] tracking-wider border-b border-white/5 bg-black/40">
                            <th class="p-3 pl-4">Hạng mục / Loại</th>
                            <th class="p-3 text-center">Số lượng</th>
                            <th class="p-3 text-right">Đơn giá</th>
                            <th class="p-3 text-right pr-4">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="det_items_body" class="divide-y divide-white/5 text-gray-300"></tbody>
                </table>
            </div>

            <div class="flex justify-between items-center p-4 bg-black/40 border border-white/5 rounded-xl">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">TỔNG CỘNG THANH TOÁN:</span>
                <span class="text-primary font-bold text-lg font-mono" id="det_total_price"></span>
            </div>

            <form id="updateOrderForm" onsubmit="saveOrderStatus(event)">
                @csrf @method('PUT')
                <div class="p-4 bg-black/20 border border-white/5 rounded-xl space-y-3">
                    <label class="block text-xs font-bold text-primary uppercase tracking-wider">Cập nhật trạng thái mới:</label>
                    <div class="flex gap-3">
                        <select name="payment_status" id="det_status_select" class="flex-1 bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-primary transition-colors [color-scheme:dark]">
                            <option value="Pending" class="bg-[#1A1A1A]">Chờ thanh toán</option>
                            <option value="Paid" class="bg-[#1A1A1A]">Đã thanh toán</option>
                            <option value="Cancelled" class="bg-[#1A1A1A]">Hủy đơn hàng</option>
                        </select>
                        <button type="submit" class="bg-primary hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider px-6 py-2.5 rounded-xl transition-all shadow-lg shadow-primary/20">Lưu thông tin</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
window.cachedOrders = [];
let currentTypeFilter = '';

// Cập nhật hàm lọc theo loại sang style Tailwind mới
function filterByType(type, btn) {
    currentTypeFilter = type;
    document.querySelectorAll('.filter-type-btn').forEach(b => {
        b.className = "filter-type-btn px-4 py-2.5 text-xs font-bold bg-[#1A1A1A] text-gray-400 hover:text-white border border-white/10 rounded-xl transition-all flex items-center gap-2";
    });
    btn.className = "filter-type-btn px-4 py-2.5 text-xs font-bold bg-primary text-white rounded-xl transition-all flex items-center gap-2";
    loadOrders(1);
}

// 1. Hàm tải dữ liệu (LoadData)
function loadOrders(page = 1) {
    const search = document.getElementById('orderSearchInput').value;
    const status = document.getElementById('orderStatusFilter').value;
    let url = "{{ route('admin.transaction') }}" + `?page=${page}&search=${encodeURIComponent(search)}&status=${status}&type=${currentTypeFilter}`;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            window.cachedOrders = data.data; 
            renderOrderTable(window.cachedOrders);
            renderPagination(data.links);
        })
        .catch(err => console.error("Lỗi tải đơn hàng:", err));
}

// 2. Hàm vẽ bảng với thiết kế Dark Theme cao cấp
function renderOrderTable(orders) {
    const tbody = document.getElementById('orderTableBody');
    if (!orders.length) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-8 text-gray-400 italic">Không có dữ liệu đơn hàng.</td></tr>';
        return;
    }
    tbody.innerHTML = orders.map(order => {
        let statusBadge = '';
        if(order.payment_status === 'Paid') {
            statusBadge = '<span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Đã thanh toán</span>';
        } else if(order.payment_status === 'Cancelled') {
            statusBadge = '<span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md bg-rose-500/20 text-rose-400 border border-rose-500/30">Đã hủy</span>';
        } else {
            statusBadge = '<span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md bg-amber-500/20 text-amber-400 border border-amber-500/30">Chờ thanh toán</span>';
        }

        let methodBadge = '';
        if(order.payment_method === 'Bank_QR') {
            methodBadge = '<span class="text-[10px] font-bold text-cyan-400 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">qr_code_2</span> VietQR</span>';
        } else {
            methodBadge = '<span class="text-[10px] font-bold text-gray-400 flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">payments</span> Tiền mặt</span>';
        }

        const totalQty = order.items.reduce((sum, item) => sum + parseInt(item.quantity), 0);

        return `
            <tr id="order-row-${order.id}" class="hover:bg-white/5 transition-colors cursor-pointer" onclick="openOrderDetail(${order.id})">
                <td class="p-4 pl-6 font-mono font-bold text-white">#${order.id}</td>
                <td class="p-4 text-gray-300 font-bold">${escapeHtml(order.user ? order.user.full_name : 'Khách lạ')}</td>
                <td class="p-4">${methodBadge}</td>
                <td class="p-4 text-center text-gray-300">${totalQty} món</td>
                <td class="p-4 text-right font-bold text-primary-container font-mono">${new Intl.NumberFormat('vi-VN').format(order.total_amount)}đ</td>
                <td class="p-4">${statusBadge}</td>
                <td class="p-4 text-right pr-6 text-gray-400 font-mono text-xs">${new Date(order.order_date).toLocaleString('vi-VN')}</td>
            </tr>`;
    }).join('');
}

// 3. Hàm phân trang tối ưu class Tailwind
function renderPagination(links) {
    const container = document.getElementById('orderPagination');
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
            return `<a href="#" onclick="event.preventDefault(); loadOrders(${page})" class="bg-black/40 text-gray-400 border border-white/10 hover:bg-white/5 hover:text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-all">${link.label}</a>`;
        }).join('') + `</div>`;
}

// 4. Hàm xem chi tiết (Mở Modal dạng Tailwind)
function openOrderDetail(orderId) {
    const order = window.cachedOrders.find(o => o.id === orderId);
    if (!order) return;

    document.getElementById('det_order_id').innerText = order.id;
    document.getElementById('det_user_name').innerText = order.user ? order.user.full_name : 'Khách vãng lai';
    document.getElementById('det_total_price').innerText = new Intl.NumberFormat('vi-VN').format(order.total_amount) + 'đ';
    document.getElementById('det_payment_method').innerText = order.payment_method || 'Chưa xác định';
    document.getElementById('det_status_select').value = order.payment_status;
    
    let statusBadge = '';
    if(order.payment_status === 'Paid') {
        statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Đã thanh toán (Paid)</span>';
    } else if(order.payment_status === 'Cancelled') {
        statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-rose-500/20 text-rose-400 border border-rose-500/30">Đã hủy (Cancelled)</span>';
    } else {
        statusBadge = '<span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded bg-amber-500/20 text-amber-400 border border-amber-500/30">Chờ thanh toán (Pending)</span>';
    }
    document.getElementById('det_status_badge').innerHTML = statusBadge;

    const itemsHtml = order.items.map(item => {
        const itemName = item.name || (item.product ? item.product.name : 'Mặt hàng');
        let typeBadge = '';
        if(item.item_type === 'package') {
            typeBadge = '<span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-cyan-500/20 text-cyan-400 border border-cyan-500/30 ms-2">Gói tập</span>';
        } else if(item.item_type === 'class') {
            typeBadge = '<span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-blue-500/20 text-blue-400 border border-blue-500/30 ms-2">Lớp học</span>';
        } else {
            typeBadge = '<span class="px-1.5 py-0.5 text-[9px] font-bold uppercase rounded bg-gray-500/20 text-gray-400 border border-gray-500/30 ms-2">Sản phẩm</span>';
        }

        const unitPrice = item.quantity > 0 ? (item.subtotal / item.quantity) : item.price;
        
        return `<tr class="hover:bg-white/5 transition-colors">
            <td class="p-3 pl-4">
                <div class="font-bold text-white">${escapeHtml(itemName)}</div>
                <div class="mt-1">${typeBadge}</div>
            </td>
            <td class="p-3 text-center text-gray-300 font-mono">${item.quantity}</td>
            <td class="p-3 text-right text-gray-400 font-mono">${new Intl.NumberFormat('vi-VN').format(unitPrice)}đ</td>
            <td class="p-3 text-right font-bold text-primary font-mono">${new Intl.NumberFormat('vi-VN').format(item.subtotal)}đ</td>
        </tr>`;
    }).join('');

    document.getElementById('det_items_body').innerHTML = itemsHtml;

    // Kích hoạt hiển thị Modal dạng Tailwind thuần
    const modal = document.getElementById('orderDetailModal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Hàm đóng Modal điều hướng bằng ClassList
function closeOrderDetailModal() {
    const modal = document.getElementById('orderDetailModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// 5. Hàm cập nhật trạng thái (Đóng modal chuẩn hóa thuần)
function saveOrderStatus(event) {
    event.preventDefault();
    const orderId = document.getElementById('det_order_id').innerText;
    const status = document.getElementById('det_status_select').value;

    const url = "{{ route('admin.orders.updateStatus', ['id' => ':id']) }}".replace(':id', orderId);
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ payment_status: status, _method: 'PUT' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            closeOrderDetailModal(); // Đóng modal qua hàm thuần
            loadOrders(); 
            alert(data.message);
        }
    })
    .catch(err => alert('Có lỗi xảy ra khi cập nhật trạng thái!'));
}

document.addEventListener('DOMContentLoaded', () => loadOrders());

function escapeHtml(text) {
    if (!text) return '';
    return text.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}
</script>
@endsection