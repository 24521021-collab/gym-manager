@extends('layout.frontend')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-calendar-alt me-2 text-primary"></i> Lớp học phụ trách</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addClassModal">
            <i class="fas fa-plus me-1"></i> Thêm lớp mới
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Thông tin lớp</th>
                        <th class="text-center">Học viên</th>
                        <th class="text-center">Số buổi</th>
                        <th class="text-center">Giá</th>
                        <th class="text-end pe-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classes as $class)
                    <tr>
                    <tr id="row-{{ $class->id }}">
                        <td class="ps-3">
                            <div class="d-flex align-items-center">
                                <img src="{{ asset('uploads/classes/'.$class->image) }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.src='{{ asset('images/default-class.jpg') }}'">
                                <div>
                                    <div class="fw-bold text-dark">{{ $class->name }}</div>
                                    <small class="text-muted">Tỉ lệ lắp đầy: {{ round(($class->bookings_count / $class->max_capacity) * 100) }}%</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info rounded-pill">{{ $class->bookings_count }} / {{ $class->max_capacity }}</span>
                        </td>
                        <td class="text-center">{{ $class->total_sessions }}</td>
                        <td class="text-center text-primary fw-bold">{{ number_format($class->price) }}đ</td>
                        <td class="text-end pe-3">
                            <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#editClass{{ $class->id }}">
                            <button class="btn btn-sm btn-outline-warning me-1 btn-edit" 
                                    data-id="{{ $class->id }}"
                                    data-name="{{ $class->name }}"
                                    data-capacity="{{ $class->max_capacity }}"
                                    data-sessions="{{ $class->total_sessions }}"
                                    data-price="{{ $class->price }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('pt.classes.destroy', $class->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Xác xóa lớp học này?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="{{ $class->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    {{-- Modal Sửa --}}
                    <div class="modal fade" id="editClass{{ $class->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('pt.classes.update', $class->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Sửa lớp học</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label">Tên lớp</label>
                                            <input type="text" name="name" class="form-control" value="{{ $class->name }}" required>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Sức chứa tối đa</label>
                                                <input type="number" name="max_capacity" class="form-control" value="{{ $class->max_capacity }}" required>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label">Số buổi dạy</label>
                                                <input type="number" name="total_sessions" class="form-control" value="{{ $class->total_sessions }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Giá (VNĐ)</label>
                                            <input type="number" name="price" class="form-control" value="{{ $class->price }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Ảnh mô tả</label>
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
                    @empty
                    <tr><td colspan="5" class="text-center py-5">Bạn chưa quản lý lớp học nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Sửa (Dùng chung) --}}
<div class="modal fade" id="editClassModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editClassForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" id="edit_id">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa lớp học</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên lớp</label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sức chứa tối đa</label>
                            <input type="number" name="max_capacity" id="edit_capacity" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số buổi dạy</label>
                            <input type="number" name="total_sessions" id="edit_sessions" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá (VNĐ)</label>
                        <input type="number" name="price" id="edit_price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ảnh mô tả</label>
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
        <form action="{{ route('pt.classes.store') }}" method="POST" enctype="multipart/form-data">
        <form id="addClassForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm lớp học mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên lớp</label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Yoga phục hồi" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sức chứa (Người)</label>
                            <input type="number" name="max_capacity" class="form-control" value="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số buổi</label>
                            <input type="number" name="total_sessions" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá tiền</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ảnh lớp học</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Tạo ngay</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // 1. THÊM MỚI LỚP HỌC (AJAX)
    $('#addClassForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this); // Dùng FormData để gửi được cả ảnh
        $.ajax({
            url: "{{ route('pt.classes.store') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                alert(res.message);
                location.reload(); // Reload để cập nhật bảng cùng các logic Blade phức tạp
            },
            error: function(xhr) {
                alert('Có lỗi xảy ra: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Vui lòng thử lại'));
            }
        });
    });

    // 2. MỞ MODAL SỬA VÀ ĐỔ DỮ LIỆU
    $(document).on('click', '.btn-edit', function() {
        let id = $(this).data('id');
        $('#edit_id').val(id);
        $('#edit_name').val($(this).data('name'));
        $('#edit_capacity').val($(this).data('capacity'));
        $('#edit_sessions').val($(this).data('sessions'));
        $('#edit_price').val($(this).data('price'));
        $('#editClassModal').modal('show');
    });

    // 3. CẬP NHẬT LỚP HỌC (AJAX)
    $('#editClassForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        let formData = new FormData(this);
        
        let url = "{{ route('pt.classes.update', ':id') }}";
        url = url.replace(':id', id);

        $.ajax({
            url: url,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(res) {
                alert(res.message);
                location.reload();
            },
            error: function(xhr) {
                alert('Có lỗi xảy ra: ' + (xhr.responseJSON ? xhr.responseJSON.message : 'Vui lòng thử lại'));
            }
        });
    });

    // 4. XÓA LỚP HỌC (AJAX)
    $(document).on('click', '.btn-delete', function() {
        let id = $(this).data('id');
        if(confirm('Bạn có chắc chắn muốn xóa lớp học này?')) {
            let url = "{{ route('pt.classes.destroy', ':id') }}";
            url = url.replace(':id', id);

            $.ajax({
                url: url,
                method: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    alert(res.message);
                    // Hiệu ứng xóa dòng mà không load lại trang
                    $('#row-' + id).fadeOut(500, function() {
                        $(this).remove();
                        // Nếu xóa hết hàng thì reload để hiện thông báo "Trống"
                        if ($('tbody tr').length === 0) {
                            location.reload();
                        }
                    });
                },
                error: function(xhr) {
                    alert('Không thể xóa lớp học này.');
                }
            });
        }
    });
});
</script>
@endsection
