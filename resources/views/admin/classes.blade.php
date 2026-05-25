@extends('layout.admin_layout')
@section('content')
<style>
    .section-title { border-left: 5px solid #212529; padding-left: 15px; margin-bottom: 25px; font-weight: bold; font-size: 1.25rem; }
    .card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .table thead { background: #212529; color: white; }
    .table thead th { border: none; padding: 12px 15px; }
</style>
<div class="container-fluid py-4">
    <div class="section-title">Quản lý lớp học Gym</div>
    <div class="mb-4 text-end">
        <button class="btn btn-dark px-4" data-bs-toggle="modal" data-bs-target="#modalClass" onclick="prepareAdd()">
            <i class="fas fa-plus"></i> Thêm lớp học mới
        </button>
    </div>
    <div class="section-title">Danh sách Lớp học hiện tại</div>
    <!-- Form Tìm kiếm -->
    <div class="row mb-3">
        <div class="col-md-4">
            <form id="searchForm" onsubmit="event.preventDefault(); loadClasses();" class="d-flex">
                <input type="text" id="searchInput" class="form-control me-2" placeholder="Tìm tên lớp, PT, phòng..." value="{{ request('search') }}">
                <button class="btn btn-outline-dark" type="submit">Tìm kiếm</button>
            </form>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Ảnh</th>
                        <th>Tên lớp</th>
                        <th>PT</th>
                        <th>Sức chứa</th>
                        <th>Số buổi</th>
                        <th>Giá trọn gói</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="classTableBody">
                    <tr><td colspan="9" class="text-center py-4">Đang tải dữ liệu...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            <div id="classPagination" class="d-flex justify-content-center">
                <!-- Rendered by JS -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalClass" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="classForm" onsubmit="saveClass(event)">
            @csrf
            <input type="hidden" id="class_id" name="id">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm lớp học mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tên lớp học</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                            <span class="text-danger error-text name_error"></span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Huấn luyện viên (PT)</label>
                            <select name="pt_id" id="pt_id" class="form-control" required>
                                <option value="">-- Chọn PT --</option>
                                @foreach($pts as $pt)
                                    <option value="{{ $pt->id }}">{{ $pt->user->full_name }}</option>
                                @endforeach
                            </select>
                            <span class="text-danger error-text pt_id_error"></span>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Sức chứa tối đa</label>
                            <input type="number" name="max_capacity" id="max_capacity" class="form-control" required>
                            <span class="text-danger error-text max_capacity_error"></span>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">Tổng số buổi</label>
                            <input type="number" name="total_sessions" id="total_sessions" class="form-control" value="1" required>
                            <span class="text-danger error-text total_sessions_error"></span>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Giá trọn gói (VNĐ)</label>
                            <input type="number" name="price" id="price" class="form-control" value="0" required>
                            <span class="text-danger error-text price_error"></span>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Mô tả lớp học</label>
                            <textarea name="description" id="description" class="form-control" rows="3"></textarea>
                            <span class="text-danger error-text description_error"></span>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-semibold">Hình ảnh lớp học</label>
                            <input type="file" name="image" id="image" class="form-control">
                            <div id="imagePreviewContainer" class="mt-2" style="display:none;">
                                <img id="imagePreview" src="" width="100" class="img-thumbnail">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-dark px-4">Lưu vào Database</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Biến toàn cục lưu trữ dữ liệu cache
    window.cachedClasses = [];

    function escapeHtml(text) {
        if (!text) return '';
        return text.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function clearErrors() {
        document.querySelectorAll('.error-text').forEach(span => span.textContent = '');
    }

    // 1. READ: Tải danh sách lớp học
    function loadClasses(page = 1) {
        const search = document.getElementById('searchInput').value;
        let url = "{{ route('admin.gym-classes.index') }}?page=" + page;
        if (search) url += "&search=" + encodeURIComponent(search);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                window.cachedClasses = data.data;
                const tbody = document.getElementById('classTableBody');
                if (window.cachedClasses.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4">Không tìm thấy lớp học nào.</td></tr>';
                    return;
                }

                let html = '';
                window.cachedClasses.forEach(c => {
                    const imgPath = c.image ? `/images/products/${c.image}` : '/images/products/default-class.jpg';
                    const ptName = c.pt && c.pt.user ? c.pt.user.full_name : 'N/A';
                    
                    html += `
                        <tr id="class-row-${c.id}">
                            <td class="ps-4"><img src="${imgPath}" width="45" height="45" class="rounded object-fit-cover"></td>
                            <td class="fw-bold">${escapeHtml(c.name)}</td>
                            <td>${escapeHtml(ptName)}</td>
                            <td>${c.max_capacity} người</td>
                            <td>${c.total_sessions} buổi</td>
                            <td class="text-danger fw-bold">${Number(c.price).toLocaleString('vi-VN')}đ</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-dark" onclick="prepareEdit(${c.id})">Sửa<i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteClass(${c.id})">Xóa<i class="fas fa-trash"></i></button>
                            </td>
                        </tr>`;
                });
                tbody.innerHTML = html;
                renderPagination(data.links);
            });
    }

    // 1.5 Render Pagination
    function renderPagination(links) {
        const container = document.getElementById('classPagination');
        if (!links || links.length <= 3) { container.innerHTML = ''; return; }

        let html = '<nav><ul class="pagination pagination-sm mb-0">';
        links.forEach(link => {
            let pageNum = link.url ? new URL(link.url).searchParams.get('page') : 1;
            html += `<li class="page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="event.preventDefault(); loadClasses(${pageNum})">${link.label}</a>
            </li>`;
        });
        container.innerHTML = html + '</ul></nav>';
    }

    // 2. PREPARE ADD
    function prepareAdd() {
        document.getElementById('modalTitle').innerText = "Thêm lớp học mới";
        document.getElementById('classForm').reset();
        document.getElementById('class_id').value = '';
        document.getElementById('imagePreviewContainer').style.display = 'none';
        clearErrors();
    }

    // 2.5 PREPARE EDIT (Cache-based)
    function prepareEdit(id) {
        const c = window.cachedClasses.find(item => item.id == id);
        if (!c) return;

        document.getElementById('modalTitle').innerText = "Chỉnh sửa: " + c.name;
        document.getElementById('class_id').value = c.id;
        document.getElementById('name').value = c.name;
        document.getElementById('pt_id').value = c.pt_id;
        document.getElementById('max_capacity').value = c.max_capacity;
        document.getElementById('total_sessions').value = c.total_sessions;
        document.getElementById('price').value = c.price;
        document.getElementById('description').value = c.description || '';

        if (c.image) {
            document.getElementById('imagePreview').src = `/images/products/${c.image}`;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        } else {
            document.getElementById('imagePreviewContainer').style.display = 'none';
        }

        clearErrors();
        new bootstrap.Modal(document.getElementById('modalClass')).show();
    }

    // 3. SAVE (Create & Update)
    function saveClass(event) {
        event.preventDefault();
        const id = document.getElementById('class_id').value;
        const formData = new FormData(document.getElementById('classForm'));
        
        let url = "{{ route('admin.gym-classes.store') }}";
        if (id) {
            url = "{{ route('admin.gym-classes.update', ':id') }}".replace(':id', id);
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw err; });
            return res.json();
        })
        .then(data => {
            bootstrap.Modal.getInstance(document.getElementById('modalClass')).hide();
            loadClasses();
            alert(data.message);
        })
        .catch(err => {
            if (err.errors) {
                for (const key in err.errors) {
                    const errSpan = document.querySelector(`.${key}_error`);
                    if (errSpan) errSpan.textContent = err.errors[key][0];
                }
            } else {
                alert(err.message || "Đã xảy ra lỗi.");
            }
        });
    }

    // 4. DELETE
    function deleteClass(id) {
        if (!confirm("Xóa lớp học này?")) return;
        
        fetch("{{ route('admin.gym-classes.destroy', ':id') }}".replace(':id', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`class-row-${id}`);
                if (row) row.remove();
                alert(data.message);
            }
        });
    }

    // Preview ảnh khi chọn file
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('imagePreview').src = event.target.result;
                document.getElementById('imagePreviewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
document.addEventListener('DOMContentLoaded', () => loadClasses());
</script>
@endsection