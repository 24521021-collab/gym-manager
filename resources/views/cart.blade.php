@extends('layout.frontend')
@section('content')
<div class="container mt-5">
    <h2 class="mb-4"><i class="fa-solid fa-cart-shopping"></i> Giỏ hàng của bạn</h2>
    <div class="card shadow border-0 p-4">
        @if(session('cart') && count(session('cart')) > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 100px;">Hình</th>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th style="width: 150px;">Số lượng</th>
                            <th>Tổng</th>
                            <th class="text-center">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach(session('cart') as $id => $details)
                            {{-- Lấy thông tin gốc của item để hiển thị ảnh và SKU/kiểm tra stock --}}
                            @php
                                $itemOriginal = null;
                                $realStock = 9999; // Mặc định số lượng lớn cho gói tập/lớp học
                                $imagePath = null;
                                $sku = 'N/A';

                                if ($details['item_type'] === 'product') {
                                    $itemOriginal = \App\Models\Product::find($details['item_id']);
                                    $realStock = $itemOriginal ? $itemOriginal->stock_quantity : 0;
                                    $imagePath = asset('images/products/'.($itemOriginal->image ?? 'default-product.jpg'));
                                    $sku = $itemOriginal->sku ?? 'N/A';
                                } elseif ($details['item_type'] === 'class') {
                                    $itemOriginal = \App\Models\GymClass::find($details['item_id']);
                                    $imagePath = asset('images/products/'.($itemOriginal->image ?? 'default-class.jpg')); // Giả sử lớp học có ảnh
                                }
                                // Gói tập thường không có ảnh riêng, có thể dùng ảnh mặc định
                                $imagePath = $imagePath ?? asset('images/products/default-class.jpg');

                                $subtotal = $details['price'] * $details['quantity'];
                                $total += $subtotal;
                            @endphp
                            <tr data-id="{{ $details['row_id'] }}">
                                <td>
                                    <img src="{{ $imagePath }}" alt="{{ $details['name'] }}"
                                             class="img-thumbnail"
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong>{{ $details['name'] }}</strong>
                                    {{-- Badge phân biệt loại mặt hàng --}}
                                    @if($details['item_type'] === 'product')
                                        <span class="badge bg-dark ms-2">[Sản phẩm]</span>
                                        <br><small class="text-muted">Mã: {{ $sku }}</small>
                                    @elseif($details['item_type'] === 'package')
                                        <span class="badge bg-success ms-2">[Gói tập]</span>
                                    @elseif($details['item_type'] === 'class')
                                        <span class="badge bg-primary ms-2">[Lớp học]</span>
                                    @endif
                                </td>
                                <td>{{ number_format($details['price']) }}đ</td>
                                <td>
                                    <input type="number" value="{{ $details['quantity'] }}"
                                           class="form-control update-cart-quantity"
                                           min="1"
                                           {{-- Vô hiệu hóa nút tăng - giảm nếu không phải sản phẩm --}}
                                           @if($details['item_type'] === 'product') max="{{ $realStock }}" @else readonly @endif
                                           {{-- Thêm class để dễ dàng style hoặc xử lý JS --}}
                                           @if($details['item_type'] !== 'product') style="background-color: #e9ecef;" @endif
                                           >
                                </td>
                                <td class="subtotal-price fw-bold text-primary">{{ number_format($details['price'] * $details['quantity']) }}đ</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-from-cart">
                                        <i class="fa-solid fa-trash"></i> Xóa
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row mt-4 align-items-center">
                <div class="col-md-6">
                    <a href="{{ url('/products') }}" class="btn btn-outline-dark">
                        <i class="fa-solid fa-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                </div>
                <div class="col-md-6 text-end">
                    <h3 class="mb-3">Tổng cộng: <span class="total-cart-price text-danger">{{ number_format($total) }}đ</span></h3>
                    <a href="{{ route('checkout') }}" class="btn btn-primary btn-lg px-5">
                        Xác nhận thanh toán
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fa-solid fa-cart-ghost fa-3x text-muted mb-3"></i>
                <h4>Giỏ hàng đang trống, chưa có gì hết!</h4>
                <a href="{{ url('/products') }}" class="btn btn-primary mt-3">Quay lại cửa hàng</a>
            </div>
        @endif
    </div>
</div>
@endsection
@section('scripts')
{{-- Bạn copy các đoạn Script AJAX (Update, Remove, Checkout) từ file shop.blade.php sang đây --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).on('change keyup', '.update-cart-quantity', function () {
    var ele = $(this);
    var rowId = ele.closest("tr").attr("data-id"); // Lấy row_id từ thuộc tính của dòng
    var quantity = ele.val(); // Lấy số lượng vừa nhập
    var maxStock = parseInt(ele.attr('max')); // Lấy số tồn kho từ thuộc tính max

    // 1. Kiểm tra rỗng hoặc không phải số
    if (quantity === "" || quantity < 1) return;

    // 2. Chặn ngay tại Frontend nếu nhập quá kho
    if (maxStock && parseInt(quantity) > maxStock) {
        alert("Kho chỉ còn " + maxStock + " sản phẩm!");
        ele.val(maxStock);
        quantity = maxStock; // Gán lại để gửi lên server đúng số max
    }

    // 3. Gửi AJAX
    $.ajax({
        url: '{{ route("cart.update") }}',
        method: "patch",
        data: {
            _token: '{{ csrf_token() }}',
            row_id: rowId,
            quantity: quantity
        },
        success: function (response) {
            // Cập nhật giá từng dòng và tổng bill ngay lập tức mà không load lại trang
            ele.closest("tr").find(".subtotal-price").text(response.subtotal);
            $(".total-cart-price").text(response.total);
        },
        error: function(xhr) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: xhr.responseJSON.error || 'Không thể cập nhật số lượng.',
            });
            // Khôi phục số lượng cũ nếu có lỗi
            ele.val(ele.data('old-quantity'));
        }
        });
    });
// loại sản phẩm khỏi giỏ hàng
// Lắng nghe sự kiện click vào nút có class .remove-from-cart
$(document).on('click', '.remove-from-cart', function (e) {
    e.preventDefault(); // Ngăn chặn hành động mặc định của thẻ (như load lại trang)
    var ele = $(this);
    var rowId = ele.closest("tr").attr("data-id"); // Lấy row_id từ thuộc tính data-id của dòng <tr>
    Swal.fire({
        title: 'Bạn có chắc chắn?',
        text: "Bạn sẽ không thể hoàn tác hành động này!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Có, xóa nó đi!'
    }).then((result) => {
        if (result.isConfirmed) {
        $.ajax({
            url: '{{ route("cart.remove") }}', // Đường dẫn API xử lý xóa (định nghĩa trong routes/web.php)
            method: "DELETE", // Sử dụng phương thức DELETE theo đúng chuẩn RESTful API
            data: {
                _token: '{{ csrf_token() }}', // Gửi mã bảo mật CSRF để Laravel cho phép thực hiện yêu cầu
                row_id: rowId // Gửi row_id của item muốn xóa lên server
            },
            success: function (response) {
                // Nếu server xử lý thành công:
                // 1. Dùng hiệu ứng fadeOut (mờ dần) trong 0.3s để xóa dòng sản phẩm trên giao diện
                ele.closest("tr").fadeOut(1000, function() {
                    $(this).remove(); // Xóa hẳn phần tử HTML sau khi mờ dần xong
                    Swal.fire(
                        'Đã xóa!',
                        'Sản phẩm của bạn đã được xóa khỏi giỏ hàng.',
                        'success'
                    ).then(() => {
                        window.location.reload(); // Load lại trang để cập nhật tổng tiền và badge
                    });
                });
                // 2. Cập nhật lại con số Tổng tiền hiển thị trên trang từ dữ liệu server trả về
                $(".total-cart-price").text(response.total);
                // 3. Cập nhật số lượng trên Badge (biểu tượng giỏ hàng) để khớp với thực tế
                $('.badge.bg-danger').text(response.cart_count);
            },
            error: function (xhr) {
                // Nếu server báo lỗi (ví dụ lỗi mạng, lỗi code PHP)
                Swal.fire(
                    'Lỗi!',
                    'Không thể xóa sản phẩm: ' + (xhr.responseJSON.message || 'Lỗi không xác định.'),
                    'error'
                );
            }
        });
        }
    });
});
</script>
@endsection