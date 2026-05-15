<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Gym Class - Test Backend Only</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; padding: 40px 0; }
        .section-title { border-left: 5px solid #212529; padding-left: 15px; margin-bottom: 25px; font-weight: bold; }
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .table thead { background: #212529; color: white; }
    </style>
</head>
<body>

<div class="container">
    
    <div class="section-title">1. Cấu hình Lớp học (Create/Edit Form)</div>
    <div class="card">
        <div class="card-body p-4">
            <form action="#" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Tên lớp học</label>
                        <input type="text" name="name" class="form-control" placeholder="Nhập tên lớp..." value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">ID Huấn luyện viên (pt_id)</label>
                        <input type="number" name="pt_id" class="form-control" placeholder="ID của PT từ bảng pt_profiles" value="">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Sức chứa tối đa</label>
                        <input type="number" name="max_capacity" class="form-control" value="20">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Phòng tập (room_name)</label>
                        <input type="text" name="room_name" class="form-control" placeholder="Phòng A, Studio B..." value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Giờ bắt đầu</label>
                        <input type="datetime-local" name="schedule_time" class="form-control" value="">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Giờ kết thúc</label>
                        <input type="datetime-local" name="end_time" class="form-control" value="">
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button type="reset" class="btn btn-light border px-4">Làm mới Form</button>
                        <button type="submit" class="btn btn-dark px-5">Lưu vào Database</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="section-title">2. Danh sách Lớp học hiện tại</div>
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Tên lớp</th>
                            <th>PT ID</th>
                            <th>Sức chứa</th>
                            <th>Phòng</th>
                            <th>Lịch học</th>
                            <th class="text-center pe-4">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="ps-4">1</td>
                            <td class="fw-bold">Yoga Flow</td>
                            <td><span class="badge bg-secondary">PT #10</span></td>
                            <td>15 người</td>
                            <td>Phòng Studio A</td>
                            <td>
                                14:00 - 15:30
                                <br><small>15/05/2026</small>
                            </td>
                            <td class="text-center pe-4">
                                <button type="button" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i> Sửa
                                </button>
                                
                                <form action="#" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i> Xóa
                                    </button>
                                </form>
                            </td>
                        </tr>
                        </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3 border-0">
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>