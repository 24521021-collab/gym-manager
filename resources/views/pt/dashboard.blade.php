@extends('layout.frontend')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold"><i class="fas fa-user-tie me-2"></i> PT Dashboard</h2>
            <p class="text-muted">Xin chào, Huấn luyện viên <strong>{{ Auth::user()->full_name }}</strong>.</p>
        </div>
    </div>

    {{-- Hàng thống kê nhanh --}}
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white p-4" onclick="location.href='{{ route('pt.classes.index') }}'" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase opacity-75 small fw-bold">Tổng học viên các lớp</h6>
                        <h2 class="mb-0 fw-bold">{{ $totalClassStudents }}</h2>
                        <small>Xem chi tiết <i class="fas fa-arrow-right ms-1"></i></small>
                    </div>
                    <i class="fas fa-users fa-3x opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white p-4" onclick="location.href='{{ route('pt.bookings.index') }}'" style="cursor: pointer;">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase opacity-75 small fw-bold">Khách đặt lịch riêng</h6>
                        <h2 class="mb-0 fw-bold">{{ $totalPrivateClients }}</h2>
                        <small>Quản lý lịch đặt <i class="fas fa-arrow-right ms-1"></i></small>
                    </div>
                    <i class="fas fa-user-check fa-3x opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info text-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-uppercase opacity-75 small fw-bold">Tổng số lớp dạy</h6>
                        <h2 class="mb-0 fw-bold">{{ $classes->count() }}</h2>
                    </div>
                    <i class="fas fa-dumbbell fa-3x opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Biểu đồ hình tròn phân bổ học viên --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 16px;">
                <h5 class="fw-bold mb-4"><i class="fas fa-chart-pie me-2 text-primary"></i>Phân bổ học viên</h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="ptStudentChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Thống kê thu nhập --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 16px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="text-white d-flex flex-column h-100 justify-content-center">
                    <h5 class="fw-bold mb-2 opacity-75">Tổng thu nhập dự kiến (Hoa hồng)</h5>
                    <h1 class="display-4 fw-bold mb-0">{{ number_format($totalCommission) }} đ</h1>
                    <div class="mt-4 p-3 bg-white bg-opacity-10 rounded-3">
                        <p class="small mb-0">
                            <i class="fas fa-info-circle me-1"></i> 
                            Thu nhập bao gồm <strong>50%</strong> phí lớp học và <strong>80%</strong> phí khách đặt lịch riêng.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('ptStudentChart').getContext('2d');
        
        // Kiểm tra nếu không có dữ liệu để tránh biểu đồ trống
        const hasData = ({{ $totalClassStudents }} + {{ $totalPrivateClients }}) > 0;
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Học viên lớp học', 'Khách đặt lịch riêng'],
                datasets: [{
                    data: [{{ $totalClassStudents }}, {{ $totalPrivateClients }}],
                    // Sử dụng màu sắc tương phản rõ rệt
                    backgroundColor: ['#4e73df', '#1cc88a'], 
                    hoverBackgroundColor: ['#2e59d9', '#17a673'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                    borderWidth: 5,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '70%', // Tạo lỗ rỗng ở giữa cho đẹp (Doughnut)
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: { size: 13 }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
