@extends('layout.frontend') 
@section('content')
<style>
    /* Tùy chỉnh thanh cuộn ngang cho mượt mà */
    .custom-scrollbar::-webkit-scrollbar { height: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(255, 255, 255, 0.05); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(227, 24, 55, 0.3); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(227, 24, 55, 0.6); }
</style>

<main class="max-w-7xl mx-auto px-4 md:px-8 pt-8 space-y-8">

    {{-- Hiển thị thông báo đăng ký thành công --}}
    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm font-bold flex items-center gap-2 animate-fade-in">
            <span class="material-symbols-outlined">check_circle</span> {{ session('success') }}
        </div>
    @endif

    {{-- Hiển thị các lỗi validation (ví dụ: mật khẩu dưới 8 ký tự) --}}
    @if($errors->any())
        <div class="bg-primary/10 border border-primary/20 text-primary p-4 rounded-xl text-sm font-bold">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="flex justify-between items-end">
        <div>
            <h1 class="font-headline text-3xl md:text-4xl text-white uppercase tracking-tight">Cổng Hội Viên</h1>
            <p class="text-sm text-gray-400 mt-1">Chào {{ Auth::check() ? Auth::user()->full_name : 'Hội viên' }}. Bạn đã sẵn sàng bứt phá giới hạn chưa?</p>
        </div>
        <div class="hidden md:flex items-center gap-2 text-gray-400">
            <span class="material-symbols-outlined text-lg">calendar_today</span>
            <span class="text-xs font-bold uppercase" id="current-date">Hôm nay</span>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <div class="{{ Auth::check() ? 'md:col-span-8' : 'md:col-span-12' }} bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 flex flex-col justify-between shadow-xl">
            <div>
                <h2 class="font-headline text-xl uppercase text-white border-b border-white/10 pb-4 mb-4">Hồ sơ sức khỏe & Đề xuất toàn diện</h2>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-surface-variant/30 p-3 rounded-xl border border-white/5">
                        <label for="height-input" class="block text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Chiều cao (cm)</label>
                        <input type="number" id="height-input" value="{{ $latestMetric->height ?? '' }}" class="w-full bg-transparent border-none p-0 text-xl font-bold text-white focus:ring-0">
                    </div>
                    <div class="bg-surface-variant/30 p-3 rounded-xl border border-white/5">
                        <label for="weight-input" class="block text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Cân nặng (kg)</label>
                        <input type="number" id="weight-input" value="{{ $latestMetric->weight ?? '' }}" class="w-full bg-transparent border-none p-0 text-xl font-bold text-white focus:ring-0">
                    </div>
                    <div class="bg-surface-variant/30 p-3 rounded-xl border border-white/5">
                        <label for="fat-input" class="block text-[10px] uppercase tracking-wider text-gray-400 font-bold mb-1">Tỷ lệ mỡ (Body Fat %)</label>
                        <input type="number" id="fat-input" value="{{ $latestMetric->body_fat_percentage ?? '' }}" class="w-full bg-transparent border-none p-0 text-xl font-bold text-white focus:ring-0">
                    </div>
                </div>

                <div id="bmi-panel" class="p-4 rounded-xl border transition-all duration-300 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="text-sm font-bold text-white">Chỉ số BMI của bạn: <span id="bmi-score" class="text-xl font-headline ml-1">--</span></div>
                        <p id="bmi-status" class="text-xs text-gray-400 mt-1">Đang phân tích dữ liệu thể trạng...</p>
                    </div>
                    <div class="bg-black/40 px-4 py-3 rounded-lg border border-white/5 max-w-md">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-primary block mb-1">Hệ thống đề xuất KOR:</span>
                        <p id="ai-suggestion" class="text-xs text-gray-300 leading-relaxed">Đang xử lý đề xuất giáo án tối ưu...</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-white/10 flex justify-between items-center">
                <span class="text-xs text-gray-500">Dữ liệu kết nối tự động từ máy đo InBody KOR. Cập nhật lần cuối: <span id="last-updated-date">{{ isset($latestMetric->measured_at) ? \Carbon\Carbon::parse($latestMetric->measured_at)->format('d/m/Y H:i') : 'Chưa có' }}</span></span>
                <button id="update-metrics-btn" class="bg-primary text-white text-xs font-bold uppercase py-2 px-4 rounded hover:bg-red-700 transition-colors">Cập nhật chỉ số</button>
            </div>
        </div>

        @auth
        {{-- Mã QR của khách hàng --}}
        {{-- Chỉ hiển thị nếu người dùng đã đăng nhập --}}
        <div class="md:col-span-4 bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 flex flex-col items-center justify-center text-center shadow-xl">
            <h2 class="font-headline text-xl uppercase text-white border-b border-white/10 pb-4 mb-5 w-full tracking-wide">Mã Check-in Thẻ</h2>
            <div class="bg-white p-4 rounded-xl inline-block shadow-lg">
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ Auth::id() }}" alt="QR Code" class="w-36 h-36">
            </div>
            <p class="text-xs text-gray-400 mt-5 leading-relaxed">Xuất trình mã này cho bộ phận lễ tân khi đến trung tâm để thực hiện check-in vào phòng tập.</p>
        </div>
        @endauth
    </div>

    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 shadow-xl">
        <h2 class="font-headline text-xl uppercase text-white border-b border-white/10 pb-4 mb-6 tracking-wide flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">newspaper</span> Chuyên mục kiến thức & Sự kiện phòng tập
        </h2>
        <div class="flex overflow-x-auto gap-6 pb-6 snap-x snap-mandatory custom-scrollbar">
            @forelse($posts ?? [] as $post)
            {{-- Link tới trang posts kèm anchor #post-slug để tự động cuộn xuống --}}
            <a href="{{ route('posts.index') }}#post-{{ $post->slug }}" class="min-w-[280px] md:min-w-[calc(33.333%-1.25rem)] snap-start bg-black/20 rounded-xl overflow-hidden border border-white/5 group hover:border-primary/50 transition-colors block">
                <div class="h-40 overflow-hidden">
                    <img src="{{ $post->header_image ? asset('images/posts/' . $post->header_image) : 'https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=400' }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $post->title }}">
                </div>
                <div class="p-4 space-y-2">
                    @php
                        $categoryColor = str_contains(strtolower($post->category), 'sự kiện') ? 'text-yellow-500' : 'text-primary';
                    @endphp
                    <span class="text-[10px] font-bold {{ $categoryColor }} uppercase tracking-widest">{{ $post->category }}</span>
                    <h3 class="text-sm font-bold text-white line-clamp-1">{{ $post->title }}</h3>
                    <p class="text-xs text-gray-400 line-clamp-2 whitespace-pre-line">{{ \Illuminate\Support\Str::limit(strip_tags($post->content), 150) }}</p>
                </div>
            </a>
            @empty
            <div class="w-full text-center py-12 bg-black/20 rounded-xl border border-white/5">
                <p class="text-gray-500 italic text-sm">Chưa có bài viết hoặc sự kiện nào được đăng tải.</p>
            </div>
            @endforelse
        </div>
    </div>

    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 shadow-xl">
        <h2 class="font-headline text-xl uppercase text-white border-b border-white/10 pb-4 mb-6 tracking-wide flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">rate_review</span> Hệ thống phản hồi & Chấm điểm dịch vụ KOR
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            <div class="md:col-span-5 bg-black/20 border border-white/5 p-4 rounded-xl space-y-4">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Để lại đánh giá buổi tập vừa qua</h3>
                
                <div>
                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Loại đánh giá</label>
                    <select id="feedback-type" onchange="switchReviewTarget()" class="w-full bg-surface-variant/40 border border-white/10 rounded-lg text-xs text-white p-2.5 outline-none focus:border-primary mb-3">
                        <option value="pt">Huấn luyện viên (PT)</option>
                        <option value="product">Sản phẩm đã mua</option>
                    </select>

                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Chọn Huấn luyện viên / Sản phẩm</label>
                    <select id="feedback-target" class="w-full bg-surface-variant/40 border border-white/10 rounded-lg text-xs text-white p-2.5 outline-none focus:border-primary">
                        <option value="">Đang tải dữ liệu...</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Chấm điểm chất lượng (Rating)</label>
                    <div class="flex gap-1 text-yellow-500" id="star-rating-container">
                        <span onclick="setStarRating(1)" class="star-node cursor-pointer material-symbols-outlined text-xl">star</span>
                        <span onclick="setStarRating(2)" class="star-node cursor-pointer material-symbols-outlined text-xl">star</span>
                        <span onclick="setStarRating(3)" class="star-node cursor-pointer material-symbols-outlined text-xl">star</span>
                        <span onclick="setStarRating(4)" class="star-node cursor-pointer material-symbols-outlined text-xl">star</span>
                        <span onclick="setStarRating(5)" class="star-node cursor-pointer material-symbols-outlined text-xl">star</span>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] text-gray-400 font-bold uppercase mb-1">Ý kiến bình luận nhận xét</label>
                    <textarea id="feedback-comment" rows="2" class="w-full bg-surface-variant/40 border border-white/10 rounded-lg text-xs text-white p-2.5 outline-none focus:border-primary placeholder:text-gray-600" placeholder="PT hướng dẫn nhiệt tình, cơ sở sạch sẽ..."></textarea>
                </div>

                <button onclick="submitFeedbackForm()" class="w-full py-2.5 bg-primary hover:bg-red-700 text-white font-headline text-sm uppercase rounded-lg shadow-md transition-all">Gửi phản hồi hệ thống</button>
            </div>

            <div class="md:col-span-7 space-y-3 max-h-[340px] overflow-y-auto pr-1" id="comments-display-list">
                {{-- Dữ liệu phản hồi sẽ được load qua JavaScript fetchAllReviews() --}}
            </div>
        </div>
    </div>

    <!-- KHỐI GÓI HỘI VIÊN DỮ LIỆU ĐỘNG (SAO CHÉP TỪ INDEX.HTML) -->
    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 md:p-10 shadow-xl">
        <h2 class="font-headline text-xl uppercase text-white border-b border-white/10 pb-4 mb-6 tracking-wide flex items-center gap-2" style="font-family: 'Oswald', sans-serif;">
            <span class="material-symbols-outlined text-primary">workspace_premium</span> Gói hội viên & Đăng ký dịch vụ
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-12 mt-10">
            @if(isset($goiTaps) && count($goiTaps) > 0)
            @foreach($goiTaps as $package)
            @php
                // Xác định gói nổi bật
                $isPopular = str_contains(strtolower($package->package_name), 'khỏe mạnh') || str_contains(strtolower($package->package_name), 'công sở');
            @endphp
            <div class="{{ $isPopular ? 'bg-primary/10 border-2 border-primary relative' : 'bg-black/20 border border-white/10' }} rounded-2xl p-6 hover:shadow-2xl hover:scale-[1.02] transition-all duration-300 flex flex-col group">
                @if($isPopular)
                <span class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-[10px] font-black px-4 py-1.5 rounded-full text-white shadow-lg uppercase tracking-widest z-10 border border-white/10">BÁN CHẠY NHẤT</span>
                @endif
                
                <div class="flex-1">
                    <h3 class="text-xl font-headline font-bold text-white mb-4 uppercase group-hover:text-primary transition-colors">{{ $package->package_name }}</h3>
                    
                    {{-- Danh sách quyền lợi --}}
                    <div class="text-xs text-gray-200 font-bold leading-relaxed mb-6 space-y-3 whitespace-pre-line">
                        @php
                            // Thay thế ký tự bullet bằng icon Material Symbols
                            $desc = str_replace('•', '<span class="material-symbols-outlined text-[14px] text-primary align-middle mr-1.5" style="font-variation-settings: \'FILL\' 1;">check_circle</span>', $package->description);
                        @endphp
                        {!! $desc !!}
                    </div>
                </div>

                <div class="border-t border-white/5 pt-4">
                    <div class="text-2xl font-black text-primary mb-4 font-mono">{{ number_format($package->price, 0, ',', '.') }}đ <span class="text-[10px] text-gray-500 font-normal lowercase">/ {{ $package->duration_days }} ngày</span></div>
                    <button data-id="{{ $package->id }}" class="btn-register w-full py-3 {{ $isPopular ? 'bg-primary hover:bg-red-700' : 'bg-white/10 hover:bg-primary' }} text-white text-xs font-bold uppercase rounded-xl transition-all shadow-md">Kích hoạt thẻ ngay</button>
                </div>
            </div>
            @endforeach
            @else
                <div class="col-span-3 text-center py-10 bg-black/20 rounded-xl border border-white/5 text-gray-500 italic text-sm">Hiện chưa có gói hội viên nào được cập nhật trên hệ thống.</div>
            @endif
        </div>

</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Hàm tính toán và hiển thị BMI, gợi ý AI
    function updateHealthMetricsDisplay(height, weight, bodyFat) {
        const h = parseFloat(height) / 100;
        const w = parseFloat(weight);
        const bf = parseFloat(bodyFat);

        let bmi = '--';
        if (h && w) {
            bmi = (w / (h * h)).toFixed(1);
        }
        document.getElementById('bmi-score').innerText = bmi;

        const panel = document.getElementById('bmi-panel');
        const statusText = document.getElementById('bmi-status');
        const aiText = document.getElementById('ai-suggestion');

        // Reset classes
        panel.className = "p-4 rounded-xl border transition-all duration-300 flex flex-col md:flex-row justify-between items-start md:items-center gap-4";

        if (bmi === '--' || isNaN(bmi)) {
            statusText.innerText = "Đang phân tích dữ liệu thể trạng...";
            aiText.innerText = "Đang xử lý đề xuất giáo án tối ưu...";
            panel.classList.add('border-white/10');
        } else if (bmi >= 25) {
            statusText.innerText = "Trạng thái: Thừa cân - Lượng mỡ tích tụ cần giải phóng.";
            aiText.innerText = "Khuyên dùng: Giáo án Cắt nét & Thâm hụt calo. Nên bổ sung Whey Isolate tinh khiết tại Cửa hàng để tối ưu khối cơ nách bắp.";
            panel.classList.add('border-red-500/30', 'bg-red-500/5');
        } else if (bmi < 18.5) {
            statusText.innerText = "Trạng thái: Thiếu cân - Cần tích lũy dinh dưỡng và tập nặng.";
            aiText.innerText = "Khuyên dùng: Giáo án Tăng cơ xả cơ cường độ cao. Nên ưu tiên chọn thực phẩm Mass Gainer năng lượng cao tại Store.";
            panel.classList.add('border-yellow-500/30', 'bg-yellow-500/5');
        } else {
            statusText.innerText = "Trạng thái: Thể trạng cân đối lý tưởng - Phân bố cơ mỡ ổn định.";
            aiText.innerText = "Khuyên dùng: Lộ trình Điêu khắc và giữ nét cơ. Duy trì cường độ tập, kết hợp dùng thêm Creatine để bứt phá sức mạnh nổ.";
            panel.classList.add('border-green-500/30', 'bg-green-500/5');
        }
    }

    // Hàm gửi dữ liệu lên server và cập nhật UI
    document.getElementById('update-metrics-btn').addEventListener('click', async function() {
        const height = document.getElementById('height-input').value;
        const weight = document.getElementById('weight-input').value;
        const bodyFat = document.getElementById('fat-input').value;
        const btn = this;
        const originalText = btn.innerHTML;
        if (!height || !weight) {
            showToastNotification("Vui lòng nhập đủ Chiều cao và Cân nặng.");
            return;
        }
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Đang cập nhật...';
        try {
            const response = await fetch("{{ route('metric.update') }}", {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json', // Quan trọng: Yêu cầu server trả về JSON
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    height: height,
                    weight: weight,
                    body_fat_percentage: bodyFat || null
                })
            });
            if (response.ok) {
                const data = await response.json();
                updateHealthMetricsDisplay(height, weight, bodyFat); // Cập nhật hiển thị ngay lập tức
                
                // Chỉ cập nhật dòng "lần cuối" nếu dữ liệu thực sự được lưu (không phải guest)
                if (!data.is_guest) {
                    document.getElementById('last-updated-date').innerText = new Date().toLocaleString('vi-VN');
                }
                showToastNotification(data.success ? data.message : "Cập nhật thành công!");
            } else {
                showToastNotification(data.message || "Có lỗi xảy ra khi cập nhật.");
            }
        } catch (error) {
            console.error('Lỗi:', error);
            showToastNotification("Lỗi kết nối server.");
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    let currentRatingScore = 5;
    function setStarRating(score) {
        currentRatingScore = score;
        const stars = document.querySelectorAll('#star-rating-container .star-node');
        stars.forEach((star, index) => {
            if(index < score) {
                star.style.fontVariationSettings = "'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24";
            } else {
                star.style.fontVariationSettings = "'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24";
            }
        });
    }

    async function fetchAllReviews() {
        try {
            const response = await fetch("{{ route('reviews.all') }}");
            const data = await response.json();
            if(data.success) {
                const list = document.getElementById('comments-display-list');
                list.innerHTML = '';
                data.reviews.forEach(rev => {
                    const stars = '★'.repeat(rev.rating) + '☆'.repeat(5 - rev.rating);
                    const name = rev.user ? rev.user.full_name : 'Hội viên';
                    const target = rev.reviewable ? (rev.reviewable.name || rev.reviewable.full_name) : 'Dịch vụ';
                    const initials = name.split(' ').map(n => n[0]).join('').substring(0,2).toUpperCase();
                    
                    const div = document.createElement('div');
                    div.className = "bg-white/5 border border-white/5 p-3 rounded-xl flex gap-3 items-start text-xs mb-3 animate-fade-in";
                    div.innerHTML = `
                        <div class="w-8 h-8 rounded-full bg-primary/20 text-primary font-bold font-headline flex items-center justify-center flex-shrink-0">${initials}</div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2"><span class="font-bold text-white">${name}</span><span class="text-yellow-500">${stars}</span></div>
                            <p class="text-gray-300"><strong class="text-gray-500 text-[10px] uppercase">[Đã đánh giá cho ${target}]:</strong><br>${rev.comment}</p>
                        </div>
                    `;
                    list.appendChild(div);
                });
            }
        } catch (e) { console.error("Lỗi tải phản hồi:", e); }
    }

    let reviewDataCache = { products: [], pts: [] };

    async function fetchReviewableTargets() {
        try {
            const response = await fetch("{{ route('reviews.targets') }}");
            const data = await response.json();
            if(data.success) {
                reviewDataCache = data;
                switchReviewTarget(); // Render lần đầu cho PT
            }
        } catch (error) {
            console.error("Lỗi tải danh sách đánh giá:", error);
        }
    }

    function switchReviewTarget() {
        const type = document.getElementById('feedback-type').value;
        const targetSelect = document.getElementById('feedback-target');
        targetSelect.innerHTML = '';

        const list = type === 'pt' ? reviewDataCache.pts : reviewDataCache.products;
        
        if(list.length === 0) {
            const opt = document.createElement('option');
            opt.value = "";
            opt.innerText = type === 'pt' ? "-- Chưa có HLV đã tập --" : "-- Chưa có sản phẩm đã mua --";
            targetSelect.appendChild(opt);
            return;
        }

        list.forEach(item => {
            const opt = document.createElement('option');
            // Value format: type_id để đồng bộ với logic ReviewController@store của bạn
            opt.value = `${type}_${item.id}`; 
            opt.innerText = item.full_name || item.name;
            targetSelect.appendChild(opt);
        });
    }

    async function submitFeedbackForm() {
        const targetRaw = document.getElementById('feedback-target').value;
        if(!targetRaw) {
            showToastNotification("Vui lòng chọn đối tượng cần đánh giá!");
            return;
        }
        const [type, id] = targetRaw.split('_');
        const comment = document.getElementById('feedback-comment').value.trim();
        const targetName = document.getElementById('feedback-target').options[document.getElementById('feedback-target').selectedIndex].text;
        
        if(!comment) {
            showToastNotification("Vui lòng viết vài dòng ý kiến nhận xét trước khi gửi!");
            return;
        }

        try {
            const response = await fetch("{{ route('reviews.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    rating: currentRatingScore,
                    comment: comment,
                    reviewable_type: type,
                    reviewable_id: id
                })
            });

            const data = await response.json();

            if (data.success) {
                fetchAllReviews(); // Tải lại danh sách sau khi gửi thành công
                document.getElementById('feedback-comment').value = '';
                showToastNotification(data.message);
            } else {
                showToastNotification(data.message || "Không thể gửi đánh giá.");
            }
        } catch (error) {
            showToastNotification("Lỗi kết nối máy chủ.");
        }
    }

    function showToastNotification(msg) {
        const t = document.createElement('div');
        t.style.cssText = "position: fixed; bottom: 30px; left: 30px; background: #222; color: #fff; padding: 12px 24px; border-radius: 8px; font-size: 13px; font-weight: bold; box-shadow: 0 10px 25px rgba(0,0,0,0.3); z-index:9999; border: 1px solid rgba(255,255,255,0.1);";
        t.innerText = msg;
        document.body.appendChild(t);
        setTimeout(() => t.remove(), 2500);
    }

    function updateCurrentDateString() {
        const today = new Date();
        const days = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
        const dayName = days[today.getDay()];
        const dateStr = `${dayName}, ${today.getDate()}/${today.getMonth() + 1}/${today.getFullYear()}`;
        const dateTarget = document.getElementById('current-date');
        if(dateTarget) {
            dateTarget.innerText = dateStr;
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        // Khi trang tải xong, cập nhật hiển thị với dữ liệu ban đầu
        updateHealthMetricsDisplay(document.getElementById('height-input').value, document.getElementById('weight-input').value, document.getElementById('fat-input').value);
        setStarRating(5); 
        updateCurrentDateString();
        fetchAllReviews(); // Tải tất cả phản hồi ngay khi mở trang
        @auth
            fetchReviewableTargets(); // Chỉ gọi API này khi người dùng đã đăng nhập thành công
        @endauth
    });

    // SCRIPT ĐĂNG KÝ HỘI VIÊN (TẬN DỤNG TỪ YÊU CẦU CỦA BẠN)
    $(document).ready(function() {
        $('.btn-register').on('click', function() {
            let packageId = $(this).data('id');
            $.ajax({
                url: "{{ route('membership.register') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    package_id: packageId
                },
                success: function(res) {
                    if(res.success && res.redirect_url) {
                        window.location.href = res.redirect_url;
                    }
                },
                error: function(xhr) {
                    let errorMessage = "Đã có lỗi xảy ra, vui lòng thử lại.";
                    
                    // Lấy câu thông báo lỗi từ Controller (json error)
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }

                    alert(errorMessage);

                    // Chỉ chuyển hướng nếu thực sự chưa đăng nhập (mã 401)
                    if (xhr.status === 401) {
                        window.location.href = "{{ route('login') }}";
                    }
                }
            });
        });
    });
</script>
@endsection