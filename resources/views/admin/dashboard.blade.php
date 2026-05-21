@extends('layout.admin_layout')
@section('content')

<div class="container-fluid py-4 bg-light min-vh-screen">
    
    {{-- ĐƯA NÚT BẤM RA NGOÀI VÙNG CHỤP PDF ĐỂ TRÁNH LỖI RENDER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-dark mb-0">Tổng quan tình hình kinh doanh</h1>
        <button id="exportPdfBtn" class="btn btn-danger shadow-sm px-4 fw-bold" onclick="exportDashboardToPDF()">
            <i class="fas fa-file-pdf me-2"></i> Xuất Báo Cáo PDF Ngay
        </button>
    </div>

    {{-- VÙNG CHỤP EXPORT PDF THỰC SỰ --}}
    <div id="dashboard-content" class="p-1">
        
        {{-- 1. CÁC CARD THỐNG KÊ NHANH (QUICK STATS) - CHUYỂN SANG MÀU PHẲNG ĐỂ PDF HỖ TRỢ 100% --}}
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px; background-color: #f0f3ff; border: 1px solid #c7d2fe !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-medium">Tổng Doanh thu</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2.5 py-1 small">Thực thu</span>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.6rem;">{{ number_format($totalRevenue) }} đ</h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px; background-color: #e0f2fe; border: 1px solid #bae6fd !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-medium">Lớp học đang mở</span>
                            <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-2.5 py-1 small">Active</span>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.6rem;">{{ $totalClasses }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px; background-color: #f3e8ff; border: 1px solid #e9d5ff !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-medium">Học viên mới (Năm nay)</span>
                            <span class="badge rounded-pill px-2.5 py-1 small" style="color: #8b5cf6; background-color: rgba(139, 92, 246, 0.15);">Members</span>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.6rem;">{{ $totalNewMembers }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm" style="border-radius: 16px; background-color: #fef3c7; border: 1px solid #fde68a !important;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small fw-medium">Đơn hàng sản phẩm</span>
                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1 small">Đã mua</span>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark" style="font-size: 1.6rem;">{{ number_format($totalOrders) }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. BIỂU ĐỒ ĐƯỜNG: TỔNG DOANH THU --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-bold text-dark mb-0">Tổng doanh thu sản phẩm & Gói tập kết hợp</h5>
                    <span class="badge bg-white text-secondary border px-3 py-2 rounded-pill">Năm {{ date('Y') }}</span>
                </div>
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        {{-- 3 & 4. KHỐI BIỂU ĐỒ TRÒN --}}
        <div class="row g-4 mb-4">
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-dark mb-4">Cơ cấu doanh thu</h5>
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-sm-6 mb-3 mb-sm-0" style="position: relative; height: 220px;">
                                <canvas id="revenueStructureChart"></canvas>
                            </div>
                            <div class="col-12 col-sm-6 ps-sm-4">
                                @php $totalStructure = array_sum($structureData['data']); @endphp
                                @foreach($structureData['labels'] as $index => $label)
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="d-inline-block rounded-circle me-2" id="legend-color-struct-{{$index}}" style="width: 12px; height: 12px;"></span>
                                        <span class="text-secondary small">{{ $label }}</span>
                                    </div>
                                    <span class="fw-bold text-dark small">
                                        {{ $totalStructure > 0 ? number_format($structureData['data'][$index] / $totalStructure * 100, 1) : 0 }}%
                                    </span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 16px;">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-dark mb-4">Tỷ lệ các gói tập được đăng ký</h5>
                        <div class="row align-items-center justify-content-center">
                            <div class="col-12 col-sm-6 mb-3 mb-sm-0" style="position: relative; height: 220px;">
                                <canvas id="packageChart"></canvas>
                            </div>
                            <div class="col-12 col-sm-6 ps-sm-4">
                                @foreach($packageData['labels'] as $index => $label)
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <span class="d-inline-block rounded-circle me-2" id="legend-color-pkg-{{$index}}" style="width: 12px; height: 12px;"></span>
                                        <span class="text-secondary small text-truncate" style="max-width: 130px;">{{ $label }}</span>
                                    </div>
                                    <span class="fw-bold text-dark small">{{ $packageData['data'][$index] }}%</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 5. BIỂU ĐỒ TĂNG TRƯỞNG THÀNH VIÊN --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold text-dark mb-4">Tăng trưởng khách hàng mới</h5>
                <div style="position: relative; height: 280px; width: 100%;">
                    <canvas id="newUsersChart"></canvas>
                </div>
            </div>
        </div>

        {{-- 6. TOP SẢN PHẨM BÁN CHẠY --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                    <h5 class="card-title fw-bold text-dark mb-0">Top 10 Sản phẩm bán chạy nhất</h5>
                    
                    <div class="d-flex gap-2">
                        <select id="categoryFilter" onchange="updateProductChart('category')" class="d-none">
                            <option value="all" selected>Tất cả sản phẩm</option>
                            @foreach($productStats as $key => $cate)
                                <option value="{{ $key }}">{{ $cate['name'] }}</option>
                            @endforeach
                        </select>

                        <div class="input-group input-group-sm" style="max-width: 320px;">
                            <input type="date" id="dateStart" onchange="updateProductChart('date')" value="{{ date('Y-m-d', strtotime('-30 days')) }}" class="form-control text-secondary">
                            <span class="input-group-text bg-white border-start-0 border-end-0 text-muted">-</span>
                            <input type="date" id="dateEnd" onchange="updateProductChart('date')" value="{{ date('Y-m-d') }}" class="form-control text-secondary">
                        </div>
                    </div>
                </div>

                <div style="position: relative; height: 340px; width: 100%;">
                    <canvas id="topProductsChart"></canvas>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted text-center d-block">* Hệ thống tự động phân tích và tính toán doanh thu thực nhận</small>
                </div>
            </div>
        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    const colors = {
        primary:    '#3B82F6', 
        secondary:  '#1F2937', 
        success:    '#10B981', 
        warning:    '#F59E0B', 
        info:       '#06B6D4', 
        purple:     '#8B5CF6', 
        lightBlue:  '#93C5FD', 
        piePalette: ['#3B82F6', '#2DD4BF', '#F472B6', '#A78BFA', '#fbbf24', '#f87171']
    };

    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#9ca3af';

    // 1. BIỂU ĐỒ ĐƯỜNG
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const gradientRevenue = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    gradientRevenue.addColorStop(0, 'rgba(59, 130, 246, 0.2)'); 
    gradientRevenue.addColorStop(1, 'rgba(59, 130, 246, 0)');

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: @json($revenueData['labels']),
            datasets: [{
                label: 'Doanh thu',
                data: @json($revenueData['data']),
                borderColor: colors.primary,
                backgroundColor: gradientRevenue,
                borderWidth: 2.5,
                fill: true,
                pointRadius: 2,
                pointHoverRadius: 6,
                hitRadius: 30, 
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => v / 1000000 + ' tr' } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. BIỂU ĐỒ TRÒN 1
    // Cập nhật: Thêm màu thứ 3 (colors.info) để khớp với 3 nhãn: Gói tập, Sản phẩm, Lớp học
    const structureColors = [colors.purple, colors.success, colors.info];
    const ctxStructure = document.getElementById('revenueStructureChart');
    @if(array_sum($structureData['data']) > 0)
        new Chart(ctxStructure, {
            type: 'pie',
            data: {
                labels: @json($structureData['labels']),
                datasets: [{
                    data: @json($structureData['data']),
                    backgroundColor: structureColors,
                    borderWidth: 2
                }]
            },
            options: { plugins: { legend: { display: false } }, responsive: true, maintainAspectRatio: false }
        });
        @foreach($structureData['labels'] as $index => $label)
            document.getElementById('legend-color-struct-{{$index}}').style.backgroundColor = structureColors[{{$index}}];
        @endforeach
    @endif

    // 3. BIỂU ĐỒ TRÒN 2
    const ctxPackage = document.getElementById('packageChart');
    @if(array_sum($packageData['data']) > 0)
        new Chart(ctxPackage, {
            type: 'pie',
            data: {
                labels: @json($packageData['labels']),
                datasets: [{
                    data: @json($packageData['data']),
                    backgroundColor: colors.piePalette,
                    borderWidth: 2
                }]
            },
            options: { plugins: { legend: { display: false } }, responsive: true, maintainAspectRatio: false }
        });
        @foreach($packageData['labels'] as $index => $label)
             if(document.getElementById('legend-color-pkg-{{$index}}')) {
                 document.getElementById('legend-color-pkg-{{$index}}').style.backgroundColor = colors.piePalette[{{$index}} % colors.piePalette.length];
             }
        @endforeach
    @endif

    // 4. BIỂU ĐỒ CỘT
    new Chart(document.getElementById('newUsersChart'), {
        type: 'bar',
        data: {
            labels: @json($newMemberData['labels']),
            datasets: [{
                data: @json($newMemberData['data']),
                backgroundColor: colors.lightBlue,
                hoverBackgroundColor: colors.primary,
                borderRadius: 4,
                barThickness: 45,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: { x: { grid: { display: false } }, y: { beginAtZero: true } }
        }
    });

    // 5. BIỂU ĐỒ SẢN PHẨM HOÀN CHỈNH
    const ctxTopProducts = document.getElementById('topProductsChart').getContext('2d');
    let globalProductData = @json($productStats ?? []); 

    const gradientProduct = ctxTopProducts.createLinearGradient(0, 0, 400, 0);
    gradientProduct.addColorStop(0, '#3B82F6');
    gradientProduct.addColorStop(1, '#93C5FD');

    let productChart = new Chart(ctxTopProducts, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Doanh thu',
                data: [],
                quantities: [], 
                backgroundColor: gradientProduct,
                borderRadius: 4,
                barThickness: 16,
            }]
        },
        options: {
            indexAxis: 'y', 
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let revenue = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.raw);
                            let qty = context.dataset.quantities[context.dataIndex];
                            return [`Doanh thu: ${revenue}`, `Đã bán: ${qty} sản phẩm`];
                        }
                    }
                }
            },
            scales: { x: { beginAtZero: true, ticks: { callback: v => v / 1000000 + ' tr' } } }
        }
    });

    function renderChartFromData() {
        const catKey = document.getElementById('categoryFilter').value;
        let sourceData;

        if (catKey === 'all') {
            let allProducts = [];
            let allData = [];
            let allQuantities = [];
            
            if (Object.keys(globalProductData).length === 0) {
                 sourceData = { products: [], data: [], quantities: [] };
            } else {
                Object.values(globalProductData).forEach(cat => {
                    allProducts = allProducts.concat(cat.products);
                    allData = allData.concat(cat.data);
                    allQuantities = allQuantities.concat(cat.quantities);
                });
                sourceData = { products: allProducts, data: allData, quantities: allQuantities };
            }
        } else {
            sourceData = globalProductData[catKey] || { products: [], data: [], quantities: [] };
        }

        let combinedArray = sourceData.products.map((name, index) => {
            return { name: name, revenue: sourceData.data[index], qty: sourceData.quantities[index] };
        });

        combinedArray.sort((a, b) => b.qty - a.qty);
        let top10 = combinedArray.slice(0, 10);

        productChart.data.labels = top10.map(item => item.name);
        productChart.data.datasets[0].data = top10.map(item => item.revenue);
        productChart.data.datasets[0].quantities = top10.map(item => item.qty);
        productChart.update();
    }

    function fetchProductData() {
        const start = document.getElementById('dateStart').value;
        const end = document.getElementById('dateEnd').value;
        document.getElementById('topProductsChart').style.opacity = 0.5;

        fetch(`/admin/dashboard/filter-products?start_date=${start}&end_date=${end}`)
            .then(res => res.json())
            .then(data => {
                globalProductData = data;
                renderChartFromData();
                document.getElementById('topProductsChart').style.opacity = 1;
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById('topProductsChart').style.opacity = 1;
            });
    }

    function updateProductChart(triggerSource) {
        if (triggerSource === 'date') { fetchProductData(); } else { renderChartFromData(); }
    }

    renderChartFromData();

    /**
     * SỬA LỖI: HÀM XUẤT ĐƯỢC CHUẨN HÓA LẠI CHUỖI PROMISE VÀ BẮT ĐỘ RỘNG DESKTOP
     */
    function exportDashboardToPDF() {
    if (typeof html2pdf === 'undefined') {
        alert('Thư viện PDF đang được tải, vui lòng thử lại sau giây lát!');
        return;
    }

    const btn = document.getElementById('exportPdfBtn');
    const element = document.getElementById('dashboard-content');
    const originalText = btn.innerHTML;

    // Hiển thị trạng thái đang xử lý
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Đang khởi tạo...';
    btn.disabled = true;

    // Tùy chọn cấu hình PDF sạch, tăng độ tương thích với Canvas đồ thị của Chart.js
    const today = new Date().toLocaleDateString('vi-VN').replace(/\//g, '-');
    const opt = {
        margin: [10, 10, 10, 10], 
        filename: `Bao-cao-Gym-${today}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2, 
            useCORS: true, 
            logging: false
        },
        jsPDF: { unit: 'mm', format: 'a3', orientation: 'landscape' }
    };

    // Thực hiện chuyển đổi bằng cú pháp Promise rút gọn chuẩn của thư viện
    html2pdf().set(opt).from(element).save().then(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    }).catch(err => {
        console.error('Lỗi chi tiết xuất PDF:', err);
        alert('Có lỗi xảy ra khi tạo PDF. Bạn có thể nhấn F12 để kiểm tra tab Console.');
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>
@endsection