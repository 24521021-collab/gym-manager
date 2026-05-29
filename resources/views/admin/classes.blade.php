@extends('layout.admin_layout')
@section('content')

<div class="container-fluid py-6 px-4">
    <header>
        <h2 class="font-headline text-3xl md:text-4xl text-white uppercase tracking-tight">ĐIỀU PHỐI <span class="text-primary">&</span> LỚP HỌC</h2>
        <p class="text-gray-400 text-sm mt-1">Thiết lập danh sách các lớp học Gym nhóm, huấn luyện viên và sức chứa phòng tập.</p>
    </header>

    <!-- Form Cấu hình Lớp học (Inline - Giống Admin Products) -->
    <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6 shadow-md mt-6">
        <h3 id="form-title" class="font-headline text-lg text-white uppercase mb-4 border-b border-white/10 pb-3 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">edit_note</span> Cấu hình lớp học
        </h3>
        
        <form id="classForm" onsubmit="saveClass(event)" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="class_id" name="id">
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-xs">
                <div class="md:col-span-2">
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Tên lớp học</label>
                    <input type="text" name="name" id="name" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" required>
                    <span class="text-danger error-text name_error"></span>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Huấn luyện viên (PT)</label>
                    <select name="pt_id" id="pt_id" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary [color-scheme:dark]" required>
                        <option value="">-- Chọn PT --</option>
                        @foreach($pts as $pt)
                            <option value="{{ $pt->id }}">{{ $pt->user->full_name }}</option>
                        @endforeach
                    </select>
                    <span class="text-danger error-text pt_id_error"></span>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Sức chứa tối đa</label>
                    <input type="number" name="max_capacity" id="max_capacity" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" required>
                    <span class="text-danger error-text max_capacity_error"></span>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Tổng số buổi</label>
                    <input type="number" name="total_sessions" id="total_sessions" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" value="1" required>
                    <span class="text-danger error-text total_sessions_error"></span>
                </div>
                <div>
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Giá trọn gói (VNĐ)</label>
                    <input type="number" name="price" id="price" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" value="0" required>
                    <span class="text-danger error-text price_error"></span>
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Hình ảnh lớp</label>
                    <input type="file" name="image" id="image" class="w-full bg-black/40 border border-white/10 rounded-lg text-gray-400 px-3 py-2 focus:ring-1 focus:ring-primary focus:border-primary">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[10px] uppercase font-bold text-gray-400 mb-1">Mô tả ngắn</label>
                    <textarea name="description" id="description" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" rows="1"></textarea>
                    <span class="text-danger error-text description_error"></span>
                </div>
            </div>

            <div id="imagePreviewContainer" class="mt-3 flex items-center gap-3" style="display:none;">
                <img id="imagePreview" src="" width="80" class="rounded border border-white/10 bg-black/20 p-0.5">
            </div>

            <div class="flex gap-2 justify-end mt-4 text-xs">
                <button type="submit" class="bg-primary text-white font-bold uppercase px-6 py-2.5 rounded-lg hover:bg-red-700 transition-colors">Lưu lớp học</button>
                <button type="button" onclick="prepareAdd()" class="bg-white/10 text-gray-400 font-bold uppercase p-2.5 rounded-lg hover:bg-white/20 hover:text-white transition-colors"><span class="material-symbols-outlined text-sm block">refresh</span></button>
            </div>
        </form>
    </div>

    <!-- Form Tìm kiếm & Danh sách -->
    <div class="mt-8 space-y-4">
        <div class="max-w-md">
            <form id="searchForm" onsubmit="event.preventDefault(); loadClasses();" class="flex gap-2 text-xs">
                <input type="text" id="searchInput" class="w-full bg-black/40 border border-white/10 rounded-lg text-white px-3 py-2.5 focus:ring-1 focus:ring-primary focus:border-primary" placeholder="Tìm tên lớp hoặc PT..." value="{{ request('search') }}">
                <button type="submit" class="bg-white/10 text-white font-bold uppercase px-4 py-2.5 rounded-lg hover:bg-white/20 transition-colors whitespace-nowrap">Tìm kiếm</button>
            </form>
        </div>

        <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-6 shadow-md">
            <div class="flex justify-between items-center mb-4 border-b border-white/10 pb-3">
                <h3 class="font-headline text-lg text-white uppercase">Danh sách lớp học hiện tại</h3>
                <span class="material-symbols-outlined text-primary">groups</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left whitespace-nowrap text-sm">
                    <thead>
                        <tr class="text-gray-500 font-bold uppercase text-[10px] tracking-wider border-b border-white/10">
                            <th class="py-3 px-3">Ảnh</th>
                            <th class="py-3 px-3">Tên lớp</th>
                            <th class="py-3 px-3">PT phụ trách</th>
                            <th class="py-3 px-3">Sức chứa</th>
                            <th class="py-3 px-3">Số buổi</th>
                            <th class="py-3 px-3 text-right">Giá tiền</th>
                            <th class="py-3 px-3 text-right pr-4">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5" id="classTableBody">
                        <tr><td colspan="7" class="text-center py-4 text-gray-500 italic">Đang tải dữ liệu...</td></tr>
                    </tbody>
                </table>
            </div>
            <div id="classPagination" class="flex justify-center mt-4"></div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Biến toàn cục lưu trữ dữ liệu cache
    window.cachedClasses = [];

    function escapeHtml(text) {
        if (!text) return '';
        return text.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function clearErrors() {
        document.querySelectorAll('.error-text').forEach(span => span.textContent = '');
    }

    // 1. READ: Tải danh sách lớp học
    function loadClasses(page = 1) {
        const search = document.getElementById('searchInput').value;
        let url = "{{ route('admin.gym-classes.index') }}?page=" + page;
        if (search) url += "&search=" + encodeURIComponent(search);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                window.cachedClasses = data.data;
                const tbody = document.getElementById('classTableBody');
                if (window.cachedClasses.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="9" class="text-center py-4">Không tìm thấy lớp học nào.</td></tr>';
                    return;
                }

                let html = '';
                window.cachedClasses.forEach(c => {
                    const imgPath = c.image ? `/images/products/${c.image}` : '/images/products/default-class.jpg';
                    const ptName = c.pt && c.pt.user ? c.pt.user.full_name : 'N/A';
                    
                    html += `
                        <tr id="class-row-${c.id}" class="hover:bg-white/5 transition-colors">
                            <td class="py-3 px-3"><img src="${imgPath}" width="45" height="45" class="rounded border border-white/5 object-fit-cover"></td>
                            <td class="py-4 px-3 text-white font-bold">${escapeHtml(c.name)}</td>
                            <td class="py-4 px-3 text-gray-400">${escapeHtml(ptName)}</td>
                            <td class="py-4 px-3 text-gray-400">${c.max_capacity} người</td>
                            <td class="py-4 px-3 text-gray-400">${c.total_sessions} buổi</td>
                            <td class="py-4 px-3 text-right font-bold text-gray-300">${Number(c.price).toLocaleString('vi-VN')}đ</td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-dark" onclick="prepareEdit(${c.id})">Sửa<i class="fas fa-edit"></i></button>
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteClass(${c.id})">Xóa<i class="fas fa-trash"></i></button>
                            </td>
                        </tr>`;
                });
                tbody.innerHTML = html;
                renderPagination(data.links);
            });
    }

    // 1.5 Render Pagination
    function renderPagination(links) {
        const container = document.getElementById('classPagination');
        if (!links || links.length <= 3) { container.innerHTML = ''; return; }

        let html = '<nav class="flex gap-1">';
        links.forEach(link => {
            let pageNum = link.url ? new URL(link.url).searchParams.get('page') : 1;
            html += `
            <a href="#" onclick="event.preventDefault(); ${link.url && !link.active ? `loadClasses(${pageNum})` : ''}" 
                   class="px-3 py-1.5 rounded-lg border text-xs font-bold uppercase transition-all ${link.active ? 'bg-primary text-white border-primary' : 'bg-black/40 text-gray-400 border-white/10 hover:bg-white/5'} ${!link.url ? 'opacity-40 pointer-events-none' : ''}">
                ${link.label.replace('&laquo; Previous', 'Trước').replace('Next &raquo;', 'Sau')}
            </li>`;
        });
        container.innerHTML = html + '</ul></nav>';
    }

    // 2. PREPARE ADD
    function prepareAdd() {
        document.getElementById('form-title').innerHTML = '<span class="material-symbols-outlined text-primary">edit_note</span> Cấu hình lớp học';
        document.getElementById('classForm').reset();
        document.getElementById('class_id').value = '';
        document.getElementById('imagePreviewContainer').style.display = 'none';
        clearErrors();
    }

    // 2.5 PREPARE EDIT (Cache-based)
    function prepareEdit(id) {
        const c = window.cachedClasses.find(item => item.id == id);
        if (!c) return;

        document.getElementById('form-title').innerHTML = '<span class="material-symbols-outlined text-blue-400">edit_calendar</span> Cập nhật lớp học #' + c.id;
        document.getElementById('class_id').value = c.id;
        document.getElementById('name').value = c.name;
        document.getElementById('pt_id').value = c.pt_id;
        document.getElementById('max_capacity').value = c.max_capacity;
        document.getElementById('total_sessions').value = c.total_sessions;
        document.getElementById('price').value = c.price;
        document.getElementById('description').value = c.description || '';

        if (c.image) {
            document.getElementById('imagePreview').src = `/images/products/${c.image}`;
            document.getElementById('imagePreviewContainer').style.display = 'block';
        } else {
            document.getElementById('imagePreviewContainer').style.display = 'none';
        }

        clearErrors();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // 3. SAVE (Create & Update)
    function saveClass(event) {
        event.preventDefault();
        const id = document.getElementById('class_id').value;
        const formData = new FormData(document.getElementById('classForm'));
        
        let url = "{{ route('admin.gym-classes.store') }}";
        if (id) {
            url = "{{ route('admin.gym-classes.update', ':id') }}".replace(':id', id);
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => {
            if (!res.ok) return res.json().then(err => { throw err; });
            return res.json();
        })
        .then(data => {
            loadClasses();
            alert(data.message);
            prepareAdd();
        })
        .catch(err => {
            if (err.errors) {
                for (const key in err.errors) {
                    const errSpan = document.querySelector(`.${key}_error`);
                    if (errSpan) errSpan.textContent = err.errors[key][0];
                }
            } else {
                alert(err.message || "Đã xảy ra lỗi.");
            }
        });
    }

    // 4. DELETE
    function deleteClass(id) {
        if (!confirm("Xóa lớp học này?")) return;
        
        fetch("{{ route('admin.gym-classes.destroy', ':id') }}".replace(':id', id), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = document.getElementById(`class-row-${id}`);
                if (row) row.remove();
                alert(data.message);
            }
        });
    }

    // Preview ảnh khi chọn file
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('imagePreview').src = event.target.result;
                document.getElementById('imagePreviewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
document.addEventListener('DOMContentLoaded', () => loadClasses());
</script>
@endsection