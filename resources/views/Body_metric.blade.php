@extends('layout.frontend')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Hồ Sơ Cá Nhân & Chỉ Số Cơ Thể</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('metric.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và Tên</label>
                                <input type="text" name="full_name" class="form-control" value="{{ $user->full_name }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="text" class="form-control" value="{{ $user->email }}" disabled>
                            </div>

                            <hr>
                            <h5 class="text-primary">Body Metrics (Chỉ số cơ thể)</h5>
                            <!-- value=" " lấy thông tin backend trong controller -->
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Chiều cao (cm)</label>
                                <input type="number" name="height" class="form-control" value="{{ $latestMetric->height ?? '' }}" placeholder="cm">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Cân nặng (kg)</label>
                                <input type="number" name="weight" class="form-control" value="{{ $latestMetric->weight ?? '' }}" placeholder="kg">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">BMI hiện tại</label>
                                <input type="text" class="form-control bg-light" value="{{ $latestMetric->bmi ?? 'Chưa có dữ liệu' }}" readonly>
                            </div>
                             <div class="col-md-4 mb-3">
                                <label class="form-label">Ngày cập nhật</label>
                                <input type="text" class="form-control bg-light" value="{{ $latestMetric ? \Carbon\Carbon::parse($latestMetric->measure_at)->format('d/m/Y H:i') : 'Chưa có dữ liệu' }}" ngày cập nhật>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success px-4">Lưu thay đổi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<br><br>
<div class="card shadow border-0 mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0 text-primary"><i class="fas fa-chart-line"></i> Biểu đồ theo dõi sức khỏe</h5>
    </div>
    <div class="card-body">
        <canvas id="healthChart" style="height: 400px;"></canvas>
    </div>
</div>
<div class="text-center mt-4">
    <h5>Mã Check-in của bạn</h5>
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ Auth::id() }}" alt="QR Code">
    <p class="text-muted small">Đưa mã này cho Admin khi đến tập</p>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('healthChart').getContext('2d');
    
    // Dữ liệu từ Controller truyền qua (labels, weights, bmis)
    // Dữ liệu từ Controller truyền qua (labels, weights, bmis)
    const labels = {!! json_encode($labels) !!};
    const weights = {!! json_encode($weights) !!};
    const bmis = {!! json_encode($bmis) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Cân nặng (kg)',
                    data: weights,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 3,
                    tension: 0.4, // Tạo độ cong cho đường
                    fill: true,
                    yAxisID: 'y'
                },
                {
                    label: 'Chỉ số BMI',
                    data: bmis,
                    borderColor: '#ffc107',
                    borderWidth: 2,
                    borderDash: [5, 5], // Đường đứt nét
                    fill: false,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: { display: true, text: 'Cân nặng (kg)' }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: { drawOnChartArea: false }, // Tránh bị đè vạch kẻ
                    title: { display: true, text: 'Chỉ số BMI' }
                }
            }
        }
    });
</script>
@endsection
