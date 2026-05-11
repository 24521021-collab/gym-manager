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
                    <button id="confirm-checkout-btn" class="btn btn-primary btn-lg px-5">Xác nhận thanh toán</button>
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
$('#confirm-checkout-btn').click(function() {
    $.ajax({
        url:'{{ route("cart.checkout")}}',
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
    // Thanh tìm kiếm sản phẩm 
    // BỌC TẤT CẢ TRONG LỆNH NÀY ĐỂ ĐỢI HTML LOAD XONG
    document.addEventListener('DOMContentLoaded', function() {
    let currentSearchQuery = ''; // Biến để nhớ từ khóa tìm kiếm khi khách chuyển trang
    // 1. Hàm gọi API và vẽ lại giao diện (Có hỗ trợ số trang)
    async function fetchProducts(query = '', page = 1) {
        const productList = document.getElementById('product-list');
        const paginationContainer = document.getElementById('pagination-container');
        // Hiện trạng thái đang tải
        productList.innerHTML = '<div class="col-12 text-center"><p>Đang tải dữ liệu...</p></div>';
        paginationContainer.innerHTML = ''; // Ẩn phân trang lúc đang tải
        try {
            // Gửi API kèm từ khóa và số trang
            const response = await fetch(`/search-products?search=${encodeURIComponent(query)}&page=${page}`);
            const responseData = await response.json();
            productList.innerHTML = '';
            // ĐÃ FIX LỖI Ở ĐÂY: Trỏ đúng vào mảng con "data" do Laravel paginate tạo ra
            const items = responseData.products.data; 
            // Kiểm tra nếu không có sản phẩm
            if (!items || items.length === 0) {
                productList.innerHTML = '<div class="col-12 text-center"><p class="text-danger">Không tìm thấy sản phẩm nào phù hợp!</p></div>';
                return;
            }
            // Vẽ danh sách sản phẩm
            items.forEach(product => {
                const stockBadge = product.stock_quantity > 0 
                    ? `<span class="badge bg-success">Còn ${product.stock_quantity} món</span>` 
                    : `<span class="badge bg-danger">Hết hàng</span>`;
                
                const btnHtml = product.stock_quantity > 0 
                    ? `<button type="button" class="btn btn-primary add-to-cart" data-id="${product.id}">Thêm vào giỏ</button>` 
                    : `<button class="btn btn-secondary" disabled>Tạm hết hàng</button>`;

                const html = `
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">${product.name}</h5>
                                <p class="card-text text-muted mb-1">Mã SKU: <strong>${product.sku}</strong></p>
                                <p class="card-text text-primary fw-bold h5">Giá: ${new Intl.NumberFormat('vi-VN').format(product.price)}đ</p>
                                <p class="card-text">Tình trạng: ${stockBadge}</p>
                                <div class="mt-3">${btnHtml}</div>
                            </div>
                        </div>
                    </div>`;
                productList.insertAdjacentHTML('beforeend', html);
            });
            // Gọi hàm vẽ các nút Phân trang (Truyền vào trang hiện tại và tổng số trang)
            renderPagination(responseData.products.current_page, responseData.products.last_page);
        } catch (error) {
            console.error("Lỗi kết nối:", error);
            productList.innerHTML = '<p class="text-center text-danger">Có lỗi xảy ra, vui lòng thử lại!</p>';
        }
    }
    // 2. Hàm vẽ các nút Phân trang
    function renderPagination(currentPage, lastPage) {
        if (lastPage <= 1) return; // Chỉ có 1 trang thì không cần vẽ nút

        const paginationContainer = document.getElementById('pagination-container');
        let html = '<ul class="pagination">';

        // Vẽ nút Trước
        if (currentPage > 1) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage - 1}">&laquo; Trước</a></li>`;
        }
        // Vẽ các số trang (1, 2, 3...)
        for (let i = 1; i <= lastPage; i++) {
            if (i === currentPage) {
                html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
            } else {
                html += `<li class="page-item"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
            }
        }
        // Vẽ nút Sau
        if (currentPage < lastPage) {
            html += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage + 1}">Sau &raquo;</a></li>`;
        }
        html += '</ul>';
        paginationContainer.innerHTML = html;
        // Bắt sự kiện khi click vào nút chuyển trang
        paginationContainer.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                if(this.dataset.page) {
                    const selectedPage = parseInt(this.dataset.page);
                    // Gọi lại API nhưng với trang mới
                    fetchProducts(currentSearchQuery, selectedPage); 
                    // Tự động cuộn mượt mà lên đầu danh sách sản phẩm
                    document.getElementById('product-list').scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    }
    // 3. Lắng nghe sự kiện gõ phím tìm kiếm
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            currentSearchQuery = e.target.value; 
            // Gõ từ 2 ký tự trở lên hoặc xóa trắng ô
            if (currentSearchQuery.length >= 2 || currentSearchQuery.length === 0) {
                // Luôn bắt đầu tìm kiếm từ trang 1
                fetchProducts(currentSearchQuery, 1);
            }
            });
        }
    }); // Kết thúc block DOMContentLoaded
</script>
@endsection