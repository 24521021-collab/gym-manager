@extends('layout.frontend')
@section('content')

<div class="min-h-screen bg-[#0F0F0F] py-12 px-4 sm:px-6 lg:px-8 text-gray-200 font-sans flex items-center justify-center">
    
    <div class="w-full max-w-4xl">
        
        <div class="bg-[#141414] rounded-2xl border border-white/5 shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-2">
            
            <div class="p-6 md:p-8 bg-[#1A1A1A]/50 border-r border-white/5 flex flex-col justify-center items-center text-center">
                <div class="mb-4">
                    <h5 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-1">Mã QR Thanh Toán</h5>
                    <p class="text-xs text-gray-500">Mở ứng dụng Mobile Banking để quét mã</p>
                </div>

                @php
                    $bankId = "ACB"; // Ví dụ: MB Bank. Bạn thay bằng ngân hàng của bạn
                    $accountNo = "31438387"; // Thay bằng STK của bạn
                    $accountName = "PHONG TAP GYMPRO"; // Thay bằng tên chủ TK
                    $amount = $order->total_amount;
                    $info = "Thanh toan don hang " . $order->id;
                    $qrUrl = "https://img.vietqr.io/image/{$bankId}-{$accountNo}-compact2.png?amount={$amount}&addInfo=" . urlencode($info) . "&accountName=" . urlencode($accountName);
                @endphp

                <div class="bg-[#1F1F1F] p-4 rounded-2xl border border-white/10 shadow-inner relative group max-w-[280px] w-full">
                    <div class="absolute inset-0 bg-red-600/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                    
                    <img src="{{ $qrUrl }}" alt="Mã QR Thanh Toán" class="w-full h-auto shadow-md rounded-xl border border-white/5 object-cover transition-transform group-hover:scale-[1.01]">
                </div>

                <div class="mt-4 flex items-center gap-2 text-xs text-red-400 font-medium">
                    <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                    Hệ thống tự động kiểm tra giao dịch
                </div>
            </div>

            <div class="p-6 md:p-8 flex flex-col justify-between">
                
                <div>
                    <div class="mb-6">
                        <span class="text-xs font-bold text-red-500 uppercase tracking-wider block mb-1">KOR GYM Premium</span>
                        <h4 class="text-xl md:text-2xl font-bold tracking-tight text-white uppercase">
                            Thông Tin Chuyển Khoản
                        </h4>
                    </div>

                    <div class="space-y-3.5 mb-6">
                        <div class="flex justify-between items-center border-b border-white/5 pb-2.5">
                            <span class="text-sm text-gray-400">Chủ tài khoản</span>
                            <span class="text-sm text-white font-semibold uppercase tracking-wide">{{ $accountName }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/5 pb-2.5">
                            <span class="text-sm text-gray-400">Số tài khoản</span>
                            <span class="text-sm text-white font-mono font-bold tracking-wider">{{ $accountNo }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/5 pb-2.5">
                            <span class="text-sm text-gray-400">Ngân hàng</span>
                            <span class="text-sm text-red-500 font-extrabold uppercase tracking-wide">{{ $bankId }}</span>
                        </div>
                        <div class="flex justify-between items-center border-b border-white/5 pb-2.5">
                            <span class="text-sm text-gray-400">Số tiền cần thanh toán</span>
                            <span class="text-base text-red-500 font-bold">{{ number_format($amount) }}đ</span>
                        </div>
                        
                        <div class="flex flex-col gap-1.5 pt-1">
                            <span class="text-xs text-gray-400 font-medium">Nội dung chuyển khoản bắt buộc:</span>
                            <div class="bg-[#1F1F1F] border border-white/10 rounded-xl px-4 py-2 text-center text-red-400 font-mono text-sm font-semibold select-all cursor-pointer transition-colors hover:border-red-600/30" title="Nhấp chuột để tự động bôi đen nội dung">
                                {{ $info }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-amber-950/20 border border-amber-500/20 text-amber-400 rounded-xl p-3.5 text-xs flex items-start gap-2.5 leading-relaxed mb-6">
                        <span class="material-symbols-outlined text-sm md:text-base text-amber-500 mt-0.5 shrink-0">info</span>
                        <div>
                            Sau khi chuyển khoản xong, vui lòng giữ lại hóa đơn và chờ hệ thống xác nhận tự động hoặc liên hệ trực tiếp qua Hotline.
                        </div>
                    </div>
                </div>

                <div class="border-t border-white/5 pt-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <span class="text-xs text-gray-500 font-mono order-2 sm:order-1">
                        Đơn hàng: #{{ $order->id }}
                    </span>
                    <a href="{{ route('products.index') }}" class="w-full sm:w-auto bg-red-600 hover:bg-red-700 text-white font-semibold px-6 py-2.5 rounded-xl shadow-lg shadow-red-900/10 text-xs md:text-sm tracking-wide transition-all uppercase text-center order-1 sm:order-2">
                        Quay lại trang chủ
                    </a>
                </div>

            </div>

        </div>
    </div>
</div>

@endsection