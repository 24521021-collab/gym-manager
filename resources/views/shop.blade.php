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
                @foreach($products as $product)
                <div class="product-card-item bg-[#1A1A1A] rounded-2xl border border-white/10 overflow-hidden shadow-md hover:border-primary transition-colors p-3 flex flex-col justify-between" data-cat="{{ $product->product_category }}">
                    <div>
                        <div class="w-full h-36 rounded-xl overflow-hidden mb-2 bg-black/20">
                            <img src="{{ asset('images/products/'.($product->image ?? 'default-product.jpg')) }}" class="w-full h-full object-cover opacity-90" alt="{{ $product->name }}"/>
                        </div>
                        <div class="p-1">
                            <h3 class="text-xs font-bold text-white line-clamp-2 min-h-[2rem]">{{ $product->name }}</h3>
                            <p class="text-[10px] text-gray-500 mt-0.5">Mã SKU: <strong>{{ $product->sku }}</strong></p>
                            <p class="text-[10px] text-gray-500 mt-0.5">Kho còn: <strong class="{{ $product->stock_quantity > 0 ? 'text-emerald-400' : 'text-primary' }}">{{ $product->stock_quantity }}</strong></p>
                            <span class="text-[10px] text-gray-400 mt-1 block italic text-primary">
                                {{ $product->product_category == 'sups' ? 'Thực phẩm bổ sung' : 'Phụ kiện tập luyện' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="p-1 mt-3 flex justify-between items-center">
                        <span class="font-headline text-base text-white">{{ number_format($product->price / 1000, 0) }}kđ</span>
                        <button onclick="addItemToCart('{{ addslashes($product->name) }}', {{ $product->price }}, {{ $product->id }})" {{ $product->stock_quantity <= 0 ? 'disabled' : '' }} class="w-8 h-8 rounded-lg bg-white/5 text-primary {{ $product->stock_quantity > 0 ? 'hover:bg-primary hover:text-white btn-glow' : 'opacity-50 cursor-not-allowed' }} transition-all flex items-center justify-center">
                            <span class="material-symbols-outlined text-sm block">add_shopping_cart</span>
                        </button>
                    </div>
                </div> 
                @endforeach
            </div>
            
            <div id="pagination-container" class="flex justify-center mt-12 mb-8">
                {{ $products->links() }}
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
    // 1. Khởi tạo dữ liệu đồng bộ từ Session của Laravel
    // Thêm cấu hình này ngay đầu khối <script>
    let selectedItems = [
        @if(session('cart'))
            @foreach(session('cart') as $cart_id => $details)
                { 
                    id: "{{ $details['row_id'] ?? $cart_id }}", 
                    name: {!! json_encode($details['name']) !!}, 
                    price: {{ $details['price'] }}, 
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

    document.addEventListener('DOMContentLoaded', function() {
        if(selectedItems.length > 0) {
            document.getElementById('empty-text').style.display = 'none';
            renderCart();
        }

        // Chuyển hướng phân trang mặc định sang AJAX
        const initialPagination = document.getElementById('pagination-container');
        if (initialPagination) {
            initialPagination.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href) {
                    e.preventDefault();
                    const page = new URL(link.href).searchParams.get('page');
                    fetchProducts(currentSearchQuery, currentCategory, page);
                }
            });
        }
        
        // Sự kiện gõ ô tìm kiếm sản phẩm
        document.getElementById('search-input').addEventListener('input', function(e) {
            currentSearchQuery = e.target.value;
            if (currentSearchQuery.length >= 2 || currentSearchQuery.length === 0) {
                fetchProducts(currentSearchQuery, currentCategory, 1);
            }
        });
    });

    // Lọc sản phẩm theo danh mục khoa học
    async function filterCategory(cat, element) {
        currentCategory = cat;

        const buttons = document.querySelectorAll('.category-btn');
        buttons.forEach(btn => {
            btn.className = "category-btn w-full text-left px-3 py-2.5 rounded-lg text-gray-400 hover:bg-white/5 hover:text-white transition-all";
        });
        element.className = "category-btn w-full text-left px-3 py-2.5 rounded-lg text-primary bg-primary/10 font-bold border-l-4 border-primary transition-all";

        fetchProducts(currentSearchQuery, currentCategory, 1);
    }

    // Gửi Request tải dữ liệu sản phẩm mới qua API
    async function fetchProducts(query = '', category = 'all', page = 1) {
        const productList = document.getElementById('product-list');
        const paginationContainer = document.getElementById('pagination-container');
        
        paginationContainer.innerHTML = '';
        productList.innerHTML = '<div class="col-span-2 text-center py-12"><p class="text-sm text-gray-500 animate-pulse">Đang tải sản phẩm...</p></div>';
        
        try {
            const response = await fetch(`/search-products?search=${encodeURIComponent(query)}&category=${category}&page=${page}`);
            const data = await response.json();
            productList.innerHTML = '';
            
            if (!data.products.data || data.products.data.length === 0) {
                productList.innerHTML = '<div class="col-span-2 text-center py-12"><p class="text-primary text-xs font-bold">Không tìm thấy sản phẩm nào phù hợp!</p></div>';
                return;
            }
            
            data.products.data.forEach(product => {
                const img = product.image ? `/images/products/${product.image}` : '/images/products/default-product.jpg';
                const catText = product.product_category === 'sups' ? 'Thực phẩm bổ sung' : 'Phụ kiện tập luyện';
                
                // Chuẩn hóa tên chống lỗi vỡ HTML/JS
                const safeName = product.name.replace(/'/g, "\\'").replace(/"/g, '&quot;');
                
                const html = `
                    <div class="product-card-item bg-[#1A1A1A] rounded-2xl border border-white/10 overflow-hidden shadow-md p-3 flex flex-col justify-between" data-cat="${product.product_category}">
                        <div>
                            <div class="w-full h-36 rounded-xl overflow-hidden mb-2 bg-black/20">
                                <img src="${img}" class="w-full h-full object-cover opacity-90" alt="${product.name}"/>
                            </div>
                            <div class="p-1">
                                <h3 class="text-xs font-bold text-white line-clamp-2 min-h-[2rem]">${product.name}</h3>
                                <p class="text-[10px] text-gray-500 mt-0.5">Mã SKU: <strong>${product.sku}</strong></p>
                                <p class="text-[10px] text-gray-500 mt-0.5">Kho còn: <strong class="${product.stock_quantity > 0 ? 'text-emerald-400' : 'text-primary'}">${product.stock_quantity}</strong></p>
                                <span class="text-[10px] text-gray-400 mt-1 block italic text-primary">${catText}</span>
                            </div>
                        </div>
                        <div class="p-1 mt-3 flex justify-between items-center">
                            <span class="font-headline text-base text-white">${new Intl.NumberFormat('vi-VN').format(product.price / 1000)}kđ</span>
                            <button onclick="addItemToCart('${safeName}', ${product.price}, ${product.id})" ${product.stock_quantity <= 0 ? 'disabled' : ''} class="w-8 h-8 rounded-lg bg-white/5 text-primary ${product.stock_quantity > 0 ? 'hover:bg-primary hover:text-white' : 'opacity-50 cursor-not-allowed'} flex items-center justify-center"><span class="material-symbols-outlined text-sm">add_shopping_cart</span></button>
                        </div>
                    </div>`;
                productList.insertAdjacentHTML('beforeend', html);
            });
            
            renderPagination(data.products);
        } catch (err) {
            productList.innerHTML = '<p class="col-span-2 text-center text-primary text-xs">Lỗi hệ thống tải dữ liệu!</p>';
        }
    }

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
</script>
@endsection