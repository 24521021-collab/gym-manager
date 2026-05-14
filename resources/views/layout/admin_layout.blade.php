<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Gym - Quản lý</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: 100vh; background: #343a40; color: white; padding-top: 20px; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px 20px; }
        .sidebar a:hover { background: #495057; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <h4 class="text-center">GYM ADMIN</h4>
                <hr>
                <a href="/admin/dashboard"><i class="fas fa-home"></i> Dashboard</a>
                <a href="/admin/packages"><i class="fas fa-box"></i> Quản lý gói tập</a>
                <a href="/admin/members"><i class="fas fa-users"></i> Hội viên</a>
                <a href="/admin/checkin"><i class="fas fa-checkin"></i>Điểm danh hội viên</a>
                <a href="/admin/gym-classes">Quản lý lớp học</a>
            </div>
            <div class="col-md-10">
                @yield('content') 
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>