@extends('layout.admin_layout')@section('content')
<style>
    .section-title { border-left: 5px solid #212529; padding-left: 15px; margin-bottom: 25px; font-weight: bold; font-size: 1.25rem; }
    .card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
    .table thead { background: #212529; color: white; }
    .table thead th { border: none; padding: 12px 15px; }
</style>
<div class="container-fluid py-4">
    <div class="section-title">Quản lý lớp học Gym</div>
    <div class="mb-4 text-end">
        <button class="btn btn-dark px-4" data-bs-toggle="modal" data-bs-target="#modalClass" onclick="resetForm()">
            <i class="fas fa-plus"></i> Thêm lớp học mới
        </button>
    </div>

    <div class="section-title">Danh sách Lớp học hiện tại</div>
    <!-- Form Tìm kiếm -->
    <div class="row mb-3">
        <div class="col-md-4">
            <form action="{{ route('admin.gym-classes.index') }}" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Tìm tên lớp, PT, phòng..." value="{{ request('search') }}">
                <button class="btn btn-outline-primary" type="submit">Tìm</button>
                @if(request('search'))
                    <a href="{{ route('admin.gym-classes.index') }}" class="btn btn-outline-secondary ms-1 text-nowrap">Xóa lọc</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tên lớp</th>
                        <th>PT</th>
                        <th>Phòng</th>
                        <th>Lịch học</th>
                        <th class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($classes as $item)
                    <tr>
                        <td><strong>{{ $item->name }}</strong></td>
                        <td>{{ $item->pt->user->full_name ?? 'N/A' }}</td>
                        <td>{{ $item->room_name }}</td>
                        <td>
                            <span class="text-primary">{{ date('H:i', strtotime($item->schedule_time)) }}</span> - 
                            <span class="text-danger">{{ date('H:i', strtotime($item->end_time)) }}</span>
                            <br><small class="text-muted">{{ date('d/m/Y', strtotime($item->schedule_time)) }}</small>
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-warning" 
                                    onclick="editClass({{ json_encode($item) }})"
                                    data-bs-toggle="modal" data-bs-target="#modalClass">
                                <i class="fas fa-edit">Sửa</i>
                            </button>

                            <form action="{{ route('admin.gym-classes.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn xóa?')">
                                    <i class="fas fa-trash">Xóa</i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="card-footer clearfix">
                <div class="float-end">
                {{ $classes->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalClass" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="classForm" method="POST" action="{{ route('admin.gym-classes.store') }}">
            @csrf
            <div id="methodField"></div> <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Thêm lớp học mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tên lớp học</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Huấn luyện viên (PT)</label>
                            <select name="pt_id" id="pt_id" class="form-control" required>
                                <option value="">-- Chọn PT --</option>
                                @foreach($pts as $pt)
                                    <option value="{{ $pt->id }}">{{ $pt->user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Sức chứa tối đa</label>
                            <input type="number" name="max_capacity" id="max_capacity" class="form-control" required>
                        </div>
                        <div class="col-md-8 mb-3">
                            <label class="form-label fw-semibold">Phòng tập</label>
                            <input type="text" name="room_name" id="room_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Giờ bắt đầu</label>
                            <input type="datetime-local" name="schedule_time" id="schedule_time" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Giờ kết thúc</label>
                            <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
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
<script>
    // Hàm reset form về trạng thái "Thêm mới"
    function resetForm() {
        document.getElementById('modalTitle').innerText = 'Thêm lớp học mới';
        document.getElementById('classForm').action = "{{ route('admin.gym-classes.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('classForm').reset();
    }

    // Hàm đổ dữ liệu vào form để "Sửa"
    function editClass(data) {
        document.getElementById('modalTitle').innerText = 'Chỉnh sửa lớp: ' + data.name;
        
        // Đổi Action của form sang Update
        let url = "{{ route('admin.gym-classes.update', ':id') }}";
        document.getElementById('classForm').action = url.replace(':id', data.id);
        
        // Chèn @method('PUT') vào form
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';

        // Đổ dữ liệu vào các input
        document.getElementById('name').value = data.name;
        document.getElementById('pt_id').value = data.pt_id;
        document.getElementById('max_capacity').value = data.max_capacity;
        document.getElementById('room_name').value = data.room_name;
        
        // Format lại thời gian cho đúng chuẩn input datetime-local
        if(data.schedule_time) {
            let date = new Date(data.schedule_time);
            let formattedDate = date.toISOString().slice(0, 16);
            document.getElementById('schedule_time').value = formattedDate;
        }
        if(data.end_time) {
            let dateEnd = new Date(data.end_time);
            let formattedDateEnd = dateEnd.toISOString().slice(0, 16);
            document.getElementById('end_time').value = formattedDateEnd;
        }
    }
</script>