@extends('layout.admin_layout') @section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold">Lịch Sử Điểm Danh</h3>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#qrModal">
            <i class="fas fa-qrcode"></i> Quét Mã Điểm Danh
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <!-- Form Tìm kiếm -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <form action="{{ route('admin.checkin') }}" method="GET" class="d-flex">
                        <input type="text" name="search" class="form-control me-2" placeholder="Tìm tên hội viên..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">Tìm</button>
                        @if(request('search'))
                            <a href="{{ route('admin.checkin') }}" class="btn btn-outline-secondary ms-1 text-nowrap">Xóa lọc</a>
                        @endif
                    </form>
                </div>
            </div>
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Hội viên</th>
                        <th>Thời gian</th>
                        <th>Phương thức</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentCheckins as $checkin)
                    <tr>
                        <td>{{ $checkin->user->full_name }}</td>
                        <td>{{ $checkin->check_in_time}}</td>
                        <td><span class="badge bg-info">{{ $checkin->method }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="d-flex justify-content-center mt-3">
                {{ $recentCheckins->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Máy Quét QR Hội Viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" onclick="stopScanner()"></button>
            </div>
            <div class="modal-body">
                <div id="reader" style="width: 100%;"></div>
                <div id="result" class="alert mt-3 d-none"></div>
            </div>
        </div>
    </div>
</div>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner = null;

    /** * 1. KÍCH HOẠT CAMERA KHI MỞ MODAL
     * Sự kiện 'shown.bs.modal' của Bootstrap đảm bảo camera chỉ bật khi ô popup đã hiện ra hoàn toàn
     */
    document.getElementById('qrModal').addEventListener('shown.bs.modal', function () {
        // Khởi tạo máy quét với tốc độ 10 khung hình/giây và khung ngắm 250px
        html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    });

    /** * 2. TẮT CAMERA KHI ĐÓNG MODAL
     * Giúp tiết kiệm tài nguyên máy tính và bảo mật (tắt đèn camera)
     */
    function stopScanner() {
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().catch(err => console.error(err));
        }
        // Tự động tải lại trang (F5) để bảng danh sách bên ngoài cập nhật người vừa check-in
        location.reload(); 
    }
    /** * 3. XỬ LÝ KHI QUÉT TRÚNG MÃ QR
     * decodedText: Chính là ID người dùng chứa trong mã QR
     */
    // Biến cờ (flag) để ngăn việc gửi nhiều yêu cầu cùng lúc khi đang xử lý
let isProcessing = false;
function onScanSuccess(decodedText) {
    // Nếu đang xử lý một mã rồi thì thoát ra, không làm gì cả
    if (isProcessing) return;
    // Đánh dấu bắt đầu xử lý
    isProcessing = true;
    // Gửi ID lên Server
    fetch("{{ route('admin.checkin.store') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ user_id: decodedText })
    })
    .then(response => response.json())
    .then(data => {
        let resDiv = document.getElementById('result');
        resDiv.classList.remove('d-none', 'alert-success', 'alert-danger');
        if(data.success) {
            resDiv.classList.add('alert-success');
            resDiv.innerHTML = `✅ ${data.user_name}: Thành công!`;
            // --- ĐOẠN QUAN TRỌNG: TỰ ĐỘNG TẮT MODAL ---
            // Đợi 1 giây để Admin kịp nhìn thấy chữ "Thành công" rồi mới tắt
            setTimeout(() => {
                // Lấy đối tượng Modal hiện tại
                let myModalEl = document.getElementById('qrModal');
                let modal = bootstrap.Modal.getInstance(myModalEl);
                if (modal) {
                    modal.hide(); // Tắt ô quét QR
                }
                // 2. Tự động LOAD LẠI TRANG để bảng danh sách cập nhật hội viên mới
                location.reload(); 
            }, 2000);
        } else {
                // Nếu có lỗi (hết hạn, không tồn tại): Hiện thông báo màu đỏ
                resDiv.classList.add('alert-danger');
                resDiv.innerHTML = `❌ ${data.message}`;
                isProcessing = false; // Cho phép quét lại sau khi hiện lỗi
            }
        })
        .catch(err => {
            console.error("Lỗi kết nối:", err);
            isProcessing = false;
        });
    }
</script>
@endsection