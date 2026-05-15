@extends('layout.admin_layout')
@section('content')
<div class="container-fluid py-4">
    <h4 class="fw-bold mb-4">Quản lý Đơn hàng</h4>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.orders') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Mã đơn hoặc tên khách..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Chờ thanh toán (Pending)</option>
                        <option value="Paid" {{ request('status') == 'Paid' ? 'selected' : '' }}>Đã thanh toán (Paid)</option>
                        <option value="Cancelled" {{ request('status') == 'Cancelled' ? 'selected' : '' }}>Đã hủy (Cancelled)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Lọc đơn</button>
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
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-4">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="ps-4">#{{ $order->id }}</td>
                        <td>{{ $order->user->full_name ?? 'Khách lạ' }}</td>
                        <td>{{ date('d/m/Y H:i', strtotime($order->order_date)) }}</td>
                        <td class="fw-bold text-primary">{{ number_format($order->total_amount) }}đ</td>
                        <td>
                            @if($order->payment_status == 'Paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @elseif($order->payment_status == 'Cancelled')
                                <span class="badge bg-danger">Đã hủy</span>
                            @else
                                <span class="badge bg-warning text-dark">Chờ thanh toán</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-dark" 
                            onclick="openOrderDetail({{ $order->id }})"
                            data-bs-toggle="modal" data-bs-target="#orderDetailModal">
                             Chi tiết & Trạng thái
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
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
                <h6><b>Khách hàng:</b> <span id="det_user_name"></span></h6>
                <hr>
                <label class="fw-bold mb-2">Sản phẩm đã mua:</label>
                <table class="table table-bordered align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 70px;">Ảnh sản phẩm </th>
                            <th>Sản phẩm</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody id="order_items_list">
                        </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Tổng cộng:</th>
                            <th class="text-end text-danger" id="det_total_price"></th>
                        </tr>
                    </tfoot>
                </table>

                <form id="updateOrderForm" method="POST">
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
function openOrderDetail(orderId) {
    // 1. Dùng Fetch API để gọi hàm show() trong Controller
    fetch(`/admin/orders/${orderId}`)
        .then(response => response.json())
        .then(order => {
            // 2. Đổ thông tin cơ bản
            document.getElementById('det_order_id').innerText = order.id;
            document.getElementById('det_user_name').innerText = order.user ? order.user.full_name : 'Khách vãng lai';
            document.getElementById('det_total_price').innerText = new Intl.NumberFormat('vi-VN').format(order.total_amount) + 'đ';
            document.getElementById('det_status_select').value = order.payment_status;
            document.getElementById('updateOrderForm').action = `/admin/orders/update-status/${order.id}`;
            // 3. Vẽ danh sách sản phẩm
            let itemsHtml = '';
            order.items.forEach(item => {
                let productName = item.product ? item.product.name : 'Sản phẩm đã xóa';
                // Kiểm tra nếu sản phẩm có ảnh, nếu không thì dùng ảnh mặc định
                 let productImg = (item.product && item.product.image) ? `/images/products/${item.product.image}` : '/images/products/default-product.jpg';
                 // Tính đơn giá dựa trên thành tiền và số lượng vì DB không lưu cột price riêng lẻ
                 let unitPrice = item.quantity > 0 ? (item.subtotal / item.quantity) : 0;
                itemsHtml += `
                    <tr>
                        <td class="text-center">
                        <img src="${productImg}" width="50" height="50" class="rounded border" style="object-fit: cover;">
                        </td>
                        <td>${productName}</td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-end">${new Intl.NumberFormat('vi-VN').format(unitPrice)}đ</td>
                        <td class="text-end fw-bold">${new Intl.NumberFormat('vi-VN').format(item.subtotal)}đ</td>
                    </tr>
                `;
            });
            document.getElementById('order_items_list').innerHTML = itemsHtml;
            // 4. Mở Modal
            var myModal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
            myModal.show();
        });
}
</script>
@endsection