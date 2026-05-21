@extends('layout.frontend')
@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i> Lớp học phụ trách</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
            <i class="fas fa-plus me-1"></i> Thêm lớp mới
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light" >
                    <tr>
                        <th class="ps-3">Thông tin lớp</th>
                        <th style="width: 250px;">Danh sách học viên</th>
                        <th class="text-center">Số buổi</th>
                        <th class="text-center">Giá</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="classTableBody">
                    {{-- Dữ liệu render động bằng JS --}}
                </tbody>
            </table>
        </div>
    </div>

    {{-- Thanh phân trang động --}}
    <nav class="mt-4">
        <ul class="pagination justify-content-center" id="paginationContainer"></ul>
    </nav>
</div>

{{-- Modal Sửa --}}
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editClassForm" onsubmit="saveData(event, 'update')">
            @csrf
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Cập nhật lớp học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="editErrors" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label class="form-label">Tên lớp</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sức chứa</label>
                            <input type="number" name="max_capacity" id="edit_capacity" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số buổi</label>
                            <input type="number" name="total_sessions" id="edit_sessions" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá khóa học (VNĐ)</label>
                        <input type="number" name="price" id="edit_price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thay đổi ảnh (Nếu cần)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Modal Thêm mới --}}
<div class="modal fade" id="addClassModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="addClassForm" onsubmit="saveData(event, 'store')">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Thêm lớp học mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="addErrors" class="alert alert-danger d-none"></div>
                    <div class="mb-3">
                        <label class="form-label">Tên lớp</label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Yoga phục hồi" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sức chứa</label>
                            <input type="number" name="max_capacity" class="form-control" value="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số buổi</label>
                            <input type="number" name="total_sessions" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá khóa học</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ảnh bìa lớp học</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Tạo lớp học</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script>
window.cachedData = []; // Biến toàn cục lưu trữ dữ liệu lớp học

document.addEventListener('DOMContentLoaded', () => {
    loadData();
});

// Hàm Fetch dữ liệu chính
async function loadData(page = 1) {
    const tbody = document.getElementById('classTableBody');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5"><div class="spinner-border text-primary"></div></td></tr>';

    try {
        const response = await fetch(`{{ route('pt.classes.index') }}?page=${page}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();
        
        window.cachedData = result.data; // Lưu vào cache
        tbody.innerHTML = '';

        if (window.cachedData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5">Chưa có lớp học nào do bạn quản lý.</td></tr>';
        } else {
            window.cachedData.forEach(item => {
                // Xử lý hiển thị danh sách học viên
                let studentsHtml = item.bookings.length > 0 
                    ? item.bookings.map(b => `<div class="small"><b>${b.user.full_name}</b> <br> <span class="text-muted">${b.user.email}</span></div>`).join('<hr class="my-1">')
                    : '<span class="text-muted italic small">Chưa có học viên</span>';

                const row = `
                    <tr id="row-${item.id}">
                        <td class="ps-3">
                            <div class="d-flex align-items-center">
                                <img src="/images/products/${item.image || 'default-class.jpg'}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='/images/products/default-class.jpg'">
                                <div>
                                    <div class="fw-bold text-dark">${item.name}</div>
                                    <small class="text-muted">Sức chứa: ${item.bookings.length} / ${item.max_capacity}</small>
                                </div>
                            </div>
                        </td>
                        <td>${studentsHtml}</td>
                        <td class="text-center">${item.total_sessions}</td>
                        <td class="text-center text-primary fw-bold">${new Intl.NumberFormat('vi-VN').format(item.price)}đ</td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-warning" onclick="prepareEdit(${item.id})"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteClass(${item.id})"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });
        }
        renderPagination(result);
    } catch (error) {
        console.error('Error loading classes:', error);
    }
}

// Vẽ thanh phân trang Bootstrap từ JSON Laravel
function renderPagination(data) {
    const container = document.getElementById('paginationContainer');
    container.innerHTML = '';

    data.links.forEach(link => {
        const li = document.createElement('li');
        li.className = `page-item ${link.active ? 'active' : ''} ${!link.url ? 'disabled' : ''}`;
        
        const btn = document.createElement('a');
        btn.className = 'page-link';
        btn.innerHTML = link.label;
        btn.href = '#';
        if (link.url) {
            const page = new URL(link.url).searchParams.get('page');
            btn.onclick = (e) => { e.preventDefault(); loadData(page); };
        }
        
        li.appendChild(btn);
        container.appendChild(li);
    });
}

// Đổ dữ liệu từ mảng cache vào Modal (Không gọi Server)
function prepareEdit(id) {
    const item = window.cachedData.find(x => x.id === id);
    if (!item) return;

    document.getElementById('edit_id').value = item.id;
    document.getElementById('edit_name').value = item.name;
    document.getElementById('edit_capacity').value = item.max_capacity;
    document.getElementById('edit_sessions').value = item.total_sessions;
    document.getElementById('edit_price').value = item.price;
    
    document.getElementById('editErrors').classList.add('d-none');
    new bootstrap.Modal(document.getElementById('editClassModal')).show();
}

// Lưu hoặc Cập nhật dữ liệu
async function saveData(event, type) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const errorDiv = document.getElementById(type === 'store' ? 'addErrors' : 'editErrors');
    
    let url = "{{ route('pt.classes.store') }}";
    if (type === 'update') {
        const id = document.getElementById('edit_id').value;
        url = `/pt/classes/${id}`;
        formData.append('_method', 'PUT'); // Giả lập phương thức PUT
    }

    try {
        const response = await fetch(url, {
            method: 'POST', // Dùng POST để gửi FormData (có file)
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const result = await response.json();

        if (result.success) {
            bootstrap.Modal.getInstance(form.closest('.modal')).hide();
            form.reset();
            loadData(); // Load lại trang hiện tại để cập nhật bảng
        } else {
            errorDiv.innerHTML = Object.values(result.errors).flat().join('<br>');
            errorDiv.classList.remove('d-none');
        }
    } catch (error) {
        console.error('Save error:', error);
    }
}

// Xóa bản ghi
async function deleteClass(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa lớp học này? Dữ liệu không thể khôi phục.')) return;

    try {
        const response = await fetch(`/pt/classes/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const result = await response.json();
        
        if (result.success) {
            const row = document.getElementById(`row-${id}`);
            row.style.transition = 'all 0.5s';
            row.style.opacity = '0';
            setTimeout(() => {
                row.remove();
                if (document.querySelectorAll('#classTableBody tr').length === 0) loadData();
            }, 500);
        }
    } catch (error) {
        console.error('Delete error:', error);
    }
}
</script>
@endsection
