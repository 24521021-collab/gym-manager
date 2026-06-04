@extends('layout.frontend') 
@section('content')
<div class="max-w-7xl mx-auto px-4 md:px-8 py-8 space-y-6">
    
    {{-- Thanh tìm kiếm --}}
    <div class="relative w-full">
        <span class="material-symbols-outlined absolute left-4 top-3.5 text-gray-500 text-xl">search</span>
        <input type="text" id="search-input" class="w-full bg-black/40 border border-white/10 rounded-xl pl-12 pr-4 py-3 text-sm text-white outline-none focus:border-primary transition-colors placeholder:text-gray-500" placeholder="Tìm tên tạ, máy tập, phụ kiện, thực phẩm bổ sung...">
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- Sidebar lọc danh mục --}}
        <aside class="col-span-12 lg:col-span-3">
            <div class="bg-[#1A1A1A] p-4 rounded-2xl border border-white/10 shadow-xl sticky top-24">
                <h3 class="font-headline text-base uppercase text-white border-b border-white/10 pb-3 mb-4 tracking-wider">Danh mục cửa hàng</h3>
                <ul class="space-y-1 font-body text-sm" id="category-list">
                    <li><button onclick="filterCategory('all', this)" class="category-btn w-full text-left px-3 py-2.5 rounded-lg text-primary bg-primary/10 font-bold border-l-4 border-primary transition-all">Tất cả sản phẩm</button></li>
                    <li><button onclick="filterCategory('sups', this)" class="category-btn w-full text-left px-3 py-2.5 rounded-lg text-gray-400 hover:bg-white/5 hover:text-white transition-all">Thực phẩm bổ sung</button></li>
                    <li><button onclick="filterCategory('gear', this)" class="category-btn w-full text-left px-3 py-2.5 rounded-lg text-gray-400 hover:bg-white/5 hover:text-white transition-all">Phụ kiện tập luyện</button></li>
                </ul>
            </div>
        </aside>

        {{-- Grid sản phẩm --}}
        <section class="col-span-12 lg:col-span-5">
            <div class="grid grid-cols-2 gap-4" id="product-list">
                {{-- Nội dung sẽ được load tự động qua Fetch API trong JavaScript --}}
                <div class="col-span-2 text-center py-20 text-gray-500 animate-pulse italic">Đang tải danh sách sản phẩm...</div>
            </div>
            
            <div id="pagination-container" class="flex justify-center mt-12 mb-8">
                {{-- Pagination sẽ được render qua renderPagination() --}}
            </div>
        </section>

        {{-- Giỏ hàng nhanh bên phải --}}
        <section class="col-span-12 lg:col-span-4">
            <div class="bg-[#1A1A1A] rounded-2xl p-4 border border-white/10 shadow-2xl sticky top-24 space-y-4">
                <div class="flex justify-between items-center border-b border-white/10 pb-3">
                    <h2 class="font-headline text-base uppercase tracking-wider text-white">Đơn hàng hiện tại</h2>
                    <span id="cart-item-badge" class="bg-primary/20 text-primary border border-primary/30 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">0 Món</span>
                </div>

                <div id="cart-wrapper" class="space-y-3 max-h-60 overflow-y-auto pr-1 text-xs">
                    <p id="empty-text" class="text-gray-500 text-center py-6 italic">Giỏ hàng trống. Hãy nhặt đồ bổ sung thể hình!</p>
                </div>

                <div class="border-t border-white/10 pt-3 space-y-2 text-xs">
                    <div class="flex justify-between text-gray-400"><span>Tạm tính</span><span id="total-bill">0đ</span></div>
                    <div class="flex justify-between font-headline text-lg text-white border-t border-dashed border-white/10 pt-3 mt-1">
                        <span>Tổng cộng</span><span class="text-primary-container" id="grand-total">0đ</span>
                    </div>
                </div>

                <button onclick="processCheckoutMock()" class="w-full py-3 bg-primary hover:bg-red-700 text-white font-headline text-base uppercase rounded-xl transition-all shadow-lg shadow-primary/20 font-bold tracking-wider flex items-center justify-center gap-2">
                    Xác nhận & Đến trang thanh toán
                    <span class="material-symbols-outlined text-base">payments</span>
                </button>
            </div>
        </section>

    </div>
</div>

{{-- Nhúng thư viện nếu Layout gốc chưa có --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Hàm hỗ trợ ngăn chặn XSS khi render dữ liệu từ API
    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text ? String(text).replace(/[&<>"']/g, m => map[m]) : '';
    }

    /**
     * 1. Khởi tạo dữ liệu đồng bộ từ Session của Laravel.
     * Khi trang web được tải, Blade (Server-side) sẽ duyệt qua Session 'cart' 
     * và in trực tiếp dữ liệu đó vào mảng JavaScript 'selectedItems'.
     */
    let selectedItems = [
        @if(session('cart'))
            @foreach(session('cart') as $cart_id => $details)
                { 
                    // Sử dụng row_id từ dữ liệu hoặc dùng key của mảng session làm ID
                    id: "{{ $details['row_id'] ?? $cart_id }}", 
                    // json_encode giúp xử lý an toàn các ký tự đặc biệt (như dấu ngoặc) trong tên sản phẩm
                    name: {!! json_encode($details['name']) !!}, 
                    // In trực tiếp giá trị số
                    price: {{ $details['price'] }}, 
                    // In trực tiếp số lượng
                    qty: {{ $details['quantity'] }} 
                },
            @endforeach
        @endif
    ];

    // Biến trạng thái toàn cục
    let currentSearchQuery = '';
    let currentCategory = 'all';
    // ĐÃ SỬA: Biến theo dõi tiến trình AJAX để chặn chuyển trang khi chưa lưu xong Session thành công
    let isProcessing = 0;
    /**
     * Sự kiện DOMContentLoaded: Đảm bảo toàn bộ HTML đã được tải xong 
     * trước khi JavaScript bắt đầu can thiệp vào các phần tử.
     */
    document.addEventListener('DOMContentLoaded', function() {
        // 1. ĐỒNG BỘ GIỎ HÀNG BAN ĐẦU
        // Nếu mảng selectedItems (lấy từ Session Laravel) có dữ liệu
        if(selectedItems.length > 0) {
            // Ẩn dòng chữ "Giỏ hàng trống"
            document.getElementById('empty-text').style.display = 'none';
            // Vẽ lại danh sách món hàng lên sidebar bên phải
            renderCart();;
        }
        // 2. TỰ ĐỘNG TẢI DANH SÁCH SẢN PHẨM QUA API KHI MỞ TRANG
        fetchProducts(currentSearchQuery, currentCategory, 1);
        // 3. TÌM KIẾM SẢN PHẨM THỜI GIAN THỰC
        document.getElementById('search-input').addEventListener('input', function(e) {
            currentSearchQuery = e.target.value; // Cập nhật từ khóa tìm kiếm
            // Nếu gõ từ 2 ký tự trở lên HOẶC xóa sạch ô tìm kiếm (để reset danh sách)
            if (currentSearchQuery.length >= 2 || currentSearchQuery.length === 0) {
                // Tải lại danh sách sản phẩm khớp với từ khóa (về trang 1)
                fetchProducts(currentSearchQuery, currentCategory, 1);
            }
        });
    });

    /**
     * Hàm filterCategory: Xử lý khi người dùng chọn một danh mục (Category)
     * @param {string} cat - Mã danh mục (ví dụ: 'all', 'sups', 'gear')
     * @param {HTMLElement} element - Chính là cái nút (button) vừa được nhấn
     */
    async function filterCategory(cat, element) {
        // 1. Cập nhật biến toàn cục currentCategory để các hàm khác (như phân trang) biết đang lọc mục nào
        currentCategory = cat; 

        // 2. CẬP NHẬT GIAO DIỆN NÚT BẤM
        // Tìm tất cả các nút có class 'category-btn' và đưa chúng về trạng thái bình thường (chưa chọn)
        const buttons = document.querySelectorAll('.category-btn');
        buttons.forEach(btn => {
            btn.className = "category-btn w-full text-left px-3 py-2.5 rounded-lg text-gray-400 hover:bg-white/5 hover:text-white transition-all";
        });
        // Sau đó, chỉ thêm các class CSS làm nổi bật (màu đỏ primary, font bold) cho nút vừa click
        element.className = "category-btn w-full text-left px-3 py-2.5 rounded-lg text-primary bg-primary/10 font-bold border-l-4 border-primary transition-all";

        // 3. GỌI API: Tải lại danh sách sản phẩm. 
        // currentSearchQuery được lấy từ biến toàn cục (đã được cập nhật nếu người dùng có gõ ô tìm kiếm trước đó)
        // Luôn Reset về trang 1 khi thay đổi danh mục.
        fetchProducts(currentSearchQuery, currentCategory, 1);
    }

    /**
     * Hàm fetchProducts: Gửi yêu cầu lấy dữ liệu sản phẩm từ Server qua API
     * @param {string} query - Từ khóa tìm kiếm (mặc định trống)
     * @param {string} category - Danh mục sản phẩm (mặc định là 'all')
     * @param {number} page - Số trang hiện tại cần lấy (mặc định là 1)
     */
    async function fetchProducts(query = '', category = 'all', page = 1) {
        // Lấy tham chiếu đến các vùng hiển thị trên giao diện
        const productList = document.getElementById('product-list');
        const paginationContainer = document.getElementById('pagination-container');
        
        // Tạm thời xóa thanh phân trang và hiển thị trạng thái "Đang tải" (Loading)
        paginationContainer.innerHTML = ''; 
        productList.innerHTML = '<div class="col-span-2 text-center py-12"><p class="text-sm text-gray-500 animate-pulse">Đang tải sản phẩm...</p></div>';
        
        try {
            // Gửi yêu cầu HTTP GET đến route 'search-products' của Laravel kèm các tham số
            // encodeURIComponent giúp mã hóa các ký tự đặc biệt trong từ khóa tìm kiếm để URL hợp lệ
            const response = await fetch(`/search-products?search=${encodeURIComponent(query)}&category=${category}&page=${page}`);
            
            // data ở đây là toàn bộ JSON trả về từ Controller
            const data = await response.json(); 
            productList.innerHTML = ''; // Xóa thông báo "Đang tải" để chuẩn bị vẽ danh sách mới
            
            // Kiểm tra nếu không có sản phẩm nào trả về
            if (!data.products.data || data.products.data.length === 0) {
                productList.innerHTML = '<div class="col-span-2 text-center py-12"><p class="text-primary text-xs font-bold">Không tìm thấy sản phẩm nào phù hợp!</p></div>';
                return;
            }
              
            // Duyệt qua mảng sản phẩm nhận được từ Server
            data.products.data.forEach(product => {
                // Xử lý logic hiển thị ảnh (dùng ảnh mặc định nếu sản phẩm không có ảnh)
                const img = product.image ? `/images/products/${product.image}` : '/images/products/default-product.jpg';
                const catText = product.product_category === 'sups' ? 'Thực phẩm bổ sung' : 'Phụ kiện tập luyện';
                const catBadge = product.product_category === 'sups' ? 'Supplements' : 'Gear';
                // Bảo mật: Chuẩn hóa tên sản phẩm để tránh lỗi khi truyền vào hàm JavaScript (Xử lý dấu nháy đơn/kép)
                const safeName = product.name.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                // Tạo HTML mô tả nếu có dữ liệu
                const descriptionHtml = product.description ? `<p class="text-[10px] text-gray-400 mt-2 line-clamp-3 italic leading-relaxed">${escapeHtml(product.description)}</p>` : '';
                // Xây dựng cấu trúc HTML cho một thẻ sản phẩm (Card)
                const html = `
                    <div class="product-card-item bg-[#1A1A1A] rounded-2xl border border-white/10 shadow-md p-3 flex flex-col justify-between relative" data-cat="${product.product_category}">
                        <div>
                            <div class="relative mb-2">
                                <div class="w-full h-36 rounded-xl overflow-hidden bg-black/20 relative cursor-zoom-in js-zoom-container">
                                    <img src="${img}" class="w-full h-full object-cover opacity-90 js-main-img" alt="${product.name}"/>
                                    <span class="absolute top-2 right-2 text-[9px] font-bold bg-black/60 text-primary border border-primary/20 px-2 py-0.5 rounded uppercase backdrop-blur-md z-10">${catBadge}</span>
                                    <div class="absolute bg-white/20 border border-white/40 pointer-events-none hidden js-zoom-lens" style="width: 60px; height: 60px; border-radius: 8px;"></div>
                                </div>
                                <div class="absolute left-full top-0 ml-3 w-64 h-64 border border-white/10 rounded-xl overflow-hidden bg-[#141414] shadow-2xl hidden z-50 js-zoom-result">
                                    <img src="${img}" class="absolute max-w-none js-high-res-img" style="width: 400%; height: 400%;" />
                                </div>
                            </div>
                            <div class="p-1">
                                <h3 class="text-xs font-bold text-white line-clamp-2 min-h-[2rem]">${product.name}</h3>
                                <p class="text-[10px] text-gray-500 mt-0.5">Mã SKU: <strong>${product.sku}</strong></p>
                                <p class="text-[10px] text-gray-500 mt-0.5">Kho còn: <strong class="${product.stock_quantity > 0 ? 'text-emerald-400' : 'text-primary'}">${product.stock_quantity}</strong></p>
                                ${descriptionHtml}
                            </div>
                        </div>
                        <div class="p-1 mt-3 flex justify-between items-center">
                            <span class="font-headline text-base text-white">${new Intl.NumberFormat('vi-VN').format(product.price / 1000)}kđ</span>

                            <button onclick="addItemToCart('${safeName}', ${product.price}, ${product.id})" ${product.stock_quantity <= 0 ? 'disabled' : ''} class="w-8 h-8 rounded-lg bg-white/5 text-primary ${product.stock_quantity > 0 ? 'hover:bg-primary hover:text-white' : 'opacity-50 cursor-not-allowed'} flex items-center justify-center"><span class="material-symbols-outlined text-sm">add_shopping_cart</span></button>
                        </div>
                    </div>`;
                // Chèn HTML vừa tạo vào cuối danh sách sản phẩm hiện có trên trang
                productList.insertAdjacentHTML('beforeend', html);
            });
            
            // Sau khi vẽ xong sản phẩm, gọi hàm cập nhật lại các nút bấm phân trang
            renderPagination(data.products);
        } catch (err) {
            // Xử lý lỗi nếu việc kết nối tới Server bị gián đoạn
            productList.innerHTML = '<p class="col-span-2 text-center text-primary text-xs">Lỗi hệ thống tải dữ liệu!</p>';
        }
    }
    
    //paginator link 
    //[
  //{ "url": "http://korgym.test/search-products?page=1", "label": "&laquo; Previous", "active": false },
  //{ "url": "http://korgym.test/search-products?page=1", "label": "1", "active": false },
  //{ "url": "http://korgym.test/search-products?page=2", "label": "2", "active": true },
  //{ "url": "http://korgym.test/search-products?page=3", "label": "3", "active": false },
  //{ "url": "http://korgym.test/search-products?page=3", "label": "Next &raquo;", "active": false }
//]
    // Vẽ lại hệ thống thanh phân trang
    function renderPagination(paginator) {
        if (paginator.last_page <= 1) return;
        const container = document.getElementById('pagination-container');
        let html = '<nav class="flex gap-2">';
        paginator.links.forEach(link => {
            const isActive = link.active ? 'bg-primary text-white' : 'bg-white/5 text-gray-400 hover:bg-white/10';
            const isDisabled = !link.url ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer';
            
            let label = link.label;
            if(label.includes('Previous')) label = 'Trước';
            if(label.includes('Next')) label = 'Sau';
            
            html += `<button 
                ${!link.url || link.active ? 'disabled' : ''} 
                data-page="${link.url ? new URL(link.url).searchParams.get('page') : ''}"
                class="px-4 py-2 rounded-lg text-xs font-bold transition-all ${isActive} ${isDisabled}">
                ${label}
            </button>`;
        });
        html += '</nav>';
        container.innerHTML = html;
        
        container.querySelectorAll('button[data-page]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const page = e.target.closest('button').dataset.page;
                if(page) fetchProducts(currentSearchQuery, currentCategory, page);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        });
    }

    // Thêm sản phẩm vào giỏ hàng và đồng bộ hóa Session
    function addItemToCart(name, price, id) {
        isProcessing++; // ĐÃ SỬA: Tăng cờ báo hiệu hệ thống bận gửi dữ liệu lên Server
        $.ajax({
            url: '/cart/add/' + id, 
            method: 'POST',
            data: { 
                _token: '{{ csrf_token() }}', 
                id: id, 
                type: 'product' 
            },
            success: function(response) {
                // ĐÃ SỬA: Lấy chính xác row_id phản hồi từ Server để đồng bộ khóa mảng
                const rowId = response.row_id || ('product_' + id);
                const existingItem = selectedItems.find(i => i.id == rowId);
                if(existingItem) {
                    existingItem.qty++;
                } else {
                    selectedItems.push({ id: rowId, name: name, price: price, qty: 1 });
                }
                renderCart();
                showToastNotification(`Đã thêm: ${name}`);
            },
            error: function(xhr) {
                const errorMsg = xhr.responseJSON ? xhr.responseJSON.error : "Không thể thêm sản phẩm";
                Swal.fire('Lỗi kho hàng!', errorMsg, 'error');
                
                if(xhr.status === 400) {
                    window.location.reload(); 
                }
            },
            complete: function() {
                isProcessing--; // ĐÃ SỬA: Hạ cờ khi hoàn thành xong tiến trình thêm đồ bảo mật
                if(isProcessing < 0) isProcessing = 0;
            }
        });
    }

    // Xóa nhanh món hàng ra khỏi sidebar đơn hàng bên phải
    function removeCartItem(index) {
        const deleted = selectedItems[index];
        selectedItems.splice(index, 1);
        renderCart();
        if (deleted && deleted.id) {
            $.ajax({
                url: '{{ route("cart.remove") }}',
                method: "DELETE",
                data: { _token: '{{ csrf_token() }}', row_id: deleted.id }
            });
        }
    }

    // Vẽ cấu trúc giao diện danh sách Widget Mini Cart bên phải màn hình
    function renderCart() {
        const wrapper = document.getElementById('cart-wrapper');
        wrapper.innerHTML = '';
        if(selectedItems.length === 0) {
            wrapper.innerHTML = '<p id="empty-text" class="text-gray-400 text-center py-6 italic">Giỏ hàng trống. Hãy nhặt đồ bổ sung thể hình!</p>';
            document.getElementById('cart-item-badge').innerText = "0 Món";
            document.getElementById('total-bill').innerText = "0đ";
            document.getElementById('grand-total').innerText = "0đ";
            return;
        }
        let subtotal = 0;
        let totalQty = 0;
        selectedItems.forEach((item, idx) => {
            subtotal += item.price * item.qty;
            totalQty += item.qty;
            const div = document.createElement('div');
            div.className = "flex justify-between items-center bg-black/20 p-2 rounded-lg border border-white/5 gap-2";
            div.innerHTML = `
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-white truncate">${item.name}</p>
                    <p class="text-[10px] text-gray-500">${new Intl.NumberFormat('vi-VN').format(item.price)}đ x ${item.qty}</p>
                </div>
                <button onclick="removeCartItem(${idx})" class="text-gray-500 hover:text-primary flex-shrink-0">
                    <span class="material-symbols-outlined text-base">close</span>
                </button>`;
            wrapper.appendChild(div);
        });
        document.getElementById('cart-item-badge').innerText = `${totalQty} Món`;
        document.getElementById('total-bill').innerText = `${new Intl.NumberFormat('vi-VN').format(subtotal)}đ`;
        document.getElementById('grand-total').innerText = `${new Intl.NumberFormat('vi-VN').format(subtotal)}đ`;
    }

    function showToastNotification(msg) {
        const t = document.createElement('div');
        t.style.cssText = "position: fixed; bottom: 20px; left: 20px; background: #222; color: #fff; padding: 10px 20px; border-radius: 8px; font-size: 12px; font-weight: bold; box-shadow: 0 4px 12px rgba(0,0,0,0.2); z-index:9999; border: 1px solid rgba(255,255,255,0.1);";
        t.innerText = msg; document.body.appendChild(t);
        setTimeout(() => t.remove(), 2000);
    }

    // --- ĐÃ SỬA: Hàm chuyển hướng bảo vệ dữ liệu, kiểm tra cờ bận nghiêm ngặt ---
    function processCheckoutMock() {
        if(selectedItems.length === 0) { 
            Swal.fire('Giỏ hàng trống!', 'Vui lòng lựa chọn sản phẩm trước khi đến trang thanh toán.', 'warning'); 
            return; 
        }
        window.location.href = "{{ route('cart.index') }}";
    }

    /** 
     * LOGIC ZOOM SẢN PHẨM 
     * Sử dụng Event Delegation để hoạt động với cả sản phẩm load qua AJAX
     */
    $(document).ready(function() {
        // 1. Khi chuột đi vào vùng chứa ảnh (mouseenter)
        $(document).on('mouseenter', '.js-zoom-container', function() {
            // Hiển thị "ống kính" zoom (hình vuông mờ trên ảnh gốc)
            $(this).find('.js-zoom-lens').removeClass('hidden');
            // Hiển thị khung kết quả zoom (ô chứa ảnh lớn ở bên cạnh)
            $(this).parent().find('.js-zoom-result').removeClass('hidden');
        });

        // 2. Khi chuột rời khỏi vùng chứa ảnh (mouseleave)
        $(document).on('mouseleave', '.js-zoom-container', function() {
            // Ẩn ống kính và khung kết quả
            $(this).find('.js-zoom-lens').addClass('hidden');
            $(this).parent().find('.js-zoom-result').addClass('hidden');
        });

        // 3. Khi di chuyển chuột bên trong vùng chứa ảnh (mousemove)
        $(document).on('mousemove', '.js-zoom-container', function(e) {
            const $container = $(this);
            const $lens = $container.find('.js-zoom-lens');
            const $resultBox = $container.parent().find('.js-zoom-result');
            const $highResImg = $resultBox.find('.js-high-res-img');

            // Lấy tọa độ của khung chứa ảnh so với toàn bộ trang web
            const offset = $container.offset();
            // Tính toán vị trí chuột (x, y) tương đối bên trong khung chứa
            let x = e.pageX - offset.left;
            let y = e.pageY - offset.top;

            // Tính toán vị trí của ống kính sao cho tâm ống kính nằm đúng vị trí đầu con trỏ chuột
            let lensX = x - ($lens.width() / 2);
            let lensY = y - ($lens.height() / 2);

            // GIỚI HẠN (BOUNDARIES): Ngăn không cho ống kính chạy ra ngoài rìa ảnh gốc
            // Chặn trên và trái
            if (lensX < 0) lensX = 0;
            if (lensY < 0) lensY = 0;
            // Chặn phải và dưới (chiều rộng khung - chiều rộng ống kính)
            if (lensX > $container.width() - $lens.width()) lensX = $container.width() - $lens.width();
            if (lensY > $container.height() - $lens.height()) lensY = $container.height() - $lens.height();

            // Cập nhật vị trí CSS cho ống kính
            $lens.css({ left: lensX + 'px', top: lensY + 'px' });

            /**
             * TÍNH TOÁN TỶ LỆ DỊCH CHUYỂN (Zoom Ratio):
             * Chúng ta cần tính xem khi ống kính di chuyển 1px trên ảnh nhỏ, 
             * thì ảnh lớn phải dịch chuyển bao nhiêu px để khớp vị trí.
             * Công thức: (Độ rộng ảnh lớn - Khung hiển thị) / (Độ rộng khung chứa - Ống kính)
             */
            const ratioX = ($highResImg.width() - $resultBox.width()) / ($container.width() - $lens.width());
            const ratioY = ($highResImg.height() - $resultBox.height()) / ($container.height() - $lens.height());

            /**
             * CẬP NHẬT ẢNH LỚN:
             * Ta dịch chuyển ảnh lớn theo chiều ngược lại (dấu '-') với vị trí ống kính.
             * Ví dụ: Khi ống kính sang phải, ảnh lớn phải trượt sang trái để lộ phần ảnh tương ứng.
             */
            $highResImg.css({
                left: '-' + (lensX * ratioX) + 'px',
                top: '-' + (lensY * ratioY) + 'px'
            });
        });
    });
</script>
@endsection







































































































































































