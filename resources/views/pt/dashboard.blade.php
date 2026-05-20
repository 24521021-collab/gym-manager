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
</div>
@endsection
