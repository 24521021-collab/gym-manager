@extends('layout.frontend')
@section('content')
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0 text-center p-4">
                <h4 class="fw-bold mb-3">Thanh Toán Chuyển Khoản</h4>
                <p class="text-muted">Vui lòng sử dụng ứng dụng Ngân hàng (Mobile Banking) để quét mã QR bên dưới.</p>
                
                <div class="bg-light p-3 rounded mb-4">
                    {{-- Sử dụng API VietQR để tạo mã tự động --}}
                    {{-- Cấu trúc: https://img.vietqr.io/image/<BANK_ID>-<ACCOUNT_NO>-<TEMPLATE>.png?amount=<AMOUNT>&addInfo=<INFO>&accountName=<NAME> --}}
                    @php
                        $bankId = "ACB"; // Ví dụ: MB Bank. Bạn thay bằng ngân hàng của bạn
                        $accountNo = "31438387"; // Thay bằng STK của bạn
                        $accountName = "PHONG TAP GYMPRO"; // Thay bằng tên chủ TK
                        $amount = $order->total_amount;
                        $info = "Thanh toan don hang " . $order->id;
                        $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo=" . urlencode($info) . "&accountName=" . urlencode($accountName);
                    @endphp
                    <img src="{{ $qrUrl }}" alt="Mã QR Thanh Toán" class="img-fluid shadow-sm rounded" style="max-width: 300px;">
                </div>

                <div class="text-start mb-4">
                    <p class="mb-1"><strong>Chủ tài khoản:</strong> {{ $accountName }}</p>
                    <p class="mb-1"><strong>Số tài khoản:</strong> {{ $accountNo }}</p>
                    <p class="mb-1"><strong>Ngân hàng:</strong> {{ $bankId }}</p>
                    <p class="mb-1"><strong>Số tiền:</strong> <span class="text-danger fw-bold">{{ number_format($amount) }}đ</span></p>
                    <p class="mb-0"><strong>Nội dung:</strong> <span class="text-primary">{{ $info }}</span></p>
                </div>

                <div class="alert alert-warning small">
                    <i class="fas fa-info-circle me-1"></i> Sau khi chuyển khoản xong, vui lòng chờ hệ thống xác nhận hoặc liên hệ Hotline để được hỗ trợ nhanh nhất.
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('products.index') }}" class="btn btn-dark">Quay lại trang chủ</a>
                    <small class="text-muted">Đơn hàng #{{ $order->id }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
