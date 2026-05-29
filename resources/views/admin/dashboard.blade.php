@extends('layout.admin_layout')

@section('content')
    <style>
        /* Animation cho tia quét laser */
        @keyframes scan-line {
            0% { transform: translateY(0); opacity: 0; }
            10% { opacity: 1; }
            90% { opacity: 1; }
            100% { transform: translateY(110px); opacity: 0; }
        }
        .animate-scan-line {
            animation: scan-line 2.5s ease-in-out infinite;
        }
        /* Custom thanh cuộn cho danh sách điểm danh */
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.1); border-radius: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(255, 255, 255, 0.2); }
    </style>
    
    <!-- HEADER -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-6">
        <div>
            <h2 class="font-headline text-3xl md:text-4xl text-white uppercase tracking-tight">THỐNG KÊ DOANH THU <span class="text-primary">&</span> TÀI CHÍNH</h2>
            <p class="text-gray-400 text-sm mt-1">Báo cáo tổng quan nguồn thu hệ thống phòng tập, doanh số bán hàng và hiệu suất tài chính.</p>
        </div>
        <button id="exportExcelBtn" onclick="exportToExcel()" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 text-xs font-bold uppercase px-5 py-2.5 rounded-lg transition-all flex items-center gap-2 shadow-md whitespace-nowrap group">
            <span class="material-symbols-outlined text-base text-green-400 group-hover:scale-110 transition-transform">table_view</span> 
            <span id="exportText">Xuất báo cáo Excel</span>
        </button>
    </header>

    <div id="export-area" class="space-y-6">
    <!-- BANNER MÁY QUÉT CHO ADMIN -->
    <div class="bg-gradient-to-r from-primary/20 via-[#1A1A1A] to-[#131313] border border-primary/30 rounded-xl p-5 md:p-6 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-40 h-40 bg-primary/10 blur-3xl rounded-full pointer-events-none"></div>
        
        <div class="flex flex-col md:flex-row items-center md:items-start gap-6 relative z-10 w-full">
            
            <div class="relative bg-black p-2 rounded-xl w-32 h-32 flex-shrink-0 shadow-lg border border-white/10 flex items-center justify-center overflow-hidden group">
                <div class="absolute top-2 left-2 w-4 h-4 border-t-2 border-l-2 border-primary transition-all group-hover:scale-110"></div>
                <div class="absolute top-2 right-2 w-4 h-4 border-t-2 border-r-2 border-primary transition-all group-hover:scale-110"></div>
                <div class="absolute bottom-2 left-2 w-4 h-4 border-b-2 border-l-2 border-primary transition-all group-hover:scale-110"></div>
                <div class="absolute bottom-2 right-2 w-4 h-4 border-b-2 border-r-2 border-primary transition-all group-hover:scale-110"></div>
                
                <span class="material-symbols-outlined text-4xl text-gray-700">qr_code_scanner</span>
                
                <div class="absolute top-2 left-0 w-full h-0.5 bg-primary shadow-[0_0_8px_#E63946] animate-scan-line"></div>
            </div>
            
            <div class="space-y-2 text-center md:text-left flex-1">
                <div class="flex items-center justify-center md:justify-start gap-2">
                    <span class="material-symbols-outlined text-primary text-2xl">document_scanner</span>
                    <h3 class="font-headline text-xl md:text-2xl text-white uppercase tracking-wider font-bold">Máy Quét Hệ Thống (Admin)</h3>
                </div>
                <p class="text-xs md:text-sm text-gray-300">Kết nối Webcam hoặc súng quét mã vạch để quét QR Code trên thiết bị của hội viên (Dùng cho Check-in, xác thực gói tập).</p>
                <div class="flex flex-wrap justify-center md:justify-start gap-2 pt-2">
                    <span class="bg-gray-800 text-gray-300 border border-gray-700 text-[10px] px-2.5 py-1 rounded font-bold uppercase tracking-wider flex items-center gap-1">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full animate-pulse"></span> Trạng thái: Sẵn sàng nhận mã
                    </span>
                    <span class="bg-black/40 text-gray-400 border border-white/10 text-[10px] px-2.5 py-1 rounded font-mono">Input: Thiết bị ngoại vi</span>
                </div>
            </div>
        </div>

        <button onclick="openScanner()" class="w-full md:w-auto bg-primary hover:bg-red-700 text-white text-xs font-bold uppercase px-6 py-3 rounded-lg transition-all shadow-md shadow-primary/20 whitespace-nowrap flex items-center justify-center gap-2 relative z-10">
            <span class="material-symbols-outlined text-base">center_focus_strong</span> Bật Camera Quét
        </button>
    </div>

    <!-- KHỐI 4 THẺ THỐNG KÊ DOANH THU -->
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-[#1A1A1A] to-[#18251e] border border-green-500/20 rounded-xl p-5 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/10 blur-xl rounded-full"></div>
            <span class="material-symbols-outlined text-green-400 bg-green-500/10 p-2.5 rounded-xl text-xl mb-3 block w-fit">payments</span>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider font-headline">Doanh thu Gói tập</p>
            <p class="text-2xl font-bold text-white font-mono mt-1">{{ number_format($packageRevenue ?? 0) }}đ</p>
            <p class="text-[10px] text-green-400 mt-2 font-mono">● Gói Elite VIP chiếm tỷ trọng cao nhất</p>
        </div>
        
        <div class="bg-gradient-to-br from-[#1A1A1A] to-[#25181a] border border-primary/20 rounded-xl p-5 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-primary/10 blur-xl rounded-full"></div>
            <span class="material-symbols-outlined text-primary bg-primary/10 p-2.5 rounded-xl text-xl mb-3 block w-fit">shopping_bag</span>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider font-headline">Doanh thu Cửa hàng</p>
            <p class="text-2xl font-bold text-white font-mono mt-1">{{ number_format($shopRevenue ?? 0) }}đ</p>
            <p class="text-[10px] text-primary font-mono mt-2">● Thực phẩm bổ sung (Whey/Creatine) bán chạy</p>
        </div>
        
        <div class="bg-gradient-to-br from-[#1A1A1A] to-[#242518] border border-yellow-500/20 rounded-xl p-5 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-yellow-500/10 blur-xl rounded-full"></div>
            <span class="material-symbols-outlined text-yellow-500 bg-yellow-500/10 p-2.5 rounded-xl text-xl mb-3 block w-fit">sports_martial_arts</span>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider font-headline">Doanh thu PT 1-Kèm-1</p>
            <p class="text-2xl font-bold text-white font-mono mt-1">{{ number_format($ptRevenue ?? 0) }}đ</p>
            <p class="text-[10px] text-yellow-500 mt-2">● Tăng trưởng 8.5% so với tháng trước</p>
        </div>

        <div class="bg-gradient-to-br from-[#1A1A1A] to-[#1d1825] border border-purple-500/20 rounded-xl p-5 shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/10 blur-xl rounded-full"></div>
            <span class="material-symbols-outlined text-purple-400 bg-purple-500/10 p-2.5 rounded-xl text-xl mb-3 block w-fit">groups</span>
            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider font-headline">Doanh thu Lớp học</p>
            <p class="text-2xl font-bold text-white font-mono mt-1">{{ number_format($groupRevenue ?? 0) }}đ</p>
            <p class="text-[10px] text-purple-400 mt-2 font-mono">● Lớp Yoga & Pilates hút khách</p>
        </div>
    </section>

    <!-- KHỐI PHÂN TÍCH DOANH SỐ & DANH SÁCH ĐIỂM DANH -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        <section class="col-span-12 lg:col-span-7 space-y-6">
            <div class="bg-[#1A1A1A] border border-white/10 rounded-xl p-6 shadow-md space-y-6">
                <h3 class="font-headline text-base uppercase tracking-wider text-white font-bold border-b border-white/5 pb-3">Phân tích chỉ tiêu doanh số tháng 5</h3>
                
                <div class="space-y-4">
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs text-gray-300">
                            <span class="text-gray-300 font-medium">Mục tiêu gói tập Hội viên đạt mốc</span>
                            <span class="text-white font-bold font-mono">{{ number_format($packageRevenue / 1000000, 1) }}M / 150M ({{ round(($packageRevenue / 150000000) * 100, 1) }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-black/40 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="width: {{ min(($packageRevenue / 150000000) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-xs text-gray-300">
                            <span class="text-gray-300 font-medium">Mục tiêu doanh số Shop thực phẩm phụ kiện</span>
                            <span class="text-white font-bold font-mono">{{ number_format($shopRevenue / 1000000, 1) }}M / 50M ({{ round(($shopRevenue / 50000000) * 100, 1) }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-black/40 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" style="width: {{ min(($shopRevenue / 50000000) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-xs text-gray-300">
                            <span class="text-gray-300 font-medium">Mục tiêu doanh số booking Huấn luyện viên</span>
                            <span class="text-white font-bold font-mono">{{ number_format($ptRevenue / 1000000, 1) }}M / 80M ({{ round(($ptRevenue / 80000000) * 100, 1) }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-black/40 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-500 rounded-full" style="width: {{ min(($ptRevenue / 80000000) * 100, 100) }}%"></div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-xs text-gray-300">
                            <span class="text-gray-300 font-medium">Mục tiêu doanh số Lớp học nhóm</span>
                            <span class="text-white font-bold font-mono">{{ number_format($groupRevenue / 1000000, 1) }}M / 35M ({{ round(($groupRevenue / 35000000) * 100, 1) }}%)</span>
                        </div>
                        <div class="w-full h-2 bg-black/40 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full" style="width: {{ min(($groupRevenue / 35000000) * 100, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHI TIẾT DOANH THU TỪNG LỚP NHÓM -->
            <div class="bg-[#1A1A1A] border border-white/10 rounded-xl p-6 shadow-md space-y-4">
                <div class="flex justify-between items-center border-b border-white/5 pb-3">
                    <h3 class="font-headline text-base uppercase tracking-wider text-white font-bold">Hiệu suất Doanh thu Lớp Nhóm (Group X)</h3>
                    <span class="text-[10px] text-gray-500 font-mono">Tháng 5/2026</span>
                </div>
                
                <div class="space-y-3">
                    @forelse($classPerformances as $cp)
                    @php
                        $icon = 'school'; // Biểu tượng mặc định
                        $iconColor = 'purple-400';
                        $bgColor = 'purple-500/10';
                        if (Str::contains(Str::lower($cp->name), 'yoga')) {
                            $icon = 'self_improvement';
                        } elseif (Str::contains(Str::lower($cp->name), 'spinning') || Str::contains(Str::lower($cp->name), 'đạp xe')) {
                            $icon = 'pedal_bike';
                            $iconColor = 'blue-400';
                            $bgColor = 'blue-500/10';
                        } elseif (Str::contains(Str::lower($cp->name), 'kickboxing') || Str::contains(Str::lower($cp->name), 'combat')) {
                            $icon = 'sports_mma';
                            $iconColor = 'red-400';
                            $bgColor = 'red-500/10';
                        }
                    @endphp
                    <div class="bg-black/20 p-3 rounded-lg border border-white/5 flex justify-between items-center hover:bg-white/5 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-{{ $iconColor }} bg-{{ $bgColor }} p-2.5 rounded-lg text-lg">{{ $icon }}</span>
                            <div>
                                <p class="font-bold text-white text-xs uppercase tracking-wide">{{ $cp->name }}</p>
                                <p class="text-[10px] text-gray-400 font-mono mt-0.5">HLV {{ $cp->pt->user->full_name ?? 'N/A' }} • <span class="text-white font-bold">{{ number_format($cp->price) }}đ</span>/Suất</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-500 font-mono">Đã bán: {{ $cp->total_sold ?? 0 }} suất</p>
                            <p class="text-xs text-purple-400 font-bold font-mono mt-0.5">{{ number_format($cp->total_revenue) }}đ</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 italic py-4">Chưa có dữ liệu lớp học nhóm trong tháng này.</p>
                    @endforelse
                </div>
                <!-- Tổng cộng -->
                <div class="pt-2 flex justify-between items-center border-t border-white/5">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Tổng doanh thu lớp nhóm</span>
                    <span class="text-sm text-primary font-bold font-mono">{{ number_format($groupRevenue ?? 0) }}đ</span>
                </div>
            </div>
        </section>

        <!-- DANH SÁCH ĐIỂM DANH HÔM NAY -->
        <section class="col-span-12 lg:col-span-5">
            <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-5 shadow-md flex flex-col h-full max-h-[350px]">
                <div class="flex justify-between items-center border-b border-white/10 pb-3 mb-4">
                    <h3 class="font-headline text-base uppercase tracking-wider text-white font-bold flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">how_to_reg</span> Điểm danh hôm nay
                    </h3>
                    <span class="bg-green-500/10 text-green-400 border border-green-500/20 text-[10px] px-2 py-0.5 rounded font-bold uppercase tracking-wider">{{ $checkinCount }} Lượt</span>
                </div>
                
                <!-- Danh sách cuộn -->
                <div class="space-y-2.5 overflow-y-auto pr-2 custom-scrollbar flex-1">
                    
                    @forelse($todayCheckins as $ci)
                    <div class="bg-black/30 p-2.5 rounded-lg border border-white/5 flex justify-between items-center transition-colors hover:bg-white/5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/20 border border-primary/30 flex items-center justify-center text-primary font-bold text-xs font-headline">
                                {{ substr($ci->user->full_name, 0, 1) }}
                            </div>
                            <div>
                                <p class="font-bold text-white text-xs tracking-wide">{{ $ci->user->full_name }}</p>
                                <p class="text-[10px] text-primary font-mono mt-0.5">{{ $ci->method }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-gray-400 font-mono">{{ date('H:i A', strtotime($ci->check_in_time)) }}</p>
                            <p class="text-[9px] text-green-500 flex items-center justify-end gap-0.5 mt-0.5 uppercase font-bold tracking-wider"><span class="material-symbols-outlined text-[10px]">check_circle</span> Hợp lệ</p>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-gray-500 italic py-4">Chưa có lượt check-in nào.</p>
                    @endforelse

                </div>
                
                <a href="{{ route('admin.checkin') }}" class="w-full mt-3 bg-white/5 hover:bg-white/10 text-gray-400 border border-white/10 text-[10px] font-bold uppercase py-2 rounded-lg transition-all shadow-sm text-center">
                    Xem toàn bộ lịch sử ({{ $checkinCount }})
                </a>
            </div>
        </section>
    </div>
    </div>

    <!-- MODAL QUÉT MÃ QR (TÍCH HỢP TỪ CHECKIN) -->
    <div id="qrScannerModal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-black/80 backdrop-blur-md">
        <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl w-full max-w-md shadow-2xl overflow-hidden">
            <div class="px-6 py-4 bg-black/40 border-b border-white/10 flex justify-between items-center">
                <h5 class="font-headline text-sm text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary text-base">qr_code_scanner</span> Máy Quét QR Hội Viên
                </h5>
                <button onclick="closeScanner()" class="text-gray-400 hover:text-white transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div id="reader" class="overflow-hidden rounded-xl border border-white/5 bg-black/40"></div>
                <div id="scanResult" class="hidden p-4 rounded-xl text-xs font-bold uppercase tracking-wide text-center"></div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
{{-- Thư viện SheetJS để xuất Excel chuyên nghiệp --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner = null;
    let isProcessing = false;

    function openScanner() {
        const modal = document.getElementById('qrScannerModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    }

    function closeScanner() {
        const modal = document.getElementById('qrScannerModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (html5QrcodeScanner) {
            html5QrcodeScanner.clear().catch(err => console.error(err));
        }
    }

    function onScanSuccess(decodedText) {
        if (isProcessing) return;
        isProcessing = true;

        fetch("{{ route('admin.checkin.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ user_id: decodedText })
        })
        .then(res => res.json())
        .then(data => {
            const resDiv = document.getElementById('scanResult');
            resDiv.classList.remove('hidden', 'bg-green-500/20', 'text-green-400', 'bg-red-500/20', 'text-red-400');
            
            if(data.success) {
                resDiv.classList.add('bg-green-500/20', 'text-green-400');
                resDiv.innerHTML = `✅ ${data.user_name}: Check-in thành công!`;
                setTimeout(() => location.reload(), 2000);
            } else {
                resDiv.classList.add('bg-red-500/20', 'text-red-400');
                resDiv.innerHTML = `❌ ${data.message}`;
                isProcessing = false;
            }
            resDiv.classList.remove('hidden');
        })
        .catch(err => {
            console.error(err);
            isProcessing = false;
            alert("Lỗi kết nối hệ thống. Vui lòng thử lại.");
        });
    }

/**
 * XỬ LÝ XUẤT EXCEL 4 BẢNG DOANH THU CHI TIẾT
 */
function exportToExcel() {
    const btn = document.getElementById('exportExcelBtn');
    const exportText = document.getElementById('exportText');

    if (typeof XLSX === 'undefined') {
        alert("Lỗi: Thư viện XLSX chưa được tải. Vui lòng kiểm tra kết nối mạng.");
        return;
    }

    btn.disabled = true;
    const originalText = exportText.innerText;
    exportText.innerText = "Đang tạo file...";

    try {
        const wb = XLSX.utils.book_new();

        // Bảng 1: Lớp học Nhóm
        const classData = @json($classPerformances);
        const ws_class = XLSX.utils.json_to_sheet(classData.map(i => ({
            "Tên Lớp": i.name, "HLV": i.pt?.user?.full_name || 'N/A', "Giá": i.price, "Số suất bán": i.total_sold, "Tổng Doanh Thu": i.total_revenue
        })));
        XLSX.utils.book_append_sheet(wb, ws_class, "Doanh Thu Lớp Học");

        // Bảng 2: Gói tập Hội viên
        const packageData = @json($packagePerformances);
        const ws_package = XLSX.utils.json_to_sheet(packageData.map(i => ({
            "Tên Gói Tập": i.name, "Số lượt mua": i.total_sold, "Tổng Doanh Thu": i.total_revenue
        })));
        XLSX.utils.book_append_sheet(wb, ws_package, "Doanh Thu Gói Tập");

        // Bảng 3: Sản phẩm Shop
        const productData = @json($productPerformances);
        const ws_product = XLSX.utils.json_to_sheet(productData.map(i => ({
            "Tên Sản Phẩm": i.name, "Số lượng đã bán": i.total_sold, "Tổng Doanh Thu": i.total_revenue
        })));
        XLSX.utils.book_append_sheet(wb, ws_product, "Doanh Thu Sản Phẩm");

        // Bảng 4: PT 1-1 & Hoa hồng Admin (Hệ thống thu 20%)
        const ptData = @json($ptPerformances);
        const ws_pt = XLSX.utils.json_to_sheet(ptData.map(i => ({
            "Tên Huấn Luyện Viên": i.full_name, "Tổng Doanh Thu": i.total_revenue, "Hoa Hồng Admin (20%)": i.admin_commission
        })));
        XLSX.utils.book_append_sheet(wb, ws_pt, "Hoa Hồng PT 1-1");

        // Xuất file
        XLSX.writeFile(wb, `Bao-cao-Doanh-thu-GymPro-${new Date().toISOString().slice(0,10)}.xlsx`);
    } catch (error) {
        console.error("Lỗi Excel:", error);
        alert("Có lỗi xảy ra trong quá trình xuất Excel.");
    } finally {
        btn.disabled = false;
        exportText.innerText = originalText;
    }
}
</script>
@endsection