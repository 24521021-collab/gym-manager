@extends('layout.admin_layout')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">Chi tiết đơn hàng #{{ $order->id }}</h4>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-dark">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <!-- Thông tin đơn hàng -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="fas fa-receipt me-2"></i>Thông tin đơn hàng</h6>
                </div>
                <div class="card-body">
                    <p><strong>Mã đơn:</strong> #{{ $order->id }}</p>
                    <p><strong>Ngày đặt:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</p>
                    <p><strong>Phương thức:</strong> {{ $order->payment_method }}</p>
                    <p><strong>Tổng tiền:</strong> <span class="text-primary fw-bold">{{ number_format($order->total_amount) }}đ</span></p>
                    <hr>
                    <p class="mb-1"><strong>Trạng thái:</strong></p>
                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                        @csrf
                        <select name="payment_status" class="form-select">
                            <option value="Pending" {{ $order->payment_status == 'Pending' ? 'selected' : '' }}>Chờ xử lý</option>
                            <option value="Paid" {{ $order->payment_status == 'Paid' ? 'selected' : '' }}>Đã thanh toán</option>
                            <option value="Cancelled" {{ $order->payment_status == 'Cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                        <button type="submit" class="btn btn-dark btn-sm w-100 mt-2">Cập nhật</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Thông tin khách hàng -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Thông tin khách hàng</h6>
                </div>
                <div class="card-body">
                    @if($order->user)
                    <p><strong>Tên:</strong> {{ $order->user->name }}</p>
                    <p><strong>Email:</strong> {{ $order->user->email }}</p>
                    <p><strong>Điện thoại:</strong> {{ $order->user->phone ?? 'Chưa có' }}</p>
                    <p><strong>Địa chỉ:</strong> {{ $order->user->address ?? 'Chưa có' }}</p>
                    @else
                    <p class="text-muted">Không xác định</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="fas fa-box me-2"></i>Sản phẩm đã đặt</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Sản phẩm</th>
                                <th>SL</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $item->product->name ?? 'Đã xóa' }}</div>
                                    <small class="text-muted">{{ $item->product->sku ?? '' }}</small>
                                </td>
                                <td>x{{ $item->quantity }}</td>
                                <td class="text-primary">{{ number_format($item->subtotal) }}đ</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Không có sản phẩm</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection