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
                            @php
                                $productOriginal = \App\Models\Product::find($id);
                                $realStock = $productOriginal ? $productOriginal->stock_quantity : 10;
                                $subtotal = $details['price'] * $details['stock_quantity'];
                                $total += $subtotal;
                            @endphp
                            <tr data-id="{{ $id }}">
                                <td>
                                    @if(!empty($productOriginal->image))
                                        <img src="{{ asset('images/products/'.($productOriginal->image ?? 'default-product.jpg')) }}"
                                             alt="{{ $details['name'] }}"
                                             class="img-thumbnail"
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                             style="width: 80px; height: 80px;">
                                            <i class="fa-solid fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $details['name'] }}</strong>
                                    <br><small class="text-muted">Mã: {{ $productOriginal->sku ?? 'N/A' }}</small>
                                </td>
                                <td>{{ number_format($details['price']) }}đ</td>
                                <td>
                                    <input type="number" value="{{ $details['stock_quantity'] }}"
                                           class="form-control update-cart-quantity"
                                           min="1" max="{{ $realStock }}">
                                </td>
                                <td class="subtotal-price fw-bold text-primary">{{ number_format($subtotal) }}đ</td>
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
<script>
  $(document).on('click', '.add-to-cart', function (e) {
    e.preventDefault(); // Chặn thẻ <a> hoặc button tự load trang
    var productId = $(this).attr("data-id");
    var button = $(this);
    $.ajax({
        url: '/cart/add/' + productId,
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}'
        },
        success: function (response) {
            // 1. Cập nhật số lượng trên Badge (nút giỏ hàng)
            // Giả sử server trả về số lượng item mới trong response.cart_count
            $('.badge.bg-danger').text(response.cart_count);
            // 2. Thông báo cho người dùng (tùy chọn)
            alert("Đã thêm sản phẩm vào giỏ hàng!");
        },
        error: function (xhr) {
            button.prop('disabled', false).text('Thêm vào giỏ');
            alert("Lỗi: " + (xhr.responseJSON ? xhr.responseJSON.error : "Không thể thêm hàng"));
        }
    });
});
    $(document).on('change keyup', '.update-cart-quantity', function () {
    var ele = $(this);
    var id = ele.closest("tr").attr("data-id"); // Lấy ID từ thuộc tính của dòng
    var quantity = ele.val(); // Lấy số lượng vừa nhập
    var maxStock = parseInt(ele.attr('max')); // Lấy số tồn kho từ thuộc tính max
    // 1. Kiểm tra rỗng hoặc không phải số
    if (quantity === "" || quantity < 1) return;
    // 2. Chặn ngay tại Frontend nếu nhập quá kho
    if (parseInt(quantity) > maxStock) {
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
            id: id,
            stock_quantity: quantity
        },
        success: function (response) {
            // Cập nhật giá từng dòng và tổng bill ngay lập tức mà không load lại trang
            ele.closest("tr").find(".subtotal-price").text(response.subtotal);
            $(".total-cart-price").text(response.total);
        },
        error: function(xhr) {
            console.log(xhr.responseText);
        }
        });
    });
// loại sản phẩm khỏi giỏ hàng
// Lắng nghe sự kiện click vào nút có class .remove-from-cart
$(document).on('click', '.remove-from-cart', function (e) {
    e.preventDefault(); // Ngăn chặn hành động mặc định của thẻ (như load lại trang)
    var ele = $(this); // Lưu lại đối tượng nút vừa bấm để dùng sau khi có kết quả
    var productId = ele.closest("tr").attr("data-id"); // Tìm dòng <tr> gần nhất và lấy ID sản phẩm
    // Hiển thị hộp thoại xác nhận để tránh khách bấm nhầm
    if(confirm("Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?")) {
        $.ajax({
            url: '{{ route("cart.remove") }}', // Đường dẫn API xử lý xóa (định nghĩa trong routes/web.php)
            method: "DELETE", // Sử dụng phương thức DELETE theo đúng chuẩn RESTful API
            data: {
                _token: '{{ csrf_token() }}', // Gửi mã bảo mật CSRF để Laravel cho phép thực hiện yêu cầu
                id: productId // Gửi ID sản phẩm muốn xóa lên server
            },
            success: function (response) {
                // Nếu server xử lý thành công:
                // 1. Dùng hiệu ứng fadeOut (mờ dần) trong 0.3s để xóa dòng sản phẩm trên giao diện
                ele.closest("tr").fadeOut(1000, function() {
                    $(this).remove(); // Xóa hẳn phần tử HTML sau khi mờ dần xong
                    // Kiểm tra nếu sau khi xóa mà không còn dòng nào (giỏ hàng trống)
                    if ($('tbody tr').length === 0) {
                        window.location.reload(); // Load lại trang để hiển thị thông báo "Giỏ hàng trống"
                    }
                });
                // 2. Cập nhật lại con số Tổng tiền hiển thị trên trang từ dữ liệu server trả về
                $(".total-cart-price").text(response.total);
                // 3. Cập nhật số lượng trên Badge (biểu tượng giỏ hàng) để khớp với thực tế
                $('.badge.bg-danger').text(response.cart_count);
            },
            error: function (xhr) {
                // Nếu server báo lỗi (ví dụ lỗi mạng, lỗi code PHP)
                alert("Lỗi: Không thể xóa sản phẩm.");
            }
        });
    }
});

</script>
@endsection