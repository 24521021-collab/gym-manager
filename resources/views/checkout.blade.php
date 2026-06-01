@extends('layout.frontend')
@section('content')
<form action="{{ route('checkout.process') }}" method="POST" class="container mt-5">
    @csrf
<div class="card shadow p-4 mb-4">
    <h4><i class="fa-solid fa-credit-card"></i> Phương thức thanh toán</h4>
    <hr>
    <div class="form-check mb-3">
        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
        <label class="form-check-label" for="cod">
            <strong>Thanh toán khi nhận hàng (COD)</strong>
            <p class="text-muted small">Bạn sẽ thanh toán tiền mặt cho shipper khi nhận được hàng.</p>
        </label>
    </div>
    
    <div class="form-check mb-3">
        <input class="form-check-input" type="radio" name="payment_method" id="bank_qr" value="Bank_QR">
        <label class="form-check-label" for="bank_qr">
            <strong>Chuyển khoản qua mã QR Ngân hàng (VietQR)</strong>
            <p class="text-muted small">Quét mã QR để thanh toán nhanh qua ứng dụng Ngân hàng.</p>
        </label>
    </div>

    <div class="form-check">
        <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="VNPAY">
        <label class="form-check-label" for="vnpay">
            <strong>Thanh toán qua VNPAY</strong>
            <p class="text-muted small">Thanh toán qua QR Code, ATM hoặc thẻ quốc tế nhanh chóng, bảo mật.</p>
        </label>
    </div>
</div>

<button type="submit" id="btn-checkout" class="btn btn-primary btn-lg w-100">Xác nhận và Thanh toán</button>
</form>
@endsection