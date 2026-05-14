@extends('layout.admin_layout') @section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">QUẢN LÝ HỘI VIÊN</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMembershipModal">
            <i class="fas fa-plus"></i> Thêm Hội Viên Mới
        </button>
    </div>

    <div class="card shadow border-0">
        <div class="card-body">
            <!-- Form Tìm kiếm -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <form action="{{ route('members.index') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Tìm tên hoặc email..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">Tìm</button>
                        @if(request('search'))
                            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary ms-1 text-nowrap">Xóa lọc</a>
                        @endif
                    </form>
                </div>
            </div>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Hội viên</th>
                        <th>Gói tập</th>
                        <th>Ngày bắt đầu</th>
                        <th>Ngày hết hạn</th>
                        <th>Trạng thái</th>
                        <th class="text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($memberships as $ms)
                    <tr id="row-{{ $ms->id }}">
                        <td>
                            <div class="fw-bold">{{ $ms->user->name }}</div>
                            <small class="text-muted">{{ $ms->user->email }}</small>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark">{{ $ms->package->package_name }}</span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($ms->start_date)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($ms->end_date)->format('d/m/Y') }}</td>
                        <td>
                            @if($ms->status == 'Active')
                                <span class="badge bg-success text-white">Đang tập</span>
                            @elseif($ms->status == 'Expired')
                                <span class="badge bg-danger text-white">Hết hạn</span>
                            @else
                                <span class="badge bg-secondary text-white">{{ $ms->status }}</span>
                            @endif
                        </td>
                        <!--btn-edit để dẫn đến id sửa thông tin -->
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary btn-edit"
                                data-id="{{ $ms->id }}" 
                                data-status="{{ $ms->status }}"
                                data-bs-toggle="modal" 
                                data-bs-target="#editStatusModal">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <!-- btn-delete  để xóa thông tin, data-id="{{ $ms->id }} cần phải thêm vào -->
                            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $ms->id }}">
                                <i class="fas fa-trash"></i> Xóa
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="d-flex justify-content-center mt-3">
                {{ $memberships->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addMembershipModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('members.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đăng ký gói tập mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Chọn khách hàng</label>
                        <select name="user_id" class="form-select" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chọn gói tập</label>
                        <select name="package_id" class="form-select" required>
                            @foreach($packages as $pkg)
                                <option value="{{ $pkg->id }}">{{ $pkg->id }} - {{ $pkg->package_name }} ({{ $pkg->duration_days }} ngày)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ngày bắt đầu</label>
                        <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Xác nhận đăng ký</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="editStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editStatusForm" method="POST">
            @csrf
            @method('PUT') <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cập nhật trạng thái hội viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- id=edit_status  để gán giá trị mới vào backend-->
                    <select name="status" id="edit_status" class="form-select">
                        <option value="Active">Đang tập</option>
                        <option value="Expired">Hết hạn</option>
                        <option value="Cancelled">Đã hủy</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).on('click', '.btn-delete', function() {
        if(confirm('Bạn có chắc chắn muốn xóa hội viên này không?')) {
            let id = $(this).data('id');
            let url = "{{ route('members.destroy', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                type: 'DELETE',
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    if(res.success) {
                        alert(res.message);
                        $(`#row-${id}`).fadeOut(500); // Làm dòng đó biến mất từ từ
                    }
                }
            });
        }
    });
    $('.btn-edit').on('click', function() {
    let id = $(this).data('id');
    let status = $(this).data('status');
    
    // Gán giá trị vào Modal
    $('#edit_status').val(status);
    
    // Cập nhật đường dẫn Action cho Form
    let url = "{{ route('members.update', ':id') }}";
    $('#editStatusForm').attr('action', url.replace(':id', id));
});
</script>
@endsection