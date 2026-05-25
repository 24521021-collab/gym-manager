@extends('layout.admin_layout')
@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4">Quản lý Đơn hàng</h4>

    <div class="d-flex gap-2 mb-4">
        <button class="btn btn-dark filter-type-btn active" onclick="filterByType('', this)">Tất cả đơn hàng</button>
        <button class="btn btn-outline-secondary filter-type-btn" onclick="filterByType('product', this)"><i class="fas fa-box me-1"></i> Đơn hàng Sản phẩm</button>
        <button class="btn btn-outline-info filter-type-btn" onclick="filterByType('package', this)"><i class="fas fa-id-card me-1"></i> Đơn hàng Gói tập</button>
        <button class="btn btn-outline-primary filter-type-btn" onclick="filterByType('class', this)"><i class="fas fa-chalkboard-teacher me-1"></i> Đơn hàng Lớp học</button>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form id="orderSearchForm" onsubmit="event.preventDefault(); loadOrders();" class="row g-3">
                <div class="col-md-3">
                    <input type="text" id="orderSearchInput" class="form-control" placeholder="Mã đơn hoặc tên khách...">
                </div>
                <div class="col-md-3">
                    <select id="orderStatusFilter" class="form-select" onchange="loadOrders()">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Chờ thanh toán (Pending)</option>
                        <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Đã thanh toán (Paid)</option>
                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Ngày đặt</th>
                        <th class="text-center">Số lượng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="orderTableBody">
                    <tr><td colspan="6" class="text-center py-4">Đang tải dữ liệu đơn hàng...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div id="orderPagination" class="d-flex justify-content-center"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="orderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title">Chi tiết đơn hàng #<span id="det_order_id"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6"><b>Khách hàng:</b> <span id="det_user_name"></span></div>
                    <div class="col-md-6 text-md-end"><b>Trạng thái:</b> <span id="det_status_badge"></span></div>
                </div>

                <table class="table table-bordered align-middle">
                    <thead class="table-light small">
                        <tr>
                            <th>Hạng mục / Loại</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="det_items_body"></tbody>
                </table>

                <div class="d-flex justify-content-between align-items-center mt-3 p-2 bg-light rounded">
                    <span class="fw-bold">TỔNG CỘNG THANH TOÁN:</span>
                    <span class="text-danger fw-bold fs-5" id="det_total_price"></span>
                </div>

                <form id="updateOrderForm" onsubmit="saveOrderStatus(event)">
                    @csrf @method('PUT')
                    <div class="mt-4 p-3 bg-light rounded">
                        <label class="form-label fw-bold text-primary">Cập nhật trạng thái đơn hàng:</label>
                        <div class="d-flex gap-2">
                            <select name="payment_status" id="det_status_select" class="form-select">
                                <option value="Pending">Chờ thanh toán</option>
                                <option value="Paid">Đã thanh toán</option>
                                <option value="Cancelled">Hủy đơn hàng</option>
                            </select>
                            <button type="submit" class="btn btn-primary px-4">Lưu</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
window.cachedOrders = [];
let currentTypeFilter = '';

// Hàm lọc theo loại (3 nút bấm mới)
function filterByType(type, btn) {
    currentTypeFilter = type;
    document.querySelectorAll('.filter-type-btn').forEach(b => {
        b.classList.replace('btn-dark', 'btn-outline-secondary');
        b.classList.remove('active');
    });
    btn.classList.replace('btn-outline-secondary', 'btn-dark');
    btn.classList.add('active');
    loadOrders(1);
}

// 1. Hàm tải dữ liệu (LoadData)
function loadOrders(page = 1) {
    const search = document.getElementById('orderSearchInput').value;
    const status = document.getElementById('orderStatusFilter').value;
    let url = `/admin/orders?page=${page}&search=${encodeURIComponent(search)}&status=${status}&type=${currentTypeFilter}`;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => res.json())
        .then(data => {
            window.cachedOrders = data.data; // Lưu dữ liệu vào cache
            renderOrderTable(window.cachedOrders);
            renderPagination(data.links);
        })
        .catch(err => console.error("Lỗi tải đơn hàng:", err));
}

// 2. Hàm vẽ bảng (Render Table)
function renderOrderTable(orders) {
    const tbody = document.getElementById('orderTableBody');
    if (!orders.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4">Không có dữ liệu đơn hàng.</td></tr>';
        return;
    }
    tbody.innerHTML = orders.map(order => {
        let statusBadge = '';
        if(order.payment_status === 'Paid') statusBadge = '<span class="badge bg-success">Đã thanh toán</span>';
        else if(order.payment_status === 'Cancelled') statusBadge = '<span class="badge bg-danger">Đã hủy</span>';
        else statusBadge = '<span class="badge bg-warning text-dark">Chờ thanh toán</span>';

        const totalQty = order.items.reduce((sum, item) => sum + parseInt(item.quantity), 0);

        return `
            <tr id="order-row-${order.id}">
                <td class="ps-4">#${order.id}</td>
                <td>${escapeHtml(order.user ? order.user.full_name : 'Khách lạ')}</td>
                <td>${new Date(order.order_date).toLocaleString('vi-VN')}</td>
                <td class="text-center">${totalQty} món</td>
                <td class="fw-bold text-primary">${new Intl.NumberFormat('vi-VN').format(order.total_amount)}đ</td>
                <td>${statusBadge}</td>
                <td class="text-end pe-4">
                    <button class="btn btn-sm btn-outline-dark" onclick="openOrderDetail(${order.id})">
                        Chi tiết & Trạng thái
                    </button>
                </td>
            </tr>`;
    }).join('');
}

// 3. Hàm phân trang (Render Pagination)
function renderPagination(links) {
    const container = document.getElementById('orderPagination');
    if (!links || links.length <= 3) { container.innerHTML = ''; return; }
    container.innerHTML = `<nav><ul class="pagination pagination-sm mb-0">` +
        links.map(link => {
            const page = link.url ? new URL(link.url).searchParams.get('page') : 1;
            return `<li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${link.url && !link.active ? `loadOrders(${page})` : ''}">${link.label}</a>
            </li>`;
        }).join('') + `</ul></nav>`;
}

// 4. Hàm xem chi tiết (PrepareEdit - Sử dụng Cached Data)
function openOrderDetail(orderId) {
    // Tìm đối tượng trong cache thay vì gọi API mới
    const order = window.cachedOrders.find(o => o.id === orderId);
    if (!order) return;

    document.getElementById('det_order_id').innerText = order.id;
    document.getElementById('det_user_name').innerText = order.user ? order.user.full_name : 'Khách vãng lai';
    document.getElementById('det_total_price').innerText = new Intl.NumberFormat('vi-VN').format(order.total_amount) + 'đ';
    document.getElementById('det_status_select').value = order.payment_status;
    
    // Hiển thị badge trạng thái trong modal
    let statusBadge = '';
    if(order.payment_status === 'Paid') statusBadge = '<span class="badge bg-success">Đã thanh toán (Paid)</span>';
    else if(order.payment_status === 'Cancelled') statusBadge = '<span class="badge bg-danger">Đã hủy (Cancelled)</span>';
    else statusBadge = '<span class="badge bg-warning text-dark">Chờ thanh toán (Pending)</span>';
    document.getElementById('det_status_badge').innerHTML = statusBadge;

    // Hiển thị danh sách item trong 1 bảng duy nhất (Khôi phục ban đầu)
    const itemsHtml = order.items.map(item => {
        const itemName = item.name || (item.product ? item.product.name : 'Mặt hàng');
        let typeBadge = '';
        if(item.item_type === 'package') typeBadge = '<span class="badge bg-info text-dark small ms-2">Gói tập</span>';
        else if(item.item_type === 'class') typeBadge = '<span class="badge bg-primary small ms-2">Lớp học</span>';
        else typeBadge = '<span class="badge bg-secondary small ms-2">Sản phẩm</span>';

        const unitPrice = item.quantity > 0 ? (item.subtotal / item.quantity) : item.price;
        
        return `<tr>
            <td>
                <div class="fw-bold">${escapeHtml(itemName)}</div>
                ${typeBadge}
            </td>
            <td class="text-center">${item.quantity}</td>
            <td class="text-end">${new Intl.NumberFormat('vi-VN').format(unitPrice)}đ</td>
            <td class="text-end fw-bold">${new Intl.NumberFormat('vi-VN').format(item.subtotal)}đ</td>
        </tr>`;
    }).join('');

    document.getElementById('det_items_body').innerHTML = itemsHtml;

    const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    modal.show();
}

// 5. Hàm cập nhật trạng thái (SaveData)
function saveOrderStatus(event) {
    event.preventDefault();
    const orderId = document.getElementById('det_order_id').innerText;
    const status = document.getElementById('det_status_select').value;
    
    fetch(`/admin/orders/update-status/${orderId}`, {
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
            bootstrap.Modal.getInstance(document.getElementById('orderDetailModal')).hide();
            loadOrders(); // Tải lại danh sách để cập nhật giao diện
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