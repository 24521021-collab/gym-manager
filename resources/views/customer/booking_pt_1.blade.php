@extends('layout.frontend')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 mt-5">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-dumbbell me-2"></i> Hệ Thống Đặt Lịch Tập Riêng Với PT</h4>
                </div>
                <div class="card-body p-4">

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('booking.pt.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Bước 1: Chọn Huấn Luyện Viên (PT)</label>
                            <select name="pt_id" id="pt_id" class="form-control form-select" required>
                                <option value="">-- Bấm vào đây để chọn PT --</option>
                                @foreach($pts as $pt)
                                    <option value="{{ $pt->id }}">PT. {{ $pt->full_name }}</option> 
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Bước 2: Chọn Ngày Đăng Ký Tập</label>
                            <input type="date" name="booking_date" id="booking_date" class="form-control" min="{{ date('Y-m-d') }}" required disabled>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Bước 3: Chọn Khung Giờ Tập Còn Trống</label>
                            <div id="time_slots" class="d-flex flex-wrap gap-2 pt-2">
                                <span class="text-muted italic">Vui lòng chọn PT và ngày tập để kiểm tra các ca trống...</span>
                            </div>
                            <input type="hidden" name="start_time" id="selected_start_time" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Ghi chú gửi cho PT (Nếu có)</label>
                            <textarea name="note" id="note" class="form-control" rows="3" placeholder="Nhập mục tiêu tập luyện, thể trạng sức khỏe hoặc lưu ý chấn thương nếu có..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100" id="btn-submit" disabled>Xác Nhận Đặt Lịch Tập</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('pt_id').addEventListener('change', function() {
    const dateInput = document.getElementById('booking_date');
    if(this.value) {
        dateInput.disabled = false;
        fetchBookedSlots();
    } else {
        dateInput.disabled = true;
        dateInput.value = '';
        document.getElementById('time_slots').innerHTML = '<span class="text-muted">Vui lòng chọn PT và ngày tập để kiểm tra các ca trống...</span>';
    }
});
document.getElementById('booking_date').addEventListener('change', fetchBookedSlots);

function fetchBookedSlots() {
    const ptId = document.getElementById('pt_id').value;
    const date = document.getElementById('booking_date').value;
    const slotsContainer = document.getElementById('time_slots');
    
    if(!ptId || !date) return;
    
    slotsContainer.innerHTML = '<div class="text-muted"><span class="spinner-border spinner-border-sm me-2"></span> Đang quét lịch trống của PT...</div>';
    
    const defaultSlots = ["08:00", "09:00", "10:00", "11:00", "14:00", "15:00", "16:00", "17:00"];
    
    fetch(`/api/pt-booked-slots?pt_id=${ptId}&date=${date}`)
        .then(res => res.json())
        .then(bookedSlots => {
            slotsContainer.innerHTML = '';
            const lockedHours = bookedSlots.map(slot => slot.start_time.substring(0, 5));
            
            defaultSlots.forEach(slot => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.innerText = slot;
                btn.className = 'btn m-1 ';
                
                if(lockedHours.includes(slot)) {
                    btn.classList.add('btn-secondary', 'disabled');
                    btn.style.cursor = 'not-allowed';
                } else {
                    btn.classList.add('btn-outline-primary', 'time-slot-btn');
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.time-slot-btn').forEach(b => {
                            b.classList.remove('btn-primary');
                            b.classList.add('btn-outline-primary');
                        });
                        this.classList.remove('btn-outline-primary');
                        this.classList.add('btn-primary');
                        
                        document.getElementById('selected_start_time').value = slot;
                        document.getElementById('btn-submit').disabled = false;
                    });
                }
                slotsContainer.appendChild(btn);
            });
        })
        .catch(error => {
            slotsContainer.innerHTML = '<span class="text-danger">Có lỗi xảy ra khi tải lịch trống!</span>';
        });
}
</script>
@endsection