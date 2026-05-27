@extends('layout.admin_layout')
@section('content')

    <header>
        <h2 class="font-headline text-3xl md:text-4xl text-white uppercase tracking-tight">VẬN HÀNH <span class="text-primary">&</span> KHO HÀNG</h2>
        <p class="text-gray-400 text-sm mt-1">Quản lý và thực thi các lệnh CRUD thêm, sửa, xóa sản phẩm trong kho.</p>
    </header>

    {{-- Bộ lọc & Tìm kiếm --}}
    <div class="row mb-3 mt-4 max-w-md">
        <div class="col-md-4">
            <form id="searchForm" onsubmit="event.preventDefault(); loadProducts();" class="flex gap-2 text-xs">
                <input type="text" id="searchInput" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" placeholder="Tìm tên hoặc SKU..." value="{{ request('search') }}">
                <button type="submit" class="bg-white/10 text-white font-bold uppercase px-4 py-2.5 rounded-lg hover:bg-white/20 transition-colors whitespace-nowrap">
                    Tìm kiếm
                </button>
            </form>
        </div>
    </div>

    <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6 shadow-md">
        <h3 id="form-title" class="font-headline text-lg text-white uppercase mb-4 border-b border-white/10 pb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">edit_note</span> Cấu hình sản phẩm 
        </h3>
        
        {{-- Form chuẩn hỗ trợ upload file đa phương tiện --}}
        <form id="productForm" onsubmit="saveProductCRUD(event)" enctype="multipart/form-data">
            @csrf
            {{-- id sản phẩm ẩn dùng để phân biệt khi Thêm (rỗng) hoặc Sửa (có ID) --}}
            <input type="hidden" id="product_id" name="id" value="">
            
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end text-xs">
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Hình ảnh</label>
                    <input id="prod-image" name="image" type="file" accept="image/*" class="w-full bg-black/40 border border-white/10 rounded-lg text-gray-400 px-2 py-1.5 focus:ring-1 focus:ring-primary focus:border-primary">
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Tên sản phẩm</label>
                    <input id="prod-name" name="name" type="text" placeholder="e.g. Whey Gold Standard" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" required>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Mã SKU</label>
                    <input id="prod-sku" name="sku" type="text" placeholder="e.g. WHEY-ON-01" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary uppercase" required>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Giá bán (VND)</label>
                    <input id="prod-price" name="price" type="number" placeholder="0" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" required>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Số lượng tồn kho</label>
                    <input id="prod-qty" name="stock_quantity" type="number" placeholder="10" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" required>
                </div>
                
            </div>

            {{-- Vùng xem trước ảnh thu nhỏ tương thích giao diện tối --}}
            <div id="imagePreviewContainer" class="mt-3 flex items-center gap-3" style="display:none;">
                <span class="text-[10px] uppercase font-bold text-gray-400">Ảnh hiện tại:</span>
                <img id="imagePreview" src="" alt="Preview" class="rounded border border-white/10 bg-black/20 p-0.5" style="width: 120px; height: 120px; object-fit: cover;">
            </div>

            <div class="flex gap-2 justify-end mt-4 text-xs">
                <button type="submit" class="bg-primary text-white font-bold uppercase px-6 py-2.5 rounded-lg hover:bg-red-700 transition-colors h-[38px]">Lưu sản phẩm</button>
                <button type="button" onclick="clearCRUDForm()" class="bg-white/10 text-gray-400 font-bold uppercase p-2.5 rounded-lg hover:bg-white/20 hover:text-white transition-colors h-[38px]"><span class="material-symbols-outlined text-sm block">refresh</span></button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-12 gap-6 mt-6">
        <div class="col-span-12 bg-[#1A1A1A] rounded-xl border border-white/10 p-6 shadow-md">
            <div class="flex justify-between items-center mb-4 border-b border-white/10 pb-3">
                <h3 class="font-headline text-lg text-white uppercase">Danh sách kho hàng thực tế</h3>
                <span class="material-symbols-outlined text-primary">inventory</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap text-sm" id="crud-table">
                    <thead>
                        <tr class="text-gray-500 font-bold uppercase text-[10px] tracking-wider border-b border-white/10">
                            <th class="py-3 px-3">Ảnh</th>
                            <th class="py-3 px-3">Sản phẩm</th>
                            <th class="py-3 px-3">Mã SKU</th>
                            <th class="py-3 px-3 text-right">Giá bán</th>
                            <th class="py-3 px-3 text-right">Tồn kho</th>
                            <th class="py-3 px-3 text-center">Trạng thái</th>
                            <th class="py-3 px-3 text-right pr-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5" id="inventory-tbody">
                        <tr>
                            <td colspan="7" class="text-center py-4 text-gray-500">Đang tải dữ liệu sản phẩm...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Vùng điều hướng phân trang Ajax --}}
            <div id="productPagination" class="flex justify-center mt-4 text-xs"></div>
        </div>
    </div>
@endsection

<script>
    // Hàm mã hóa ký tự đặc biệt phòng chống lỗi Stored XSS
    function escapeHtml(text) {
        if (!text) return '';
        return text.toString()
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // Khai báo đường dẫn gốc từ Laravel để JavaScript sử dụng chính xác
    const BASE_PRODUCT_IMAGE_URL = "{{ asset('images/products') }}/";

    // 1. READ: Tải danh sách sản phẩm động từ Cơ sở dữ liệu thông qua REST API
    function loadProducts(page = 1) {
        const searchKeyword = document.getElementById('searchInput').value;
        let url = "{{ route('admin.products.index') }}"; 
        const params = new URLSearchParams();
        if (searchKeyword) params.append('search', searchKeyword);
        if (page > 1) params.append('page', page);
        if (params.toString()) url += `?${params.toString()}`;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(res => {
            if(!res.ok) throw new Error('Không thể tải danh sách sản phẩm từ máy chủ.');
            return res.json();
        })
        .then(data => {
            const products = data.data; 
            const tbody = document.getElementById('inventory-tbody');
            if(!products || products.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">Không tìm thấy sản phẩm nào trong kho.</td></tr>`;
                document.getElementById('productPagination').innerHTML = '';
                return;
            }
            
            let html = '';
            products.forEach(p => {
                const imgPath = p.image ? `/images/products/${p.image}` : '/images/products/default-product.jpg';   
                
                // Thuật toán kiểm tra số lượng tồn kho để sinh màu Badge đồng bộ
                let statusBadge = '';
                if(p.stock_quantity <= 0) {
                    statusBadge = '<span class="inline-block px-2 py-0.5 bg-primary/20 text-primary border border-primary/30 font-bold text-[10px] uppercase rounded">Hết hàng</span>';
                } else if(p.stock_quantity <= 15) {
                    statusBadge = '<span class="inline-block px-2 py-0.5 bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 font-bold text-[10px] uppercase rounded">Sắp hết</span>';
                } else {
                    statusBadge = '<span class="inline-block px-2 py-0.5 bg-green-500/10 text-green-400 border border-green-500/20 font-bold text-[10px] uppercase rounded">Sẵn sàng</span>';
                }

                html += `
                    <tr id="product-row-${p.id}" class="hover:bg-white/5 transition-colors">
                        <td class="py-3 px-3">
                            <img src="${imgPath}" alt="${escapeHtml(p.name)}" class="rounded border border-white/5" style="width: 60px; height: 60px; object-fit: cover;">
                        </td>
                        <td class="py-4 px-3 text-white font-bold prod-title-cell">${escapeHtml(p.name)}</td>
                        <td class="py-4 px-3 text-gray-500 font-mono text-xs prod-sku-cell">${escapeHtml(p.sku)}</td>
                        <td class="py-4 px-3 text-right font-bold text-gray-300">${Number(p.price).toLocaleString('vi-VN')}đ</td>
                        <td class="py-4 px-3 text-right font-headline text-lg ${p.stock_quantity <= 2 ? 'text-primary' : 'text-white'} prod-qty-cell">${p.stock_quantity}</td>
                        <td class="py-4 px-3 text-center status-cell">${statusBadge}</td>
                        <td class="py-4 px-3 text-right pr-4 space-x-1 whitespace-nowrap">
                            <button onclick="editProductRow(${p.id})" class="text-blue-400 p-1 hover:bg-blue-500/10 rounded transition-colors"><span class="material-symbols-outlined text-sm block">edit</span></button>
                            <button onclick="deleteProductRow(${p.id})" class="text-primary p-1 hover:bg-primary/10 rounded transition-colors"><span class="material-symbols-outlined text-sm block">delete</span></button>
                        </td>
                    </tr>`;
            });
            
            tbody.innerHTML = html;
            window.cachedProducts = products; // Lưu bộ nhớ đệm phục vụ gán dữ liệu nhanh lên form sửa
            renderPagination(data.links); 
        })
        .catch(err => {
            document.getElementById('inventory-tbody').innerHTML = `<tr><td colspan="7" class="text-center text-primary py-4">Lỗi hệ thống: ${err.message}</td></tr>`;
            document.getElementById('productPagination').innerHTML = '';
        });
    }

    // 1.5. PAGINATION: Tạo các nút bấm chuyển trang mềm mượt qua AJAX
    function renderPagination(links) {
        const container = document.getElementById('productPagination');
        if (!links || links.length <= 3) { 
            container.innerHTML = '';
            return;
        }
        let html = '<nav class="flex gap-1">';
        links.forEach(link => {
            const activeClass = link.active ? 'bg-primary text-white border-primary' : 'bg-black/40 text-gray-400 border-white/10 hover:bg-white/5';
            const disabledClass = link.url === null ? 'opacity-40 pointer-events-none' : '';   
            
            let pageNum = 1;
            if (link.url) {
                const urlObj = new URL(link.url);
                pageNum = urlObj.searchParams.get('page') || 1;
            }
            html += `
                <a href="#" onclick="event.preventDefault(); ${link.url && !link.active ? `loadProducts(${pageNum})` : ''}" 
                   class="px-3 py-1.5 rounded-lg border font-bold uppercase transition-all ${activeClass} ${disabledClass}">
                   ${link.label.replace('&laquo; Previous', 'Trước').replace('Next &raquo;', 'Sau')}
                </a>`;
        });
        html += '</nav>';
        container.innerHTML = html;
    }

    // 2. CREATE & UPDATE: Đẩy dữ liệu lưu trữ, đồng thời viết đè và tính toán lại Badge giao diện ngay lập tức
    function saveProductCRUD(event) {
        event.preventDefault();
        
        const id = document.getElementById('product_id').value;
        const formElement = document.getElementById('productForm');
        const formData = new FormData(formElement);
        
        const name = document.getElementById('prod-name').value;
        const sku = document.getElementById('prod-sku').value;
        const price = document.getElementById('prod-price').value;
        const qty = parseInt(document.getElementById('prod-qty').value) || 0;
        
        let url = "{{ route('admin.products.store') }}"; 
        if (id) {
            url = "{{ route('admin.products.update', ':id') }}".replace(':id', id); 
            formData.append('_method', 'PUT'); 
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if(!res.ok) throw new Error('Thao tác không thành công. SKU không được trùng lặp.');
            return res.json();
        })
        .then(data => {
            if (id) {
                // UPDATE: Tìm kiếm hàng cũ và thực thi cập nhật DOM trực tiếp không loát lại trang
                const row = document.getElementById(`product-row-${id}`);
                if (row) {
                    row.querySelector('.prod-title-cell').innerText = name;
                    row.querySelector('.prod-sku-cell').innerText = sku.toUpperCase();
                    
                    if (row.cells[3]) {
                        row.cells[3].innerText = Number(price).toLocaleString('vi-VN') + 'đ';
                    }

                    const qtyCell = row.querySelector('.prod-qty-cell');
                    if (qtyCell) {
                        qtyCell.innerText = qty;
                        qtyCell.className = qty <= 2 
                            ? "py-4 px-3 text-right font-headline text-lg text-primary prod-qty-cell" 
                            : "py-4 px-3 text-right font-headline text-lg text-white prod-qty-cell";
                    }

                    // Tái tính toán Badge Trạng thái tại chỗ theo yêu cầu mới nhất của bạn
                    let statusBadge = '';
                    if(qty <= 0) {
                        statusBadge = '<span class="inline-block px-2 py-0.5 bg-primary/20 text-primary border border-primary/30 font-bold text-[10px] uppercase rounded">Hết hàng</span>';
                    } else if(qty <= 15) {
                        statusBadge = '<span class="inline-block px-2 py-0.5 bg-yellow-500/10 text-yellow-500 border border-yellow-500/20 font-bold text-[10px] uppercase rounded">Sắp hết</span>';
                    } else {
                        statusBadge = '<span class="inline-block px-2 py-0.5 bg-green-500/10 text-green-400 border border-green-500/20 font-bold text-[10px] uppercase rounded">Sẵn sàng</span>';
                    }
                    row.querySelector('.status-cell').innerHTML = statusBadge;

                    // Thay đổi ảnh đại diện xem trước trên hàng nếu tệp tệp mới được chọn
                    const fileInput = document.getElementById('prod-image');
                    if (fileInput && fileInput.files.length > 0) {
                        const imgElement = row.querySelector('td img');
                        if (imgElement) imgElement.src = URL.createObjectURL(fileInput.files[0]);
                    }
                }
                alert("Hệ thống: Đã cập nhật thông tin và trạng thái sản phẩm thành công!");
            } else {
                // CREATE: Thêm mới hoàn toàn -> Load lại bảng nạp phần tử lên đầu
                loadProducts();
                alert("Hệ thống: Đã thêm sản phẩm mới vào kho hàng!");
            }
            clearCRUDForm();
        })
        .catch(err => alert(err.message));
    }

    // 3. EDIT PREPARE: Hút dữ liệu từ bộ nhớ đệm lên lại Form nhập liệu để sẵn sàng sửa
    function editProductRow(id) {
        const p = window.cachedProducts.find(item => item.id == id);
        if (!p) return;

        document.getElementById('product_id').value = p.id;
        document.getElementById('prod-name').value = p.name;
        document.getElementById('prod-sku').value = p.sku;
        document.getElementById('prod-price').value = p.price;
        document.getElementById('prod-qty').value = p.stock_quantity;

        const preview = document.getElementById('imagePreview');
        if(p.image) {
            preview.src = `/images/products/${p.image}`;
            document.getElementById('imagePreviewContainer').style.display = 'flex';
        } else {
            document.getElementById('imagePreviewContainer').style.display = 'none';
        }

        document.getElementById('form-title').innerHTML = '<span class="material-symbols-outlined text-blue-400">edit_calendar</span> Cập nhật sản phẩm #' + escapeHtml(p.sku);
        window.scrollTo({ top: 0, behavior: 'smooth' }); // Hiệu ứng cuộn màn hình mượt mà lên vị trí form
    }

    // 4. DELETE: Gửi lệnh xóa hàng vĩnh viễn khỏi Database
    function deleteProductRow(id) {
        if(!confirm("Xác nhận: Bạn có chắc chắn muốn xóa sản phẩm này khỏi hệ thống tồn kho?")) return;
        
        const url = "{{ route('admin.products.destroy', ':id') }}".replace(':id', id); 
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => {
            if(!res.ok) throw new Error('Xóa sản phẩm thất bại.');
            return res.json();
        })
        .then(() => {
            const row = document.getElementById(`product-row-${id}`);
            if(row) row.remove();
            alert("Hệ thống: Đã xóa bản ghi sản phẩm thành công.");
            clearCRUDForm();
        })
        .catch(err => alert(err.message));
    }

    // Reset Form về rỗng ban đầu và phục hồi tiêu đề Cấu hình sản phẩm màu đỏ
    function clearCRUDForm() {
        document.getElementById('productForm').reset();
        document.getElementById('product_id').value = '';
        document.getElementById('imagePreviewContainer').style.display = 'none';
        document.getElementById('form-title').innerHTML = '<span class="material-symbols-outlined text-primary">edit_note</span> Cấu hình sản phẩm ';
    }

    // Lắng nghe sự kiện chạy ngầm nạp bảng sản phẩm ngay khi cấu trúc HTML sẵn sàng
    document.addEventListener('DOMContentLoaded', () => loadProducts());
</script>