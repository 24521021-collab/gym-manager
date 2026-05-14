@extends('layout.admin_layout') @section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Quản Lý Gói Tập Gym</h4>
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                        <!-- data-bs-target="addPackageModel" để tìm model có id tương ứng -->
                        <i class="fas fa-plus"></i> Thêm gói mới
                    </button>
                </div>
                <div class="card-body">
                    <!-- Form Tìm kiếm -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form action="{{ route('packages.index') }}" method="GET" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" placeholder="Tìm tên gói tập..." value="{{ request('search') }}">
                                <button class="btn btn-outline-primary" type="submit">Tìm</button>
                                @if(request('search'))
                                    <a href="{{ route('packages.index') }}" class="btn btn-outline-secondary ms-1 text-nowrap">Xóa lọc</a>
                                @endif
                            </form>
                        </div>
                    </div>
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
                            @foreach($packages as $item)
                            <tr id="row-{{ $item->id }}">
                                <td>GT{{ str_pad($item->id, 2, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $item->package_name }}</td>
                                <td>{{ $item->duration_days }} ngày</td>
                                <td>{{ number_format($item->price) }} VNĐ</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-btn" 
                                            data-id="GT{{ str_pad($item->id, 2, '0', STR_PAD_LEFT) }}" 
                                            data-name="{{ $item->package_name }}"
                                            data-days="{{ $item->duration_days }}"
                                            data-price="{{ $item->price }}"
                                            data-desc="{{ $item->description }}">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $item->id }}">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $packages->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- modal thêm gói tập -->
<div class="modal fade" id="addPackageModal" tabindex="-1" aria-hidden="true">
    <!-- addPackageModal là id để modal hiện ra -->
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Gói Tập Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addPackageForm">
                <!-- id="addPackageForm" dùng để lấy thông tin trong modal lưu vào database-->
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tên gói tập</label>
                        <input type="text" name="package_name" class="form-control">
                        <span class="text-danger error-text package_name_error"></span>
                    </div>
                    <div class="mb-3">
                        <label>Thời hạn (ngày)</label>
                        <input type="number" name="duration_days" class="form-control">
                        <span class="text-danger error-text duration_days_error"></span>
                    </div>
                    <div class="mb-3">
                        <label>Giá tiền (VNĐ)</label>
                        <input type="number" name="price" class="form-control">
                        <span class="text-danger error-text price_error"></span>
                    </div>
                    <div class="mb-3">
                        <label>Mô tả</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--modal chỉnh sửa gói tập -->
<div class="modal fade" id="editPackageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh Sửa Gói Tập</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPackageForm">
                <!-- editPaackageForm là id thực hiện chỉnh sửa -->
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Tên gói tập</label>
                        <input type="text" id="edit_name" name="package_name" class="form-control">
                        <span class="text-danger error-text update_package_name_error"></span>
                    </div>
                    <div class="mb-3">
                        <label>Thời hạn (ngày)</label>
                        <input type="number" id="edit_days" name="duration_days" class="form-control">
                        <span class="text-danger error-text update_duration_days_error"></span>
                    </div>
                    <div class="mb-3">
                        <label>Giá tiền</label>
                        <input type="number" id="edit_price" name="price" class="form-control">
                        <span class="text-danger error-text update_price_error"></span>
                    </div>
                    <div class="mb-3">
                        <label>Mô tả</label>
                        <textarea id="edit_desc" name="description" class="form-control"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // 1. XỬ LÝ THÊM MỚI
    $('#addPackageForm').on('submit', function(e) {
        e.preventDefault();
        $('.error-text').text('');
        $.ajax({
            url: "{{ route('packages.store') }}",
            method: "POST",
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(res) {
                alert(res.message);
                location.reload();
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function(prefix, val) {
                        $('span.'+prefix+'_error').text(val[0]);
                    });
                }
            }
        });
    });

    // 2. MỞ MODAL SỬA VÀ ĐỔ DỮ LIỆU
    $(document).on('click', '.edit-btn', function() {
        $('#edit_id').val($(this).data('id'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_days').val($(this).data('days'));
        $('#edit_price').val($(this).data('price'));
        $('#edit_desc').val($(this).data('desc'));
        $('#editPackageModal').modal('show');
    });

    // 3. XỬ LÝ CẬP NHẬT
    $('#editPackageForm').on('submit', function(e) {
        e.preventDefault();
        let id = $('#edit_id').val();
        $.ajax({
            url: "/admin/packages/" + id,
            method: "POST", // Laravel Route Resource dùng PUT, nhưng gửi qua AJAX thường dùng POST kèm _method
            data: $(this).serialize(),
            success: function(res) {
                alert(res.message);
                location.reload();
            },
            error: function(xhr) {
                if(xhr.status === 422) {
                    $.each(xhr.responseJSON.errors, function(prefix, val) {
                        $('span.update_'+prefix+'_error').text(val[0]);
                    });
                }
            }
        });
    });

    // 4. XỬ LÝ XÓA
    $(document).on('click', '.delete-btn', function() {
        let id = $(this).data('id');
        if(confirm('Bạn có chắc muốn xóa gói này?')) {
            $.ajax({
                url: "/admin/packages/" + id,
                method: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
                success: function(res) {
                    alert(res.message);
                    $('#row-' + id).remove();
                },
                error: function(xhr) {
                    alert(xhr.responseJSON.message);
                }
            });
        }
    });
});
</script>
@endsection