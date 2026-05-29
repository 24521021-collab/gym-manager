@extends('layout.admin_layout')
@section('content')
    <!-- HEADER -->
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
        <div>
            <h2 class="font-headline text-3xl md:text-4xl text-white uppercase tracking-tight border-l-4 border-primary pl-4">QUẢN LÝ GÓI TẬP <span class="text-primary">GYM</span></h2>
            <p class="text-gray-400 text-sm mt-1">Thiết lập danh sách các gói tập, thời hạn và giá tiền.</p>
        </div>
    </header>

     <!-- SEARCH & FILTER -->
    <div class="bg-[#1A1A1A] border border-white/10 rounded-xl p-4 mb-6 shadow-md">
        <form id="searchForm" onsubmit="event.preventDefault(); loadPackages();" class="flex flex-col md:flex-row gap-3">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-xl">search</span>
                <input type="text" id="searchInput" 
                       class="w-full bg-black border border-white/10 text-white rounded-lg pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:border-primary transition-colors" 
                       placeholder="Tìm tên gói tập..." value="{{ request('search') }}">
            </div>
            <button type="submit" class="bg-white/10 hover:bg-white/20 text-white text-xs font-bold uppercase px-8 py-2.5 rounded-lg transition-all border border-white/10">
                Tìm kiếm
            </button>
        </form>
    </div>


    <!-- Form Cấu hình Gói tập (Inline - Giống Admin Products/Classes) -->
    <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6 shadow-md mt-6">
        <h3 id="form-title" class="font-headline text-lg text-white uppercase mb-4 border-b border-white/10 pb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">edit_note</span> Cấu hình gói tập
        </h3>
        
        <form id="packageForm" onsubmit="savePackage(event)">
            @csrf
            <input type="hidden" id="package_id" name="id">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                <div class="md:col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Tên gói tập</label>
                    <input type="text" name="package_name" id="package_name" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" required>
                    <span class="text-primary text-[10px] error-text package_name_error"></span>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Thời hạn (ngày)</label>
                    <input type="number" name="duration_days" id="duration_days" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" required min="1">
                    <span class="text-primary text-[10px] error-text duration_days_error"></span>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Giá tiền (VNĐ)</label>
                    <input type="number" name="price" id="price" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" required min="0">
                    <span class="text-primary text-[10px] error-text price_error"></span>
                </div>
                <div class="md:col-span-4">
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Mô tả</label>
                    <textarea name="description" id="description" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" rows="2"></textarea>
                </div>
            </div>
            <div class="flex gap-2 justify-end mt-4 text-xs">
                <button type="submit" class="bg-primary text-white font-bold uppercase px-6 py-2.5 rounded-lg hover:bg-red-700 transition-colors">Lưu gói tập</button>
                <button type="button" onclick="prepareAdd()" class="bg-white/10 text-gray-400 font-bold uppercase p-2.5 rounded-lg hover:bg-white/20 hover:text-white transition-colors"><span class="material-symbols-outlined text-sm block">refresh</span></button>
            </div>
        </form>
    </div>

    <!-- DATA TABLE -->
    <div class="bg-[#1A1A1A] border border-white/10 rounded-xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-black/40 border-b border-white/10">
                    <tr class="text-gray-400 font-headline uppercase text-[11px] tracking-widest">
                        <th class="p-4 font-bold">ID</th>
                        <th class="p-4 font-bold">Tên gói tập</th>
                        <th class="p-4 font-bold">Thời hạn</th>
                        <th class="p-4 font-bold text-right">Giá tiền</th>
                        <th class="p-4 font-bold text-center">Hành động</th>
                    </tr>
                </thead>
                <tbody id="package-table-body" class="divide-y divide-white/5">
                    <tr><td colspan="5" class="text-center py-12 text-gray-500 italic">Đang tải dữ liệu gói tập...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="bg-black/20 p-4 border-t border-white/5 flex justify-center">
            <div id="packagePagination">
                <!-- Nút phân trang sẽ được render bởi JavaScript -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Hàm bảo mật chống tấn công XSS
    function escapeHtml(text) {
        if (!text) return '';
        return text.toString()
            .replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    // Hàm để xóa tất cả các thông báo lỗi
    function clearErrors() {
        document.querySelectorAll('.error-text').forEach(span => span.textContent = '');
    }

    // 1. READ & SEARCH: Lấy dữ liệu gói tập
    function loadPackages(page = 1) {
        const searchKeyword = document.getElementById('searchInput').value;
        let url = "{{ route('packages.index') }}";
        const params = new URLSearchParams();
        if (searchKeyword) {
            params.append('search', searchKeyword);
        }
        if (page > 1) {
            params.append('page', page);
        }
        if (params.toString()) {
            url += `?${params.toString()}`;
        }

        fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Không thể tải danh sách gói tập.');
                return res.json();
            })
            .then(data => {
                const packages = data.data; // Dữ liệu gói tập nằm trong thuộc tính 'data' của đối tượng phân trang
                const tbody = document.getElementById('package-table-body');
                if (!packages || packages.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">Không tìm thấy gói tập nào.</td></tr>`;
                    document.getElementById('packagePagination').innerHTML = ''; // Ensure pagination is cleared
                    return;
                }
                let html = '';
                packages.forEach(p => {
                    html += `
                        <tr id="package-row-${p.id}" class="hover:bg-white/5 transition-colors">
                            <td class="py-4 px-3 text-gray-500 font-mono text-xs">GT${String(p.id).padStart(2, '0')}</td>
                            <td class="py-4 px-3 text-white font-bold">${escapeHtml(p.package_name)}</td> 
                            <td class="py-4 px-3 text-gray-400">${p.duration_days} ngày</td>
                            <td class="py-4 px-3 text-right font-headline text-lg text-white">${Number(p.price).toLocaleString('vi-VN')}đ</td>
                            <td class="py-4 px-3 text-center pr-4 space-x-1 whitespace-nowrap">
                                <button class="text-blue-400 p-1 hover:bg-blue-500/10 rounded transition-colors" onclick="prepareEdit(${p.id})">
                                    <span class="material-symbols-outlined text-sm block">edit</span>
                                </button>
                                <button class="text-primary p-1 hover:bg-primary/10 rounded transition-colors" onclick="deletePackage(${p.id})">
                                    <span class="material-symbols-outlined text-sm block">delete</span>
                                </button>
                            </td>
                        </tr>`;
                });
                tbody.innerHTML = html;
                window.cachedPackages = packages; // Lưu bộ nhớ cache mảng gói tập

                renderPagination(data.links); // Vẽ các nút chuyển trang
            })
            .catch(err => {
                document.getElementById('package-table-body').innerHTML = `<tr><td colspan="5" class="text-center text-danger py-4">Lỗi: ${err.message}</td></tr>`;
                document.getElementById('packagePagination').innerHTML = '';
            });
    }

    /**
     * 1.5 RENDER PAGINATION: Tạo các nút phân trang từ dữ liệu Laravel trả về
     */
    function renderPagination(links) {
        const container = document.getElementById('packagePagination');
        if (!links || links.length <= 3) { // Chỉ có 1 trang (Prev, 1, Next) thì không cần hiện
            container.innerHTML = '';
            return;
        }
        let html = '<nav class="flex gap-1">';
        links.forEach(link => {
            const activeClass = link.active ? 'active' : '';
            const disabledClass = link.url === null ? 'disabled' : '';
            // Trích xuất số trang từ URL link.url (ví dụ: ?page=2)
            let pageNum = 1;
            if (link.url) {
                const urlObj = new URL(link.url);
                pageNum = urlObj.searchParams.get('page') || 1;
            }
            html += `
                <a href="#" onclick="event.preventDefault(); ${link.url && !link.active ? `loadPackages(${pageNum})` : ''}" 
                   class="px-3 py-1.5 rounded-lg border font-bold uppercase transition-all 
                   ${link.active ? 'bg-primary text-white border-primary' : 'bg-black/40 text-gray-400 border-white/10 hover:bg-white/5'} 
                   ${link.url === null ? 'opacity-40 pointer-events-none' : ''}">
                   ${link.label.replace('&laquo; Previous', 'Trước').replace('Next &raquo;', 'Sau')}
                </a>`;
        });
        html += '</nav>';
        container.innerHTML = html;
    }

    // Reset Form về chế độ Thêm mới
    function prepareAdd() {
        document.getElementById('form-title').innerHTML = '<span class="material-symbols-outlined text-primary">edit_note</span> Cấu hình gói tập';
        document.getElementById('packageForm').reset();
        document.getElementById('package_id').value = '';
        clearErrors();
    }

    // 2. PREPARE EDIT: Đổ dữ liệu cũ lên Form
    function prepareEdit(id) {
        const p = window.cachedPackages.find(item => item.id == id);
        if (!p) return;

        document.getElementById('form-title').innerHTML = '<span class="material-symbols-outlined text-blue-400">edit_calendar</span> Cập nhật gói tập #' + p.id;
        document.getElementById('package_id').value = p.id;
        document.getElementById('package_name').value = p.package_name;
        document.getElementById('duration_days').value = p.duration_days;
        document.getElementById('price').value = p.price;
        document.getElementById('description').value = p.description || '';
        clearErrors();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // 3. CREATE & UPDATE: Lưu thông tin
    function savePackage(event) {
        event.preventDefault();
        const id = document.getElementById('package_id').value;
        const formElement = document.getElementById('packageForm');
        const formData = new FormData(formElement);
        clearErrors();

        let url = "{{ route('packages.store') }}";
        let method = 'POST';

        if (id) {
            url = "{{ route('packages.update', ':id') }}".replace(':id', id);
            formData.append('_method', 'PUT'); // Giả lập phương thức PUT của Laravel
        } 

        fetch(url, {
                method: 'POST', // Fetch API sẽ luôn dùng POST khi có FormData, Laravel sẽ đọc _method
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => {
                        throw err;
                    });
                }
                return res.json();
            })
            .then(data => {
                loadPackages();
                alert(data.message);
                prepareAdd();
            })
            .catch(err => {
                if (err.errors) { // Validation errors from Laravel
                    for (const key in err.errors) {
                        document.querySelector(`span.${key}_error`).textContent = err.errors[key][0];
                    }
                } else {
                    alert(err.message || 'Có lỗi xảy ra, vui lòng thử lại.'); // Generic error message
                }
            });
    }

    // 4. DELETE: Xóa dữ liệu
    function deletePackage(id) {
        if (!confirm("Bạn có chắc chắn muốn xóa gói tập này không?")) return;
        const url = "{{ route('packages.destroy', ':id') }}".replace(':id', id);

        fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) {
                    return res.json().then(err => {
                        throw err;
                    });
                }
                return res.json();
            })
            .then(data => {
                const row = document.getElementById(`package-row-${id}`);
                if (row) row.remove();
                alert(data.message); // Show success message
                loadPackages(); // Tải lại để cập nhật phân trang nếu cần
            })
            .catch(err => {
                alert(err.message || 'Không thể xóa gói tập.');
            });
    }

    document.addEventListener('DOMContentLoaded', loadPackages);
</script>
@endsection