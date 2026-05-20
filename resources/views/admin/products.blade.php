@extends('layout.admin_layout')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">Quản lý kho hàng (Fetch API - Clean Route)</h4>
        <button class="btn btn-dark px-4" data-bs-toggle="modal" data-bs-target="#modalProduct" onclick="prepareAdd()">
            <i class="fas fa-plus me-2"></i>Thêm sản phẩm mới
        </button>
    </div>
    
    <div class="row mb-3">
        <div class="col-md-4">
            <form id="searchForm" onsubmit="event.preventDefault(); loadProducts();" class="d-flex">
                <input type="text" id="searchInput" class="form-control me-2" placeholder="Tìm tên hoặc SKU..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-outline-dark">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </form>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mx-3">
    <div class="card-body p-0">
        <table class="table align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4">Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>SKU</th>
                    <th>Giá</th>
                    <th>Kho</th>
                    <th class="text-end pe-4">Hành động</th>
                </tr>
            </thead>
            <tbody id="productTableBody">
                <tr>
                    <td colspan="6" class="text-center py-4">Đang tải dữ liệu sản phẩm...</td>
                </tr>
            </tbody>
        </table>
    </div>
    {{-- Thêm phần phân trang --}}
    <div class="card-footer bg-white border-top-0 py-3">
        <div id="productPagination" class="d-flex justify-content-center">
            <!-- Nút phân trang sẽ được render bởi JavaScript -->
        </div>
    </div>
</div>

<div class="modal fade" id="modalProduct" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalTitle">Thêm sản phẩm mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productForm" onsubmit="saveProduct(event)">
                @csrf
                <input type="hidden" id="product_id" name="id">
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Tên sản phẩm</label>
                        <input type="text" id="prod_name" name="name" class="form-control" required placeholder="Nhập tên sản phẩm">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Mã sản phẩm (SKU)</label>
                        <input type="text" id="prod_sku" name="sku" class="form-control" required placeholder="Ví dụ: SP-001">
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label small text-muted fw-bold">Giá bán (VND)</label>
                            <input type="number" id="prod_price" name="price" class="form-control" required placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small text-muted fw-bold">Số lượng kho</label>
                            <input type="number" id="prod_stock" name="stock_quantity" class="form-control" required placeholder="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Hình ảnh sản phẩm</label>
                        <input type="file" id="prod_image" name="image" class="form-control" accept="image/*">
                        <div id="imagePreviewContainer" class="mt-2" style="display:none;">
                            <img id="imagePreview" src="" alt="Preview" class="img-thumbnail" style="max-height: 80px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" id="btnSubmitForm" class="btn btn-dark px-4">Lưu sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Hàm bảo mật chống tấn công XSS
function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

// 1. READ & SEARCH: Lấy dữ liệu
function loadProducts(page = 1) { // Thêm tham số page mặc định là 1
    const searchKeyword = document.getElementById('searchInput').value;
    let url = "{{ route('admin.products.index') }}"; 
    const params = new URLSearchParams();
    if (searchKeyword) {
        params.append('search', searchKeyword);
    }
    if (page > 1) {
        params.append('page', page);
    }
    if (params.toString()) {
        url += `?${params.toString()}`;
    }

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        if(!res.ok) throw new Error('Không thể tải danh sách sản phẩm.');
        return res.json();
    })
    .then(data => {
        const products = data.data; // Dữ liệu sản phẩm nằm trong thuộc tính 'data' của đối tượng phân trang
        const tbody = document.getElementById('productTableBody');
        if(!products || products.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">Không tìm thấy sản phẩm nào.</td></tr>`;
            return;
        }
        let html = '';
        products.forEach(p => {
            // Đồng bộ đường dẫn thư mục lưu ảnh public
            const imgPath = p.image ? `/images/products/${p.image}` : '/images/products/default-product.jpg';   
            html += `
                <tr id="product-row-${p.id}">
                    <td class="ps-4">
                        <img src="${imgPath}" alt="${escapeHtml(p.name)}" class="rounded" style="width: 45px; height: 45px; object-fit: cover;">
                    </td>
                    <td><div class="fw-bold text-dark">${escapeHtml(p.name)}</div></td>
                    <td><span class="badge bg-light text-dark border">${escapeHtml(p.sku)}</span></td>
                    <td><b class="text-danger">${Number(p.price).toLocaleString('vi-VN')}đ</b></td>
                    <td>
                        <span class="badge ${p.stock_quantity > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'}">
                            ${p.stock_quantity} món
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-dark me-1" onclick="prepareEdit(${p.id})">
                            <i class="fas fa-edit"></i> Sửa
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${p.id})">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </td>
                </tr>`;
        });
        tbody.innerHTML = html;
        window.cachedProducts = products; // Lưu bộ nhớ cache mảng sản phẩm

        renderPagination(data.links); // Vẽ các nút chuyển trang
    })
    .catch(err => {
        document.getElementById('productTableBody').innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Lỗi: ${err.message}</td></tr>`;
        document.getElementById('productPagination').innerHTML = '';
    });
}
/**
 * 1.5 RENDER PAGINATION: Tạo các nút phân trang từ dữ liệu Laravel trả về
 */
function renderPagination(links) {
    const container = document.getElementById('productPagination');
    if (!links || links.length <= 3) { // Chỉ có 1 trang (Prev, 1, Next) thì không cần hiện
        container.innerHTML = '';
        return;
    }
    let html = '<nav><ul class="pagination pagination-sm mb-0">';
    links.forEach(link => {
        const activeClass = link.active ? 'active' : '';
        const disabledClass = link.url === null ? 'disabled' : '';   
        // Trích xuất số trang từ URL link.url (ví dụ: ?page=2)
        let pageNum = 1;
        if (link.url) {
            const urlObj = new URL(link.url);
            pageNum = urlObj.searchParams.get('page') || 1;
        }
        html += `
            <li class="page-item ${activeClass} ${disabledClass}">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${link.url && !link.active ? `loadProducts(${pageNum})` : ''}">${link.label}</a>
            </li>`;
    });
    html += '</ul></nav>';
    container.innerHTML = html;
}
// Reset Form về chế độ Thêm mới
function prepareAdd() {
    document.getElementById('modalTitle').innerText = "Thêm sản phẩm mới";
    document.getElementById('productForm').reset();
    document.getElementById('product_id').value = '';
    document.getElementById('imagePreviewContainer').style.display = 'none';
}
// 2. PREPARE EDIT: Đổ dữ liệu cũ lên Form
function prepareEdit(id) {
    const p = window.cachedProducts.find(item => item.id == id);
    if (!p) return;
    document.getElementById('modalTitle').innerText = "Chỉnh sửa: " + p.name;
    document.getElementById('product_id').value = p.id;
    document.getElementById('prod_name').value = p.name;
    document.getElementById('prod_sku').value = p.sku;
    document.getElementById('prod_price').value = p.price;
    document.getElementById('prod_stock').value = p.stock_quantity;

    const preview = document.getElementById('imagePreview');
    if(p.image) {
        preview.src = `/images/products/${p.image}`; // Đồng bộ đường dẫn thư mục ảnh public
        document.getElementById('imagePreviewContainer').style.display = 'block';
    } else {
        document.getElementById('imagePreviewContainer').style.display = 'none';
    }
    const modal = new bootstrap.Modal(document.getElementById('modalProduct'));
    modal.show();
}
// 3. CREATE & UPDATE: Lưu thông tin
function saveProduct(event) {
    event.preventDefault();
    const id = document.getElementById('product_id').value;
    const formElement = document.getElementById('productForm');
    const formData = new FormData(formElement);
    let url = "{{ route('admin.products.store') }}"; 
    if (id) {
        url = "{{ route('admin.products.update', ':id') }}".replace(':id', id); 
        formData.append('_method', 'PUT'); // Chèn giả lập PUT method
    }
    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        if(!res.ok) throw new Error('Xử lý dữ liệu thất bại. Hãy kiểm tra lại mã SKU.');
        return res.json();
    })
    .then(() => {
        const modalElement = document.getElementById('modalProduct');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if(modal) modal.hide();
        
        loadProducts();
        alert(id ? 'Cập nhật sản phẩm thành công!' : 'Thêm sản phẩm mới thành công!');
    })
    .catch(err => alert(err.message));
}
// 4. DELETE: Xóa dữ liệu
function deleteProduct(id) {
    if(!confirm("Bạn có chắc chắn muốn xóa sản phẩm này không?")) return;
    const url = "{{ route('admin.products.destroy', ':id') }}".replace(':id', id); 
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => {
        if(!res.ok) throw new Error('Không thể xóa sản phẩm.');
        return res.json();
    })
    .then(() => {
        const row = document.getElementById(`product-row-${id}`);
        if(row) row.remove();
    })
    .catch(err => alert(err.message));
}
document.addEventListener('DOMContentLoaded', loadProducts);
</script>
@endsection