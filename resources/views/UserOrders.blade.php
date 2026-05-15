@extends('layout.frontend')
@section('content')
<div class="container py-5">
    <h3 class="fw-bold mb-4">Đơn hàng của tôi</h3>
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Mã đơn</th>
                        <th>Ngày đặt</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr>
                        <td class="ps-3"><b>#{{ $order->id }}</b></td>
                        <td>{{ date('d/m/Y H:i', strtotime($order->order_date)) }}</td>
                        <td class="fw-bold">{{ number_format($order->total_amount) }}đ</td>
                        <td>
                            @if($order->payment_status == 'Paid')
                                <span class="badge bg-success">Đã thanh toán</span>
                            @elseif($order->payment_status == 'Cancelled')
                                <span class="badge bg-danger">Đã hủy</span>
                            @else
                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <button onclick="viewOrderDetails({{ $order->id }})" 
                            class="btn btn-sm btn-dark"
                            data-bs-toggle="modal" 
                            data-bs-target="#userOrderDetailModal"
                            >
                                Xem chi tiết
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
</div>

<div class="modal fade" id="userOrderDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Chi tiết đơn hàng #<span id="u_order_id"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="border-bottom">
                            <tr>
                                <th>Sản phẩm</th>
                                <th class="text-center">Số lượng</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody id="u_items_list">
                            </tbody>
                        <tfoot class="border-top">
                            <tr>
                                <th colspan="2" class="text-end px-3">Tổng cộng:</th>
                                <th class="text-end text-danger fs-5" id="u_total_price"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function viewOrderDetails(orderId) {
    // 1. Gọi AJAX lấy data
    fetch(`/orders/${orderId}`)
        .then(res => res.json())
        .then(order => {
            // 2. Đổ thông tin cơ bản
            document.getElementById('u_order_id').innerText = order.id;
            document.getElementById('u_total_price').innerText = new Intl.NumberFormat('vi-VN').format(order.total_amount) + 'đ';
            // 3. Vẽ danh sách sản phẩm
            let html = '';
            order.items.forEach(item => {
                let productName = item.product ? item.product.name : 'Sản phẩm không khả dụng';
                let img = (item.product && item.product.image) ? `/images/products/${item.product.image}` : '/images/products/default-product.jpg';
                // Tính đơn giá tạm thời từ subtotal vì order_items không lưu price
                let unitPrice = item.quantity > 0 ? (item.subtotal / item.quantity) : 0;

                html += `
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="${img}" width="50" class="rounded me-3">
                                <div>
                                    <div class="fw-bold">${productName}</div>
                                    <small class="text-muted">${new Intl.NumberFormat('vi-VN').format(unitPrice)}đ</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">${item.quantity}</td>
                        <td class="text-end fw-bold">${new Intl.NumberFormat('vi-VN').format(item.subtotal)}đ</td>
                    </tr>`;
            });
            document.getElementById('u_items_list').innerHTML = html;
            // 4. Mở Modal
            // 4. Mở Modal (Sửa lại để không bị chồng lớp nền)
            let modalElement = document.getElementById('userOrderDetailModal');
            // Kiểm tra xem Modal này đã được khởi tạo trước đó chưa
            let modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (!modalInstance) {
            // Nếu chưa có thì mới tạo mới
            modalInstance = new bootstrap.Modal(modalElement);
                }
            // Hiển thị Modal
            modalInstance.show();   
        });
}
</script>
@endsection