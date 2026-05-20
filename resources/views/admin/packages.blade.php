@extends('layout.admin_layout') @section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">Quản Lý Gói Tập Gym</h4>
        <button type="button" class="btn btn-dark px-4" data-bs-toggle="modal" data-bs-target="#modalPackage" onclick="prepareAdd()">
            <i class="fas fa-plus me-2"></i> Thêm gói mới
        </button>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <form id="searchForm" onsubmit="event.preventDefault(); loadPackages();" class="d-flex">
                <input type="text" id="searchInput" class="form-control me-2" placeholder="Tìm tên gói tập..." value="{{ request('search') }}">
                <button class="btn btn-outline-dark" type="submit">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên gói tập</th>
                                <th>Thời hạn</th>
                                <th>Giá tiền</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="package-table-body">
                            <tr>
                                <td colspan="5" class="text-center py-4">Đang tải dữ liệu gói tập...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div id="packagePagination" class="d-flex justify-content-center">
                <!-- Nút phân trang sẽ được render bởi JavaScript -->
            </div>
        </div>
            </div>
        </div>
    </div>
<!-- Modal Thêm/Sửa Gói Tập -->
<div class="modal fade" id="modalPackage" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Gói Tập Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="packageForm" onsubmit="savePackage(event)">
                @csrf
                <input type="hidden" id="package_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tên gói tập</label>
                        <input type="text" name="package_name" class="form-control" required>
                        <span class="text-danger error-text package_name_error"></span>
                    </div>
                    <div class="mb-3">
                        <label>Thời hạn (ngày)</label>
                        <input type="number" name="duration_days" class="form-control" required min="1">
                        <span class="text-danger error-text duration_days_error"></span>
                    </div>
                    <div class="mb-3">
                        <label>Giá tiền (VNĐ)</label>
                        <input type="number" name="price" class="form-control" required min="0">
                        <span class="text-danger error-text price_error"></span>
                    </div>
                    <div class="mb-3">
                        <label>Mô tả</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-dark px-4">Lưu gói tập</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Hàm bảo mật chống tấn công XSS
    function escapeHtml(text) {
        if (!text) return '';
        return text.toString()
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // Hàm để xóa tất cả các thông báo lỗi
    function clearErrors() {
        document.querySelectorAll('.error-text').forEach(span => span.textContent = '');
    }

    // 1. READ & SEARCH: Lấy dữ liệu gói tập
    function loadPackages(page = 1) {
        const searchKeyword = document.getElementById('searchInput').value;
        let url = "{{ route('packages.index') }}";
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
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Không thể tải danh sách gói tập.');
                return res.json();
            })
            .then(data => {
                const packages = data.data; // Dữ liệu gói tập nằm trong thuộc tính 'data' của đối tượng phân trang
                const tbody = document.getElementById('package-table-body');
                if (!packages || packages.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Không tìm thấy gói tập nào.</td></tr>`;
                    document.getElementById('packagePagination').innerHTML = '';
                    return;
                }
                let html = '';
                packages.forEach(p => {
                    html += `
                        <tr id="package-row-${p.id}">
                            <td>GT${String(p.id).padStart(2, '0')}</td>
                            <td>${escapeHtml(p.package_name)}</td>
                            <td>${p.duration_days} ngày</td>
                            <td>${Number(p.price).toLocaleString('vi-VN')} VNĐ</td>
                            <td>
                                <button class="btn btn-sm btn-outline-dark me-1" onclick="prepareEdit(${p.id})">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deletePackage(${p.id})">
                                    <i class="fas fa-trash"></i> Xóa
                                </button>
                            </td>
                        </tr>`;
                });
                tbody.innerHTML = html;
                window.cachedPackages = packages; // Lưu bộ nhớ cache mảng gói tập

                renderPagination(data.links); // Vẽ các nút chuyển trang
            })
            .catch(err => {
                document.getElementById('package-table-body').innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Lỗi: ${err.message}</td></tr>`;
                document.getElementById('packagePagination').innerHTML = '';
            });
    }

    /**
     * 1.5 RENDER PAGINATION: Tạo các nút phân trang từ dữ liệu Laravel trả về
     */
    function renderPagination(links) {
        const container = document.getElementById('packagePagination');
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
                    <a class="page-link" href="#" onclick="event.preventDefault(); ${link.url && !link.active ? `loadPackages(${pageNum})` : ''}">${link.label}</a>
                </li>`;
        });
        html += '</ul></nav>';
        container.innerHTML = html;
    }

    // Reset Form về chế độ Thêm mới
    function prepareAdd() {
        document.getElementById('modalPackage').querySelector('.modal-title').innerText = "Thêm Gói Tập Mới";
        document.getElementById('packageForm').reset();
        document.getElementById('package_id').value = '';
        clearErrors();
    }

    // 2. PREPARE EDIT: Đổ dữ liệu cũ lên Form
    function prepareEdit(id) {
        const p = window.cachedPackages.find(item => item.id == id);
        if (!p) return;

        document.getElementById('modalPackage').querySelector('.modal-title').innerText = "Chỉnh sửa: " + p.package_name;
        document.getElementById('package_id').value = p.id;
        document.querySelector('#packageForm input[name="package_name"]').value = p.package_name;
        document.querySelector('#packageForm input[name="duration_days"]').value = p.duration_days;
        document.querySelector('#packageForm input[name="price"]').value = p.price;
        document.querySelector('#packageForm textarea[name="description"]').value = p.description || '';
        clearErrors();

        const modal = new bootstrap.Modal(document.getElementById('modalPackage'));
        modal.show();
    }

    // 3. CREATE & UPDATE: Lưu thông tin
    function savePackage(event) {
        event.preventDefault();
        const id = document.getElementById('package_id').value;
        const formElement = document.getElementById('packageForm');
        const formData = new FormData(formElement);
        clearErrors();

        let url = "{{ route('packages.store') }}";
        let method = 'POST';

        if (id) {
            url = "{{ route('packages.update', ':id') }}".replace(':id', id);
            formData.append('_method', 'PUT'); // Giả lập phương thức PUT của Laravel
        }

        fetch(url, {
                method: 'POST', // Fetch API sẽ luôn dùng POST khi có FormData, Laravel sẽ đọc _method
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => {
                        throw err;
                    });
                }
                return res.json();
            })
            .then(data => {
                const modalElement = document.getElementById('modalPackage');
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) modal.hide();

                loadPackages();
                alert(data.message);
            })
            .catch(err => {
                if (err.errors) { // Validation errors from Laravel
                    for (const key in err.errors) {
                        document.querySelector(`span.${key}_error`).textContent = err.errors[key][0];
                    }
                } else {
                    alert(err.message || 'Có lỗi xảy ra, vui lòng thử lại.');
                }
            });
    }

    // 4. DELETE: Xóa dữ liệu
    function deletePackage(id) {
        if (!confirm("Bạn có chắc chắn muốn xóa gói tập này không?")) return;
        const url = "{{ route('packages.destroy', ':id') }}".replace(':id', id);

        fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => {
                        throw err;
                    });
                }
                return res.json();
            })
            .then(data => {
                const row = document.getElementById(`package-row-${id}`);
                if (row) row.remove();
                alert(data.message);
                loadPackages(); // Tải lại để cập nhật phân trang nếu cần
            })
            .catch(err => {
                alert(err.message || 'Không thể xóa gói tập.');
            });
    }

    document.addEventListener('DOMContentLoaded', loadPackages);
</script>
@endsection