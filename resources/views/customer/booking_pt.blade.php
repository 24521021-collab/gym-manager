@extends('layout.frontend')
@section('content')

<div class="bg-background text-on-background min-h-screen antialiased selection:bg-primary-container selection:text-white py-8">
    <div class="max-w-7xl mx-auto px-4 md:px-8 space-y-8">
        
        <div class="border-b border-white/10 pb-4">
            <h1 class="font-headline text-3xl font-bold text-white uppercase italic tracking-tighter">
                Đặt lịch tập luyện <span class="text-primary">Personal Trainer</span>
            </h1>
            <p class="text-gray-400 text-sm mt-1 font-body">Thiết kế lộ trình kháng lực và tăng trưởng cơ bắp 1 kèm 1 chuyên nghiệp.</p>
        </div>

        {{-- Hiển thị thông báo phản hồi từ hệ thống --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 rounded-xl text-sm">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl text-sm">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            </div>
        @endif

        <form id="booking-main-form" action="{{ route('booking.pt.store') }}" method="POST">
            @csrf
            
            <input type="hidden" name="pt_id" id="hidden_pt_id" required>
            <input type="hidden" name="booking_date" id="hidden_booking_date" required>
            <input type="hidden" name="start_time" id="hidden_start_time" required>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <div class="lg:col-span-8 space-y-8">
                    
                    <div class="space-y-4">
                        <h2 class="font-headline text-lg uppercase tracking-wider text-white flex items-center gap-2">
                            <span class="w-2 h-6 bg-primary inline-block rounded-sm"></span>
                            Lựa chọn Huấn luyện viên cá nhân
                        </h2>

                        <div class="bg-[#1A1A1A]/50 border border-white/5 rounded-2xl p-4 space-y-2">
                            <label class="block text-[11px] text-gray-400 uppercase font-bold tracking-wider">Lọc theo mục tiêu / Chuyên môn:</label>
                            <div class="flex flex-wrap gap-2" id="spec-filter-container">
                                <button type="button" onclick="filterPTsBySpecialization('all', this)"
                                   class="spec-filter-btn px-4 py-2 text-xs font-bold border {{ $selectedSpec === 'all' ? 'border-primary bg-primary/10 text-primary' : 'border-white/10 bg-black/40 text-gray-400' }} rounded-full transition-all">
                                    Tất cả HLV
                                </button>
                                @foreach($allSpecializations as $spec)
                                <button type="button" onclick="filterPTsBySpecialization('{{ $spec }}', this)"
                                   class="spec-filter-btn px-4 py-2 text-xs font-bold border {{ $selectedSpec === $spec ? 'border-primary bg-primary/10 text-primary' : 'border-white/10 bg-black/40 text-gray-400' }} rounded-full hover:border-primary/40 hover:text-primary transition-all">
                                    {{ $spec }}
                                </button>
                                @endforeach
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="pt-cards-grid">
                            @foreach($pts as $pt)
                            @php
                                $ptSpec = $pt->ptProfile->specialization ?? 'Thể hình';
                                $ptImg = $pt->ptProfile->image ?? 'pt.jpg';
                            @endphp
                            
                            <div onclick="selectPersonalTrainer(this, {{ $pt->id }}, '{{ addslashes($pt->full_name) }}')" 
                                 data-specialization="{{ $ptSpec }}"
                                 class="pt-card-item bg-[#1A1A1A] border border-white/10 rounded-2xl p-4 flex gap-4 cursor-pointer hover:border-primary/5 transition-all">
                                <div class="relative w-20 h-20 flex-shrink-0">
                                    <div class="w-full h-full rounded-xl overflow-hidden bg-black/40 relative cursor-zoom-in js-zoom-container border border-white/5">
                                        <img src="{{ asset('images/pt/' . $ptImg) }}" class="w-full h-full object-cover js-main-img" alt="{{ $pt->full_name }}" onerror="this.src='{{ asset('images/pt/pt.jpg') }}'">
                                        {{-- Thấu kính Zoom --}}
                                        <div class="absolute bg-white/20 border border-white/40 pointer-events-none hidden js-zoom-lens" style="width: 30px; height: 30px; border-radius: 4px;"></div>
                                    </div>
                                    {{-- Hộp hiển thị kết quả Zoom --}}
                                    <div class="absolute left-full top-0 ml-3 w-48 h-48 border border-white/10 rounded-xl overflow-hidden bg-[#141414] shadow-2xl hidden z-50 js-zoom-result">
                                        <img src="{{ asset('images/pt/' . $ptImg) }}" class="absolute max-w-none js-high-res-img" style="width: 400%; height: 400%;" onerror="this.src='{{ asset('images/pt/pt.jpg') }}'"/>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0 space-y-1">
                                    <h3 class="font-bold text-white text-base truncate">{{ $pt->full_name }}</h3>
                                    <p class="text-xs text-primary font-medium">Chuyên môn: {{ $ptSpec }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h2 class="font-headline text-lg uppercase tracking-wider text-white flex items-center gap-2">
                            <span class="w-2 h-6 bg-primary inline-block rounded-sm"></span>
                            Ghi chú bổ sung (Mục tiêu / Chấn thương)
                        </h2>
                        
                        <textarea name="note" id="note" class="w-full bg-[#1A1A1A] border border-white/10 rounded-2xl p-4 text-sm text-white outline-none focus:border-primary transition-colors" rows="4" placeholder="Nhập mục tiêu tập luyện, thể trạng hoặc lưu ý chấn thương nếu có..."></textarea>
                    </div>

                </div>

                <div class="lg:col-span-4">
                    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 sticky top-24 space-y-6 shadow-2xl">
                        <div class="border-b border-white/10 pb-4">
                            <h2 class="font-headline text-base uppercase tracking-wider text-white">Cấu hình lịch hẹn</h2>
                            <p class="text-[11px] text-gray-500 mt-0.5">Hoàn tất các bước để chốt lịch tập.</p>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Bước 2: Chọn ngày tập</label>
                                <input type="date" id="date-picker-input" onchange="selectBookingDate(this.value)" 
                                       class="w-full bg-black/40 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white outline-none focus:border-primary transition-colors [color-scheme:dark]"
                                       min="{{ date('Y-m-d') }}" required>
                            </div>

                            <div>
                                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Bước 3: Chọn khung giờ</label>
                                <div id="time-slots-wrapper" class="grid grid-cols-3 gap-2 bg-black/20 border border-white/5 rounded-xl p-3">
                                    <p class="text-gray-500 text-[10px] italic col-span-full" id="slot-notice">Chọn HLV & Ngày...</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 text-xs font-body">
                            <div class="flex justify-between items-center bg-black/20 p-3 rounded-xl border border-white/5">
                                <span class="text-gray-400">Huấn luyện viên:</span>
                                <span id="summary-pt" class="font-bold text-white text-right">Chưa chọn</span>
                            </div>
                            <div class="flex justify-between items-center bg-black/20 p-3 rounded-xl border border-white/5">
                                <span class="text-gray-400">Ngày tập luyện:</span>
                                <span id="summary-date" class="font-bold text-primary text-right">Chưa chọn</span>
                            </div>
                            <div class="flex justify-between items-center bg-black/20 p-3 rounded-xl border border-white/5">
                                <span class="text-gray-400">Khung giờ tập:</span>
                                <span id="summary-time" class="font-bold text-white text-right">Chưa chọn</span>
                            </div>
                        </div>

                        <button type="button" onclick="confirmBookingAppointment()" id="btn-submit-booking"
                                class="w-full py-4 bg-primary hover:bg-red-700 text-white font-headline text-sm uppercase rounded-xl font-bold tracking-wider transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/20 opacity-50 cursor-not-allowed" disabled>
                            Xác nhận chốt lịch ngay
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // 1. Hệ thống biến trạng thái lưu trữ theo phong cách gốc
    let currentSelectedPT = '';      
    let currentSelectedPTId = '';    
    let currentSelectedDate = '';    
    let currentSelectedTime = '';    

    //001. CHỨC NĂNG LỌC PT THEO CHUYÊN MÔN (SỬ DỤNG FETCH API)
    function filterPTsBySpecialization(spec, btn) {
        // 1. Cập nhật trạng thái hiển thị của các nút lọc (Active/Inactive)
        document.querySelectorAll('.spec-filter-btn').forEach(b => {
            b.className = "spec-filter-btn px-4 py-2 text-xs font-bold border border-white/10 bg-black/40 text-gray-400 rounded-full hover:border-primary/40 hover:text-primary transition-all";
        });
        btn.className = "spec-filter-btn px-4 py-2 text-xs font-bold border border-primary bg-primary/10 text-primary rounded-full transition-all";
        const grid = document.getElementById('pt-cards-grid');
        grid.innerHTML = '<div class="col-span-full py-10 text-center text-primary animate-pulse italic">Đang lọc danh sách HLV chuyên nghiệp...</div>';
        // 2. Gửi yêu cầu lấy dữ liệu đã lọc về server
        const url = `{{ route('booking.pt.index') }}?specialization=${encodeURIComponent(spec)}`;
        
        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            grid.innerHTML = ''; // Xóa danh sách cũ
            
            if (data.pts.length === 0) {
                grid.innerHTML = '<div class="col-span-full py-10 text-center text-gray-500 italic">Không tìm thấy huấn luyện viên nào phù hợp với chuyên môn này.</div>';
                return;
            }

            // 3. Vẽ lại danh sách PT mới từ dữ liệu JSON nhận được
            data.pts.forEach(pt => {
                const ptSpec = pt.pt_profile ? pt.pt_profile.specialization : 'Thể hình';
                const ptImg = pt.pt_profile && pt.pt_profile.image ? pt.pt_profile.image : 'pt.jpg';
                const ptNameSafe = pt.full_name.replace(/'/g, "\\'"); // Chống lỗi JS khi tên có dấu nháy
                const assetBase = "{{ asset('images/pt') }}";

                grid.innerHTML += `
                    <div onclick="selectPersonalTrainer(this, ${pt.id}, '${ptNameSafe}')" 
                         class="pt-card-item bg-[#1A1A1A] border border-white/10 rounded-2xl p-4 flex gap-4 cursor-pointer hover:border-primary/5 transition-all">
                        <div class="relative w-20 h-20 flex-shrink-0">
                            <div class="w-full h-full rounded-xl overflow-hidden bg-black/40 relative cursor-zoom-in js-zoom-container border border-white/5">
                                <img src="${assetBase}/${ptImg}" class="w-full h-full object-cover js-main-img" alt="${pt.full_name}" onerror="this.src='${assetBase}/pt.jpg'">
                                <div class="absolute bg-white/20 border border-white/40 pointer-events-none hidden js-zoom-lens" style="width: 30px; height: 30px; border-radius: 4px;"></div>
                            </div>
                            <div class="absolute left-full top-0 ml-3 w-48 h-48 border border-white/10 rounded-xl overflow-hidden bg-[#141414] shadow-2xl hidden z-50 js-zoom-result">
                                <img src="${assetBase}/${ptImg}" class="absolute max-w-none js-high-res-img" style="width: 400%; height: 400%;" onerror="this.src='${assetBase}/pt.jpg'"/>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0 space-y-1">
                            <h3 class="font-bold text-white text-base truncate">${pt.full_name}</h3>
                            <p class="text-xs text-primary font-medium">Chuyên môn: ${ptSpec}</p>
                        </div>
                    </div>
                `;
            });
        })
        .catch(error => {
            console.error("Lỗi lọc chuyên môn:", error);
            grid.innerHTML = '<div class="col-span-full py-10 text-center text-red-500 italic">Lỗi hệ thống: Không thể đồng bộ dữ liệu vào lúc này.</div>';
        });
    }

    const defaultSlots = [
        "06:00", "07:00", "08:00", "09:00", "10:00", "11:00",
        "13:00", "14:00", "15:00", "16:00", "17:00", "18:00",
        "19:00"
    ];

    // 2. CHỨC NĂNG LỰA CHỌN HUẤN LUYỆN VIÊN 
    function selectPersonalTrainer(element, ptId, ptName) {
        document.querySelectorAll('.pt-card-item').forEach(card => {
            card.className = "pt-card-item bg-[#1A1A1A] border border-white/10 rounded-2xl p-4 flex gap-4 cursor-pointer hover:border-primary/5 transition-all";
        });
        
        element.className = "pt-card-item bg-[#1A1A1A] border border-primary bg-primary/5 rounded-2xl p-4 flex gap-4 cursor-pointer transition-all";
        
        currentSelectedPT = ptName;
        currentSelectedPTId = ptId;
        document.getElementById('hidden_pt_id').value = ptId;
        document.getElementById('summary-pt').innerText = ptName;

        checkAvailableTimeSlotsFromServer();
    }

    // 3. CHỨC NĂNG LỰA CHỌN NGÀY TẬP LUYỆN
    function selectBookingDate(dateStr) {
        currentSelectedDate = dateStr;
        document.getElementById('hidden_booking_date').value = dateStr;
        
        if(dateStr) {
            const parts = dateStr.split('-');
            document.getElementById('summary-date').innerText = `${parts[2]}/${parts[1]}/${parts[0]}`;
        } else {
            document.getElementById('summary-date').innerText = "Chưa chọn";
        }

        checkAvailableTimeSlotsFromServer();
    }

    // 4. BIÊN DỊCH VÀ TÍCH HỢP LOGIC KIỂM TRA LỊCH TRÙNG QUA AJAX
    function checkAvailableTimeSlotsFromServer() {
        const wrapper = document.getElementById('time-slots-wrapper');
        
        currentSelectedTime = '';
        document.getElementById('hidden_start_time').value = '';
        document.getElementById('summary-time').innerText = 'Chưa chọn';
        
        const submitBtn = document.getElementById('btn-submit-booking');
        submitBtn.disabled = true;
        submitBtn.className = "w-full py-4 bg-primary hover:bg-red-700 text-white font-headline text-sm uppercase rounded-xl font-bold tracking-wider transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/20 opacity-50 cursor-not-allowed";

        if (!currentSelectedPTId || !currentSelectedDate) {
            wrapper.innerHTML = '<p class="text-gray-500 text-xs italic col-span-full py-2" id="slot-notice">Vui lòng chọn Huấn luyện viên và Ngày tập để hệ thống quét ca trống...</p>';
            return;
        }

        wrapper.innerHTML = '<p class="text-primary text-xs col-span-full py-2 animate-pulse">Đang rà soát dữ liệu ca trực trống của HLV...</p>';

        fetch(`/api/pt-booked-slots?pt_id=${currentSelectedPTId}&date=${currentSelectedDate}`)
            .then(response => response.json())
            .then(bookedSlots => {
                wrapper.innerHTML = ''; 
                const formattedLockedHours = bookedSlots.map(slot => slot.start_time.substring(0, 5));
                defaultSlots.forEach(slot => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.innerText = slot;
                    
                    if (formattedLockedHours.includes(slot)) {
                        btn.className = "time-btn py-2 text-xs font-bold border border-white/5 bg-black/40 text-gray-600 rounded-lg opacity-30 cursor-not-allowed";
                        btn.disabled = true;
                        btn.title = "Ca tập này HLV đã kín lịch!";
                    } else {
                        btn.className = "time-btn py-2 text-xs font-bold border border-white/5 bg-black/20 text-white rounded-lg hover:border-primary/40 hover:text-primary transition-all";
                        btn.addEventListener('click', function() {
                            selectTimeSlot(this, slot);
                        });
                    }
                    wrapper.appendChild(btn);
                });
            })
            .catch(error => {
                console.error("Lỗi đồng bộ lịch PT:", error);
                wrapper.innerHTML = '<p class="text-danger text-xs col-span-full py-2">Lỗi máy chủ: Không thể đồng bộ lịch trực vào lúc này!</p>';
            });
    }

    // 5. CHỨC NĂNG BỐC CHỌN GIỜ TẬP LUYỆN
    function selectTimeSlot(element, timeStr) {
        document.querySelectorAll('.time-btn:not([disabled])').forEach(b => {
            b.className = "time-btn py-2 text-xs font-bold border border-white/5 bg-black/20 text-white rounded-lg hover:border-primary/40 hover:text-primary transition-all";
        });
        
        element.className = "time-btn py-2 text-xs font-bold border border-primary bg-primary/10 text-primary rounded-lg transition-all";
        
        currentSelectedTime = timeStr;
        document.getElementById('hidden_start_time').value = timeStr;
        document.getElementById('summary-time').innerText = timeStr;

        const submitBtn = document.getElementById('btn-submit-booking');
        submitBtn.disabled = false;
        submitBtn.className = "w-full py-4 bg-primary hover:bg-red-700 text-white font-headline text-sm uppercase rounded-xl font-bold tracking-wider transition-all flex items-center justify-center gap-2 shadow-lg shadow-primary/20 cursor-pointer";
    }

    // 6. HÀM XÁC NHẬN GỬI FORM THỰC TẾ LÊN CONTROLLER
    function confirmBookingAppointment() {
        if (!currentSelectedPTId || !currentSelectedDate || !currentSelectedTime) {
            alert("Lỗi tác vụ: Vui lòng hoàn thành đầy đủ 3 bước lựa chọn trước khi chốt lịch!");
            return;
        }
        document.getElementById('booking-main-form').submit();
    }

    /** 
     * LOGIC ZOOM HÌNH ẢNH PT (Áp dụng đồng bộ từ Shop)
     * Sử dụng Event Delegation để hoạt động với cả danh sách nạp qua Fetch API
     */
    $(document).ready(function() {
        $(document).on('mouseenter', '.js-zoom-container', function() {
            $(this).find('.js-zoom-lens').removeClass('hidden');
            $(this).parent().find('.js-zoom-result').removeClass('hidden');
        });

        $(document).on('mouseleave', '.js-zoom-container', function() {
            $(this).find('.js-zoom-lens').addClass('hidden');
            $(this).parent().find('.js-zoom-result').addClass('hidden');
        });

        $(document).on('mousemove', '.js-zoom-container', function(e) {
            const $container = $(this);
            const $lens = $container.find('.js-zoom-lens');
            const $resultBox = $container.parent().find('.js-zoom-result');
            const $highResImg = $resultBox.find('.js-high-res-img');

            const offset = $container.offset();
            let x = e.pageX - offset.left;
            let y = e.pageY - offset.top;

            let lensX = x - ($lens.width() / 2);
            let lensY = y - ($lens.height() / 2);

            if (lensX < 0) lensX = 0;
            if (lensY < 0) lensY = 0;
            if (lensX > $container.width() - $lens.width()) lensX = $container.width() - $lens.width();
            if (lensY > $container.height() - $lens.height()) lensY = $container.height() - $lens.height();

            $lens.css({ left: lensX + 'px', top: lensY + 'px' });

            const ratioX = ($highResImg.width() - $resultBox.width()) / ($container.width() - $lens.width());
            const ratioY = ($highResImg.height() - $resultBox.height()) / ($container.height() - $lens.height());

            $highResImg.css({
                left: '-' + (lensX * ratioX) + 'px',
                top: '-' + (lensY * ratioY) + 'px'
            });
        });
    });
</script>
@endsection