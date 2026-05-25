@extends('layout.frontend')

@section('content')
<div class="max-w-7xl mx-auto px-4 md:px-8 py-8">
    <h2 class="font-headline text-2xl uppercase tracking-wider text-white mb-6 flex items-center gap-2 italic">
        <span class="material-symbols-outlined text-primary text-3xl">shopping_cart</span> Giỏ hàng & Thanh toán
    </h2>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-primary/10 border border-primary/20 text-primary rounded-xl text-sm">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('checkout.process') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="col-span-12 lg:col-span-8">
                <div class="bg-[#1A1A1A] rounded-2xl p-6 border border-white/10 shadow-2xl">
                    <h3 class="font-headline text-lg uppercase tracking-wider text-white border-b border-white/10 pb-3 mb-4">
                        Chi tiết giỏ hàng
                    </h3>

                    @if(session('cart') && count(session('cart')) > 0)
                        <div class="space-y-4 max-h-[450px] overflow-y-auto pr-2">
                            @php $total = 0; @endphp
                            @foreach(session('cart') as $id => $details)
                                @php 
                                    $total += $details['price'] * $details['quantity'];
                                    // Kiểm tra loại hình để lấy đường dẫn ảnh chính xác
                                    $imagePath = $details['item_type'] === 'product' 
                                        ? asset('images/products/' . ($details['image'] ?? 'default-product.jpg'))
                                        : asset('images/products/' . ($details['image'] ?? 'default-class.jpg'));
                                @endphp
                                <div data-id="{{ $id }}" class="flex flex-col sm:flex-row items-start sm:items-center justify-between p-3 bg-black/20 rounded-xl border border-white/5 gap-4">
                                    <div class="flex items-center gap-4 flex-1">
                                        <img src="{{ $imagePath }}" class="w-16 h-16 object-cover rounded-lg border border-white/10" alt="Item">
                                        <div>
                                            <h4 class="font-bold text-sm text-white line-clamp-1">{{ $details['name'] }}</h4>
                                            <p class="text-[11px] text-gray-500 mt-0.5">
                                                Loại: <span class="text-primary uppercase font-bold">{{ $details['item_type'] == 'product' ? 'Sản phẩm' : 'Lớp học / Gói tập' }}</span>
                                            </p>
                                            <p class="text-xs text-primary font-headline mt-1 sm:hidden">
                                                {{ number_format($details['price']) }}đ
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between sm:justify-end w-full sm:w-auto gap-6 border-t sm:border-t-0 pt-2 sm:pt-0 border-white/5">
                                        <div class="hidden sm:block text-right">
                                            <p class="text-xs text-gray-400 font-headline">{{ number_format($details['price']) }}đ</p>
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <input type="number" value="{{ $details['quantity'] }}" min="1" max="100" class="update-cart-quantity w-16 bg-black/40 border border-white/10 rounded-lg px-2 py-1 text-center text-xs text-white focus:outline-none focus:border-primary">
                                        </div>

                                        <div class="text-right min-w-[80px]">
                                            <p class="text-sm font-headline text-white font-bold"><span class="subtotal-price">{{ number_format($details['price'] * $details['quantity']) }}</span>đ</p>
                                        </div>

                                        <button type="button" class="remove-from-cart text-gray-500 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined text-xl block">delete</span>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 italic text-sm">Giỏ hàng đang trống.</p>
                            <a href="/shop" class="inline-block mt-4 px-6 py-2.5 bg-white/5 border border-white/10 hover:border-primary text-white text-xs uppercase font-headline rounded-xl transition-all">Quay lại chọn gói</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-span-12 lg:col-span-4">
                @if(session('cart') && count(session('cart')) > 0)
                    <div class="space-y-6 sticky top-24">
                        
                        <div class="bg-[#1A1A1A] rounded-2xl p-5 border border-white/10 shadow-2xl space-y-4">
                            <h3 class="font-headline text-base uppercase tracking-wider text-white border-b border-white/10 pb-3">
                                Phương thức thanh toán
                            </h3>
                            <div class="space-y-3 text-xs">
                                <label class="flex items-start gap-3 p-3 bg-black/20 border border-white/5 rounded-xl cursor-pointer hover:border-primary/40 transition-colors">
                                    <input type="radio" name="payment_method" value="COD" checked class="mt-0.5 text-primary focus:ring-0 focus:ring-offset-0 bg-transparent border-white/20">
                                    <div>
                                        <strong class="text-white block mb-0.5">Thanh toán trực tiếp (COD / Tại quầy)</strong>
                                        <p class="text-gray-500 text-[10px]">Thanh toán tiền mặt tại quầy lễ tân hoặc khi nhận thẻ hội viên.</p>
                                    </div>
                                </label>
                                
                                <label class="flex items-start gap-3 p-3 bg-black/20 border border-white/5 rounded-xl cursor-pointer hover:border-primary/40 transition-colors">
                                    <input type="radio" name="payment_method" value="Bank_QR" class="mt-0.5 text-primary focus:ring-0 focus:ring-offset-0 bg-transparent border-white/20">
                                    <div>
                                        <strong class="text-white block mb-0.5">Chuyển khoản nhanh qua VietQR</strong>
                                        <p class="text-gray-500 text-[10px]">Quét mã QR hiển thị để thanh toán nhanh bằng Mobile Banking.</p>
                                    </div>
                                </label>

                                <label class="flex items-start gap-3 p-3 bg-black/20 border border-white/5 rounded-xl cursor-pointer hover:border-primary/40 transition-colors">
                                    <input type="radio" name="payment_method" value="VNPAY" class="mt-0.5 text-primary focus:ring-0 focus:ring-offset-0 bg-transparent border-white/20">
                                    <div>
                                        <strong class="text-white block mb-0.5">Ứng dụng VNPAY / Thẻ ATM</strong>
                                        <p class="text-gray-500 text-[10px]">Kết nối cổng VNPAY quét mã hoặc dùng thẻ ATM nội địa.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="bg-[#1A1A1A] rounded-2xl p-5 border border-white/10 shadow-2xl space-y-4">
                            <div class="flex justify-between items-center border-b border-white/10 pb-3">
                                <h2 class="font-headline text-base uppercase tracking-wider text-white">Tóm tắt thanh toán</h2>
                                <span class="bg-primary/20 text-primary border border-primary/30 text-[10px] font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                    {{ count(session('cart')) }} Mục
                                </span>
                            </div>

                            <div class="border-t border-white/10 pt-1 space-y-2 text-xs">
                                <div class="flex justify-between text-gray-400">
                                    <span>Tạm tính</span>
                                    <span class="total-cart-price">{{ number_format($total) }}đ</span>
                                </div>
                                <div class="flex justify-between text-gray-400">
                                    <span>Phí kích hoạt & VAT</span>
                                    <span class="text-emerald-400">Miễn phí</span>
                                </div>
                                <div class="flex justify-between font-headline text-lg text-white border-t border-dashed border-white/10 pt-3 mt-1">
                                    <span>Tổng số tiền</span>
                                    <span class="text-primary-container total-cart-price">{{ number_format($total) }}đ</span>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3 bg-primary hover:bg-red-700 text-white font-headline text-base uppercase rounded-xl transition-all shadow-lg shadow-primary/20 block text-center font-bold">
                                Xác nhận đăng ký & Thanh toán
                            </button>
                        </div>

                    </div>
                @endif
            </div>

        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // 1. AJAX thay đổi số lượng gói tập/sản phẩm
    $(".update-cart-quantity").on('change keyup', function () {
        var ele = $(this);
        var id = ele.closest("div[data-id]").attr("data-id");
        var quantity = ele.val();

        if (quantity === "" || quantity < 1) return;

        $.ajax({
            url: '{{ route("cart.update") }}',
            method: "patch",
            data: {
                _token: '{{ csrf_token() }}',
                row_id: id,
                quantity: quantity
            },
            success: function (response) {
                ele.closest("div[data-id]").find(".subtotal-price").text(response.subtotal);
                $(".total-cart-price").text(response.total + "đ");
            },
            error: function(xhr) {
                alert("Lỗi: Không thể cập nhật số lượng.");
                window.location.reload();
            }
        });
    });

    // 2. AJAX xóa nhanh gói tập khỏi giỏ hàng
    $(".remove-from-cart").click(function (e) {
        e.preventDefault();
        var ele = $(this);
        var id = ele.closest("div[data-id]").attr("data-id");

        if(confirm("Bạn có chắc chắn muốn bỏ mục này khỏi giỏ hàng không?")) {
            $.ajax({
                url: '{{ route("cart.remove") }}',
                method: "DELETE",
                data: {
                    _token: '{{ csrf_token() }}',
                    row_id: id
                },
                success: function (response) {
                    ele.closest("div[data-id]").fadeOut(400, function() {
                        $(this).remove();
                        window.location.reload();
                    });
                },
                error: function (xhr) {
                    alert("Lỗi: Không thể xóa mục này.");
                }
            });
        }
    });
});
</script>
@endsection