@extends('layout.frontend') 
@section('content')
<div class="container">
    <h2>Danh mục dụng cụ tập Gym</h2>
    <button type="button" class="btn btn-outline-dark position-relative" data-bs-toggle="modal" data-bs-target="#cartModal">
    🛒 Giỏ hàng
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            {{ session('cart') ? count(session('cart')) : 0 }}
        </span>
    </button>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        @foreach($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text">Mã SKU: <strong>{{ $product->sku }}</strong></p>
                        <p class="card-text text-primary">Giá: {{ number_format($product->price) }}đ</p>
                        <p class="card-text">
                            Tình trạng: 
                            @if($product->stock_quantity > 0)
                                <span class="badge bg-success">Còn {{ $product->stock_quantity }} món</span>
                            @else
                                <span class="badge bg-danger">Hết hàng</span>
                            @endif
                        </p>

                        @if($product->stock_quantity > 0)
                            <a href="{{ route('cart.add', $product->id) }}" class="btn btn-primary add-to-cart" data-id="{{ $product->id }}">Thêm vào giỏ</a>
                        @else
                            <button class="btn btn-secondary" disabled>Tạm hết hàng</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
       @dump(session('cart'))
<!-- modal show toàn bộ sản phẩm trong giỏ hàng, lưu trong sesion -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cartModalLabel">Giỏ hàng của bạn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if(session('cart') && count(session('cart')) > 0)
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- cập nhật số lượng sản phẩm còn trong kho -->
                         @php $total = 0; @endphp
                @foreach(session('cart') as $id => $details)
                        @php 
                        $productOriginal = \App\Models\Product::find($id); 
                        $realStock = $productOriginal ? $productOriginal->stock_quantity : 10;
                        // Tính toán số tiền ngay tại đây
                        $subtotal = $details['price'] * $details['stock_quantity'];
                        $total += $subtotal;
                        @endphp
                    <tr data-id="{{ $id }}">
                        <td>{{ $details['name'] }}</td>
                        <td>{{ $details['price']}}đ</td>
                        <td><input type="number" value="{{ $details['stock_quantity'] }}" class="form-control update-cart-quantity" min="1" max="{{ $realStock }}"></td>
                        <td class="subtotal-price">{{ number_format($details['price'] * $details['stock_quantity']) }}đ</td>
                        <td>
                        {{-- Nút xóa sản phẩm --}}
                        <button class="btn btn-danger btn-sm remove-from-cart">
                            <i class="fa-solid fa-trash"></i> Xóa
                        </button>
                        </td>
                    </tr>
                @endforeach  
                    </tbody>
                <tfoot>
                 <tr>
                    <td colspan="4" class="text-right">
                    <h3>Tổng tiền: <span class="total-cart-price">{{ number_format($total) }}đ</span></h3>
                    </td>
                </tr>
                </tfoot>
                </table>
                @else
                    <div class="text-center py-4">
                        <p>Giỏ hàng đang trống!</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                @if(session('cart'))
                    <a href="#" class="btn btn-primary confirm-checkout-btn">Xác nhận thanh toán</a>
                @endif
            </div>
        </div>
    </div>
</div>
</div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(".add-to-cart").click(function (e) {
    e.preventDefault();

    var productId = $(this).attr("data-id");

    $.ajax({
        url: '/cart/add/' + productId, // Đường dẫn này phải khớp với Route ::get('/cart/add/{id}')
        method: 'POST', 
        success: function (response) {
            // Sau khi add thành công, hiện thông báo hoặc tự mở Modal giỏ hàng lên luôn
            alert("Đã thêm sản phẩm vào giỏ!");
            window.location.reload(); // Load lại để cập nhật số lượng trên icon giỏ hàng
        },
        error: function (xhr) {
            alert("Lỗi: " + xhr.responseJSON.error);
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
$('#confirm-checkout-btn').click(function() {
    $.ajax({
        url: '/api/cart/checkout',
        method: 'POST',
        data: { _token: '{{ csrf_token() }}' },
        success: function(response) {
            alert(response.message);
            window.location.reload(); // Hoặc chuyển hướng sang trang cám ơn
        },
        error: function(xhr) {
            alert(xhr.responseJSON.error);
        }
    });
});
// loại sản phẩm khỏi giỏ hàng
$(".remove-from-cart").click(function (e) {
    e.preventDefault();

    var ele = $(this);

    if(confirm("Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?")) {
        $.ajax({
            url: '{{ route("cart.remove") }}',
            method: "DELETE",
            data: {
                _token: '{{ csrf_token() }}', 
                id: ele.closest("tr").attr("data-id")
            },
            success: function (response) {
                // Xóa dòng đó trên giao diện ngay lập tức
                ele.closest("tr").fadeOut(300, function() {
                    $(this).remove();
                    // Sau khi xóa, bạn có thể cần cập nhật lại Tổng tiền (nếu Controller trả về)
                    location.reload(); // Cách nhanh nhất là reload, hoặc tính toán lại bằng JS
                });
            }
        });
    }
});
</script>