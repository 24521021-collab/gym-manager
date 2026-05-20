@extends('layout.frontend')
@section('content')
<style>
    .class-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        transition: transform 0.25s, box-shadow 0.25s;
        overflow: hidden;
    }
    .class-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.15);
    }
    .class-card .card-top-bar {
        height: 5px;
        background: linear-gradient(90deg, #0d6efd, #6610f2);
    }
    .class-card .card-top-bar.full   { background: linear-gradient(90deg, #dc3545, #fd7e14); }
    .class-card .card-top-bar.booked { background: linear-gradient(90deg, #198754, #20c997); }
    .progress-slot { height: 5px; border-radius: 10px; }
    .status-badge {
        position: absolute; top: 14px; right: 14px;
        font-size: 11px; font-weight: 600;
        padding: 3px 10px; border-radius: 20px;
    }
    .badge-full   { background: #fee2e2; color: #dc3545; }
    .badge-booked { background: #d1fae5; color: #198754; }
    .hero-classes {
        background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
        padding: 50px 0 35px;
        color: white;
        margin-bottom: 35px;
    }
    .filter-bar {
        background: white;
        border-radius: 12px;
        padding: 14px 18px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        margin-bottom: 28px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 8px;
    }
    .btn-register {
        background: linear-gradient(135deg, #0d6efd, #6610f2);
        border: none; color: white;
        border-radius: 8px; font-weight: 600;
        transition: opacity .2s;
    }
    .btn-register:hover { opacity: .88; color: white; }
    .btn-cancel {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #6c757d;
        border-radius: 8px; font-weight: 500;
    }
    .btn-cancel:hover { background: #fee2e2; color: #dc3545; border-color: #dc3545; }
    .info-icon { color: #0d6efd; }
</style>

{{-- Hero --}}
<div class="hero-classes">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold mb-2">
                    <i class="fas fa-calendar-alt me-3"></i>Đăng Ký Lớp Học
                </h1>
                <p class="mb-0 opacity-75 fs-5">Chọn lớp học phù hợp và đặt chỗ ngay hôm nay</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="d-inline-block bg-white bg-opacity-20 rounded-3 px-4 py-3 text-center">
                    <div class="fs-2 fw-bold">{{ $classes->count() }}</div>
                    <div class="small opacity-75">Lớp học có sẵn</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">

    {{-- Thông báo --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm mb-4">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter bar --}}
    <div class="filter-bar">
        <span class="fw-semibold text-secondary me-1">
            <i class="fas fa-filter me-1"></i>Lọc:
        </span>
        <button class="btn btn-sm btn-primary rounded-pill px-3 filter-btn active" data-filter="all">Tất cả</button>
        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 filter-btn" data-filter="available">Còn chỗ</button>
        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 filter-btn" data-filter="full">Hết chỗ</button>
        @auth
        <button class="btn btn-sm btn-outline-success rounded-pill px-3 filter-btn" data-filter="booked">Đã đăng ký</button>
        @endauth
        <div class="ms-auto">
            <input type="text" id="searchClass" class="form-control form-control-sm rounded-pill"
                   placeholder="🔍 Tìm lớp học..." style="min-width:200px;">
        </div>
    </div>
    {{-- Danh sách --}}
    @if($classes->isEmpty())
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Chưa có lớp học nào</h5>
        </div>
    @else
        <div class="row g-4" id="classesGrid">
            @foreach($classes as $class)
                @php
                    $remaining = $class->max_capacity - $class->booked_count;
                    $isFull    = $remaining <= 0;
                    $isBooked  = in_array($class->id, $bookedClassIds);
                    $percent   = $class->max_capacity > 0
                                    ? round(($class->booked_count / $class->max_capacity) * 100) : 0;
                    $ptName    = optional(optional($class->pt)->user)->full_name ?? 'Chưa cập nhật';
                    $barColor  = $isFull ? 'bg-danger' : ($percent >= 70 ? 'bg-warning' : 'bg-success');
                    $slotColor = $isFull ? 'text-danger' : ($percent >= 70 ? 'text-warning' : 'text-success');
                @endphp
                <div class="col-lg-4 col-md-6 class-item"
                     data-status="{{ $isFull ? 'full' : 'available' }}"
                     data-booked="{{ $isBooked ? 'true' : 'false' }}"
                     data-name="{{ strtolower($class->name) }}">
                    <div class="class-card card h-100 position-relative">
                        <div class="card-top-bar {{ $isBooked ? 'booked' : ($isFull ? 'full' : '') }}"></div>
                        <div class="card-img-top-wrapper" style="height: 200px; overflow: hidden;">
                            <img src="{{ asset('images/products/' . ($class->image ?? 'default-class.jpg')) }}"
                                 class="card-img-top w-100 h-100"
                                 style="object-fit: cover;"
                                 alt="{{ $class->name }}">
                        </div>

                        @if($isBooked)
                            <span class="status-badge badge-booked">
                                <i class="fas fa-check me-1"></i>Đã đăng ký
                            </span>
                        @elseif($isFull)
                            <span class="status-badge badge-full">
                                <i class="fas fa-ban me-1"></i>Hết chỗ
                            </span>
                        @endif
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-1">{{ $class->name }}</h5>
                            <p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 10; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $class->description }}
                            </p>
                            <div class="d-flex flex-column gap-2 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-check info-icon me-2 fa-fw"></i>
                                    <span class="small">Thời lượng: <strong>{{ $class->total_sessions }} buổi</strong></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-tie info-icon me-2 fa-fw"></i>
                                    <span class="small">PT: <strong>{{ $ptName }}</strong></span>
                                </div>
                                <div class="mt-2">
                                    <span class="h5 fw-bold text-primary">{{ number_format($class->price) }}đ</span>
                                    <span class="text-muted small">/ khóa</span>
                                </div>
                            </div>
                            {{-- Thanh chỗ --}}
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-muted">Sức chứa</span>
                                    <span class="{{ $slotColor }} fw-semibold">
                                        {{ $class->booked_count }}/{{ $class->max_capacity }}
                                        @if(!$isFull) · Còn {{ $remaining }} chỗ @endif
                                    </span>
                                </div>
                                <div class="progress progress-slot">
                                    <div class="progress-bar {{ $barColor }}" style="width:{{ $percent }}%"></div>
                                </div>
                            </div>

                            {{-- Nút hành động --}}
                            @auth
                                @if($isBooked)
                                    <form action="{{ route('classes.cancel') }}" method="POST"
                                          onsubmit="return confirm('Xác nhận hủy đăng ký lớp này?')">
                                        @csrf @method('DELETE')
                                        <input type="hidden" name="class_id" value="{{ $class->id }}">
                                        <button type="submit" class="btn btn-cancel w-100 py-2">
                                            <i class="fas fa-times me-2"></i>Hủy đăng ký
                                        </button>
                                    </form>
                                @elseif($isFull)
                                    <button class="btn btn-secondary w-100 py-2" disabled>
                                        <i class="fas fa-ban me-2"></i>Đã hết chỗ
                                    </button>
                                @else
                                    <form action="{{ route('classes.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="class_id" value="{{ $class->id }}">
                                        <button type="submit" class="btn btn-register w-100 py-2">
                                            <i class="fas fa-plus-circle me-2"></i>Đăng Ký Ngay
                                        </button>
                                    </form>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-primary w-100 py-2">
                                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập để đăng ký
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="emptyFilter" class="text-center py-5 d-none">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h6 class="text-muted">Không tìm thấy lớp học phù hợp</h6>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
const items      = document.querySelectorAll('.class-item');
const filterBtns = document.querySelectorAll('.filter-btn');
const searchInput = document.getElementById('searchClass');
const emptyDiv   = document.getElementById('emptyFilter');
let currentFilter = 'all';

filterBtns.forEach(btn => {
    btn.addEventListener('click', function () {
        filterBtns.forEach(b => b.classList.remove('active','btn-primary','btn-success','btn-danger'));
        this.classList.add('active');
        if (this.dataset.filter === 'booked') this.classList.add('btn-success');
        else if (this.dataset.filter === 'full') this.classList.add('btn-danger');
        else this.classList.add('btn-primary');
        currentFilter = this.dataset.filter;
        applyFilters();
    });
});

searchInput.addEventListener('input', applyFilters);

function applyFilters() {
    const kw = searchInput.value.toLowerCase().trim();
    let visible = 0;
    items.forEach(item => {
        const matchFilter =
            currentFilter === 'all' ||
            (currentFilter === 'available' && item.dataset.status === 'available') ||
            (currentFilter === 'full'      && item.dataset.status === 'full') ||
            (currentFilter === 'booked'    && item.dataset.booked === 'true');
        const matchSearch = !kw || item.dataset.name.includes(kw);
        item.style.display = (matchFilter && matchSearch) ? '' : 'none';
        if (matchFilter && matchSearch) visible++;
    });
    if (emptyDiv) emptyDiv.classList.toggle('d-none', visible > 0);
}

// Tự đóng alert sau 4 giây
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => new bootstrap.Alert(a).close());
}, 4000);
</script>
@endsection
