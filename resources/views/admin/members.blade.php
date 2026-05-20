@extends('layout.admin_layout')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark">Quản lý hội viên đăng ký (Fetch API)</h4>
        <button class="btn btn-dark px-4" data-bs-toggle="modal" data-bs-target="#modalMembership" onclick="prepareAdd()">
            <i class="fas fa-plus me-2"></i>Thêm hội viên mới
        </button>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <form id="searchForm" onsubmit="event.preventDefault(); loadMemberships();" class="d-flex">
                <input type="text" id="searchInput" class="form-control me-2" placeholder="Tìm tên hoặc email...">
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
                    <th class="ps-4">Hội viên</th>
                    <th>Gói tập</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày hết hạn</th>
                    <th>Trạng thái</th>
                    <th class="text-end pe-4">Thao tác</th>
                </tr>
            </thead>
            <tbody id="membershipTableBody">
                <tr>
                    <td colspan="6" class="text-center py-4">Đang tải dữ liệu hội viên...</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white border-top-0 py-3">
        <div id="membershipPagination" class="d-flex justify-content-center"></div>
    </div>
</div>

<div class="modal fade" id="modalMembership" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="modalTitle">Đăng ký gói tập mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="membershipForm" onsubmit="saveMembership(event)">
                @csrf
                <input type="hidden" id="membership_id" name="id">
                
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Chọn khách hàng</label>
                        <select name="user_id" id="ms_user_id" class="form-select" required>
                            <option value="">-- Chọn khách hàng --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Chọn gói tập</label>
                        <select name="package_id" id="ms_package_id" class="form-select" required>
                            <option value="">-- Chọn gói tập --</option>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}">{{ $pkg->package_name }} ({{ $pkg->duration_days }} ngày)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">Ngày bắt đầu tập</label>
                        <input type="date" name="start_date" id="ms_start_date" class="form-control" required>
                    </div>
                    
                    <div class="mb-3" id="statusContainer" style="display: none;">
                        <label class="form-label small text-muted fw-bold">Trạng thái thẻ hội viên</label>
                        <select name="status" id="ms_status" class="form-select">
                            <option value="Active">Active (Đang tập)</option>
                            <option value="Expired">Expired (Hết hạn)</option>
                            <option value="Cancelled">Cancelled (Đã hủy)</option>
                            <option value="Inactive">Inactive (Tạm khóa)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-dark px-4">Lưu thông tin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Hàm định dạng ngày tháng sang dd/mm/yyyy hiển thị trên bảng
function formatDate(dateString) {
    if (!dateString) return '';
    const d = new Date(dateString);
    if (isNaN(d.getTime())) return dateString;
    const day = String(d.getDate()).padStart(2, '0');
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const year = d.getFullYear();
    return `${day}/${month}/${year}`;
}

// Hàm chống tấn công mã độc XSS bảo vệ bảng dữ liệu giống product
function escapeHtml(text) {
    if (!text) return '';
    return text.toString()
        .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

/**
 * 1. READ & SEARCH: Tải danh sách hội viên không reload trang
 */
function loadMemberships(page = 1) {
    const searchKeyword = document.getElementById('searchInput').value;
    let url = "{{ route('members.index') }}"; 
    const params = new URLSearchParams();
    if (searchKeyword) params.append('search', searchKeyword);
    if (page > 1) params.append('page', page);
    if (params.toString()) url += `?${params.toString()}`;

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        if(!res.ok) throw new Error('Không thể tải danh sách hội viên.');
        return res.json();
    })
    .then(data => {
        const memberships = data.data; 
        const tbody = document.getElementById('membershipTableBody');
        
        if(!memberships || memberships.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-muted">Không tìm thấy hồ sơ đăng ký nào.</td></tr>`;
            document.getElementById('membershipPagination').innerHTML = '';
            return;
        }
        
        let html = '';
        memberships.forEach(m => {
            const userName = m.user ? (m.user.full_name || m.user.name) : 'N/A';
            const userEmail = m.user ? m.user.email : '';
            const packageName = m.package ? m.package.package_name : 'N/A';
            
            // Xử lý huy hiệu màu sắc cho trạng thái
            let statusBadge = '';
            if(m.status === 'Active') {
                statusBadge = '<span class="badge bg-success-subtle text-success">Đang tập</span>';
            } else if(m.status === 'Expired') {
                statusBadge = '<span class="badge bg-danger-subtle text-danger">Hết hạn</span>';
            } else if(m.status === 'Cancelled') {
                statusBadge = '<span class="badge bg-warning-subtle text-warning">Đã hủy</span>';
            } else {
                statusBadge = `<span class="badge bg-secondary-subtle text-secondary">${escapeHtml(m.status)}</span>`;
            }

            html += `
                <tr id="membership-row-${m.id}">
                    <td class="ps-4">
                        <div class="fw-bold text-dark">${escapeHtml(userName)}</div>
                        <small class="text-muted">${escapeHtml(userEmail)}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border">${escapeHtml(packageName)}</span></td>
                    <td>${formatDate(m.start_date)}</td>
                    <td>${formatDate(m.end_date)}</td>
                    <td>${statusBadge}</td>
                    <td class="text-end pe-4">
                        <button class="btn btn-sm btn-outline-dark me-1" onclick="prepareEdit(${m.id})">
                            <i class="fas fa-edit"></i> Sửa
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteMembership(${m.id})">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </td>
                </tr>`;
        });
        
        tbody.innerHTML = html;
        window.cachedMemberships = memberships; // Lưu cache mảng dữ liệu hội viên vào trình duyệt
        renderPagination(data.links); 
    })
    .catch(err => {
        document.getElementById('membershipTableBody').innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Lỗi: ${err.message}</td></tr>`;
    });
}

/**
 * 1.5 RENDER PAGINATION: Tạo giao diện thanh chuyển trang động
 */
function renderPagination(links) {
    const container = document.getElementById('membershipPagination');
    if (!links || links.length <= 3) { 
        container.innerHTML = '';
        return;
    }
    let html = '<nav><ul class="pagination pagination-sm mb-0">';
    links.forEach(link => {
        const activeClass = link.active ? 'active' : '';
        const disabledClass = link.url === null ? 'disabled' : '';   
        let pageNum = 1;
        if (link.url) {
            const urlObj = new URL(link.url);
            pageNum = urlObj.searchParams.get('page') || 1;
        }
        html += `
            <li class="page-item ${activeClass} ${disabledClass}">
                <a class="page-link" href="#" onclick="event.preventDefault(); ${link.url && !link.active ? `loadMemberships(${pageNum})` : ''}">${link.label}</a>
            </li>`;
    });
    html += '</ul></nav>';
    container.innerHTML = html;
}

// Chuẩn bị form cho trạng thái thêm mới
function prepareAdd() {
    document.getElementById('modalTitle').innerText = "Đăng ký gói tập mới";
    document.getElementById('membershipForm').reset();
    document.getElementById('membership_id').value = '';
    document.getElementById('statusContainer').style.display = 'none'; // Ẩn ô chọn trạng thái khi thêm mới
    document.getElementById('ms_start_date').value = new Date().toISOString().split('T')[0]; // Mặc định ngày hôm nay
}

/**
 * 2. PREPARE EDIT: Tìm dữ liệu từ Cache của luồng và đưa lên form điền
 */
function prepareEdit(id) {
    const m = window.cachedMemberships.find(item => item.id == id);
    if (!m) return;
    
    const userName = m.user ? (m.user.full_name || m.user.name) : 'Hội viên';
    document.getElementById('modalTitle').innerText = "Chỉnh sửa: " + userName;
    document.getElementById('membership_id').value = m.id;
    document.getElementById('ms_user_id').value = m.user_id;
    document.getElementById('ms_package_id').value = m.package_id;
    
    // Cắt chuỗi lấy định dạng YYYY-MM-DD để gán vào ô input date chuẩn xác
    document.getElementById('ms_start_date').value = m.start_date.split('T')[0];
    
    // Hiện ô chọn trạng thái và gán dữ liệu cũ
    document.getElementById('statusContainer').style.display = 'block';
    document.getElementById('ms_status').value = m.status;

    const modal = new bootstrap.Modal(document.getElementById('modalMembership'));
    modal.show();
}

/**
 * 3. CREATE & UPDATE: Thực thi gửi yêu cầu bất đồng bộ lưu vào DB
 */
function saveMembership(event) {
    event.preventDefault();
    const id = document.getElementById('membership_id').value;
    const formElement = document.getElementById('membershipForm');
    const formData = new FormData(formElement);
    
    let url = "{{ route('members.store') }}"; 
    if (id) {
        url = "{{ route('members.update', ':id') }}".replace(':id', id); 
        formData.append('_method', 'PUT'); // Giả lập phương thức PUT của Laravel
    }

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => {
        if(!res.ok) throw new Error('Xử lý dữ liệu thất bại. Vui lòng kiểm tra lại thông tin.');
        return res.json();
    })
    .then(() => {
        const modalElement = document.getElementById('modalMembership');
        const modal = bootstrap.Modal.getInstance(modalElement);
        if(modal) modal.hide();
        
        loadMemberships(); // Gọi làm mới bảng tức thì không tải lại trang
        alert(id ? 'Cập nhật thông tin đăng ký thành công!' : 'Thêm đăng ký gói tập mới thành công!');
    })
    .catch(err => alert(err.message));
}

/**
 * 4. DELETE: Gửi lệnh xóa hàng bằng AJAX
 */
function deleteMembership(id) {
    if(!confirm("Bạn có chắc chắn muốn xóa lượt đăng ký hội viên này không?")) return;
    const url = "{{ route('members.destroy', ':id') }}".replace(':id', id); 
    
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => {
        if(!res.ok) throw new Error('Không thể xóa bản ghi hội viên này.');
        return res.json();
    })
    .then(() => {
        const row = document.getElementById(`membership-row-${id}`);
        if(row) row.remove(); // Xóa thẻ tr ngay lập tức khỏi giao diện HTML
    })
    .catch(err => alert(err.message));
}

// Chạy hàm tải danh sách ngay khi trang web sẵn sàng
document.addEventListener('DOMContentLoaded', () => loadMemberships());
</script>
@endsection