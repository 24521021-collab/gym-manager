@extends('layout.frontend') 
@section('content')
<div class="container">
    <h2>Danh mục dụng cụ</h2>
     <!-- thanh tìm kiếm sản phẩm id="search input giúp backend tìm kiếm sản phẩm -->
    <div class="row mb-4 g-3">
        <div class="col-md-4">
            <div class="input-group">
                <input type="text" id="search-input" class="form-control" placeholder="Tìm tên tạ, máy tập, phụ kiện...">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
            </div>
        </div>
        <div class="col-md-3">
            <input type="number" id="min-price" class="form-control" placeholder="Giá từ (VNĐ)">
        </div>
        <div class="col-md-3">
            <input type="number" id="max-price" class="form-control" placeholder="Giá đến (VNĐ)">
        </div>
        <div class="col-md-2">
            <button id="filter-btn" class="btn btn-dark w-100">Lọc giá</button>
        </div>
    </div><br>
    <a href="{{route('cart.index')}}" type="button" class="btn btn-outline-dark position-relative">
    🛒 Giỏ hàng
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            {{ session('cart') ? count(session('cart')) : 0 }}
        </span>
    </a>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
   <!-- id="product-list" giúp tìm kiếm ản phẩm -->
    <div class="row" id="product-list">
        @foreach($products as $product)
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <div style="height: 200px; background: #f8f9fa;">
                         <img src="{{asset('images/products/'.($product->image ?? 'default-product.jpg')) }}" 
                         class="card-img-top w-100 h-100" 
                         style="object-fit: cover;" 
                         alt="{{ $product->name }}">
                        </div>
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
                            <button type="button" class="btn btn-primary add-to-cart" data-id="{{ $product->id }}">Thêm vào giỏ hàng</button>
                        @else
                            <button class="btn btn-secondary" disabled>Tạm hết hàng</button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
        <div id="pagination-container" class="d-flex justify-content-center mt-4 mb-5">{{ $products->links('pagination::bootstrap-5') }}</div>
       @dump(session('cart'))
<!-- modal show toàn bộ sản phẩm trong giỏ hàng, lưu trong sesion -->
    <div class="modal fade" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
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
                        <button type="button" class="btn btn-danger btn-sm remove-from-cart">
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
                        <p>Giỏ hàng đang trống, chưa có gì hết !</p>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng hàng</button>
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
$(document).on('click', '.remove-from-cart', function (e) {
    e.preventDefault();
    var ele = $(this);
    var productId = ele.closest("tr").attr("data-id"); // Lấy ID từ thuộc tính data-id của dòng <tr>
    if(confirm("Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?")) {
        $.ajax({
            url: '{{ route("cart.remove") }}', // Đường dẫn khớp với Route::delete
            method: "DELETE",
            data: {
                _token: '{{ csrf_token() }}', 
                id: productId
            },
            success: function (response) {
                // Hiệu ứng xóa dòng ngay lập tức trên giao diện
                ele.closest("tr").fadeOut(300, function() {
                    $(this).remove();
                    // Tải lại trang để cập nhật lại Tổng tiền trong Session và Badge giỏ hàng
                    window.location.reload(); 
                });
            },
            error: function (xhr) {
                alert("Lỗi: Không thể xóa sản phẩm.");
                }
            });
        }
    });
    // Thanh tìm kiếm sản phẩm 
    // BỌC TẤT CẢ TRONG LỆNH NÀY ĐỂ ĐỢI HTML LOAD XONG
    document.addEventListener('DOMContentLoaded', function() {
    let currentSearchQuery = ''; // Biến để nhớ từ khóa tìm kiếm khi khách chuyển trang
    let currentMinPrice = '';
    let currentMaxPrice = '';

    // 1. Hàm gọi API và vẽ lại giao diện (Có hỗ trợ số trang)
    async function fetchProducts(query = '', minPrice = '', maxPrice = '', page = 1) {
        const productList = document.getElementById('product-list');
        const paginationContainer = document.getElementById('pagination-container');
        // Hiện trạng thái đang tải
        productList.innerHTML = '<div class="col-12 text-center"><p>Đang tải dữ liệu...</p></div>';
        paginationContainer.innerHTML = ''; // Ẩn phân trang lúc đang tải
        try {
            // Gửi API kèm từ khóa và số trang
            const response = await fetch(`/search-products?search=${encodeURIComponent(query)}&min_price=${minPrice}&max_price=${maxPrice}&page=${page}`);
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
                    fetchProducts(currentSearchQuery, currentMinPrice, currentMaxPrice, selectedPage); 
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
                fetchProducts(currentSearchQuery, currentMinPrice, currentMaxPrice, 1);
            }
            });
        }

    // 4. Xử lý logic Lọc giá
    const filterBtn = document.getElementById('filter-btn');
    const minInput = document.getElementById('min-price');
    const maxInput = document.getElementById('max-price');

    if (filterBtn) {
        // Sự kiện khi nhấn nút "Lọc giá"
        filterBtn.addEventListener('click', function() {
            // Cập nhật giá trị vào biến toàn cục để giữ trạng thái khi chuyển trang
            currentMinPrice = minInput.value;
            currentMaxPrice = maxInput.value;
            // Gọi hàm tải sản phẩm và luôn bắt đầu từ trang 1 khi lọc mới
            fetchProducts(currentSearchQuery, currentMinPrice, currentMaxPrice, 1);
        });

        // Cải tiến: Cho phép nhấn phím Enter trong ô nhập giá để kích hoạt lọc nhanh
        [minInput, maxInput].forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') filterBtn.click();
            });
        });
    }
    }); // Kết thúc block DOMContentLoaded
</script>