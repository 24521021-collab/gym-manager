@extends('layout.admin_layout') {{-- Sử dụng layout chuẩn của dự án --}}

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 font-headline text-white uppercase tracking-tight">VẬN HÀNH <span class="text-primary">&</span> TIN TỨC</h1>
        <br><br>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary bg-primary border-none hover:bg-red-700 font-bold uppercase text-xs px-6 py-2.5 rounded-lg transition-all shadow-md shadow-primary/20">
            <i class="fas fa-plus me-2"></i> Viết bài mới
        </a>
    </div>

    <div class="bg-[#1A1A1A] border border-white/10 rounded-xl p-4 mb-6 shadow-md">
        <div class="card-header bg-transparent p-0 border-0">
            <div class="row align-items-center">
                <div class="col">
                    <div class="relative">
                        <input type="text" id="searchInput" class="w-full bg-black border border-white/10 text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:border-primary transition-colors" placeholder="Tìm tên bài viết hoặc danh mục..." onkeyup="loadData(1)">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[#1A1A1A] rounded-xl border border-white/10 p-5 shadow-md">
        <div class="flex justify-between items-center mb-4 border-b border-white/10 pb-3">
            <h3 class="font-headline text-lg text-white uppercase">Danh sách bài viết</h3>
            <span class="material-symbols-outlined text-primary">article</span>
        </div>
        <div class="table-responsive">
            <table class="w-full text-left whitespace-nowrap text-sm align-middle mb-0">
                <thead>
                    <tr class="text-gray-500 font-bold uppercase text-[10px] tracking-wider border-b border-white/10">
                        <th class="py-3 px-3">ID</th>
                        <th class="py-3 px-3">Ảnh bìa</th>
                        <th class="py-3 px-3">Tiêu đề</th>
                        <th class="py-3 px-3">Danh mục</th>
                        <th class="py-3 px-3">Tác giả</th>
                        <th class="py-3 px-3 text-center">Trạng thái</th>
                        <th class="py-3 px-3 text-right pr-4">Hành động</th>
                    </tr>
                </thead>
                <tbody id="postTableBody" class="divide-y divide-white/5">
                    {{-- Dữ liệu sẽ được render từ JS --}}
                    <tr><td colspan="7" class="text-center py-4 text-gray-500">Đang tải dữ liệu bài viết...</td></tr>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-transparent border-t border-white/5 mt-4 pt-4 d-flex justify-content-between align-items-center">
            <div id="paginationInfo" class="text-gray-500 text-xs font-mono uppercase"></div>
            <nav id="paginationLinks"></nav>
        </div>
    </div>
</div>

<script>
window.cachedData = []; // Lưu trữ dữ liệu toàn cục để Edit nhanh

document.addEventListener('DOMContentLoaded', () => {
    loadData();
});

function escapeHtml(text) {
    if (!text) return '';
    return text.toString().replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
}

async function loadData(page = 1) {
    const search = document.getElementById('searchInput').value;
    const url = `{{ route('admin.posts.index') }}?page=${page}&search=${encodeURIComponent(search)}`;

    try {
        const response = await fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();

        window.cachedData = data.data; // Cập nhật cache
        renderTable(data.data);
        renderPagination(data.links);
        document.getElementById('paginationInfo').innerText = `HIỂN THỊ ${data.from || 0}-${data.to || 0} TRÊN TỔNG SỐ ${data.total} BÀI VIẾT`;
    } catch (error) {
        console.error('Lỗi khi tải dữ liệu:', error);
    }
}

function renderTable(posts) {
    const tbody = document.getElementById('postTableBody');
    tbody.innerHTML = posts.map(post => `
        <tr id="row_${post.id}" class="hover:bg-white/5 transition-colors">
            <td class="py-3 px-3 font-mono text-gray-500 text-xs">#${post.id}</td>
            <td class="py-3 px-3">
                <img src="/images/posts/${post.header_image || 'default.jpg'}" class="rounded border border-white/5" width="60" height="40" style="object-fit: cover;">
            </td>
            <td class="py-4 px-3 text-white font-bold ">${escapeHtml(post.title)}</td>
            <td class="py-4 px-3 text-gray-400 text-middle"><span class="px-2 py-0.5 bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 font-bold text-[10px] uppercase rounded">${post.category}</span></td>
            <td class="py-4 px-3 text-gray-400">${post.author ? post.author.full_name : 'N/A'}</td>
            <td class="py-4 px-3 text-center">
                <span class="inline-block px-2 py-0.5 border font-bold text-[10px] uppercase rounded ${post.status === 'Sẵn sàng' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20'}">
                    ${post.status === 'Sẵn sàng' ? 'HOÀN THIỆN' : 'BẢN NHÁP'}
                </span>
            </td>
            <td class="py-4 px-3 text-right pr-4 space-x-1 whitespace-nowrap">
                <a href="/admin/posts/${post.id}/edit" class="text-blue-400 p-1 hover:bg-blue-500/10 rounded transition-colors inline-block" title="Chỉnh sửa">
                    <span class="material-symbols-outlined text-sm block">edit</span>
                </a>
                <button class="text-primary p-1 hover:bg-primary/10 rounded transition-colors" onclick="deletePost(${post.id})" title="Xóa">
                    <span class="material-symbols-outlined text-sm block">delete</span>
                </button>
            </td>
        </tr>
    `).join('');
}

function renderPagination(links) {
    const nav = document.getElementById('paginationLinks');
    nav.innerHTML = `<div class="flex items-center gap-1">
        ${links.map(link => `
            <a href="#" onclick="event.preventDefault(); ${link.url && !link.active ? `loadData(${new URL(link.url).searchParams.get('page')})` : ''}" 
               class="px-3 py-1.5 rounded-lg border font-bold uppercase transition-all 
               ${link.active ? 'bg-primary text-white border-primary' : 'bg-black/40 text-gray-400 border-white/10 hover:bg-white/5'} 
               ${link.url === null ? 'opacity-40 pointer-events-none' : ''} text-[10px]">
               ${link.label.replace('&laquo; Previous', 'Trước').replace('Next &raquo;', 'Sau')}
            </a>
        `).join('')}
    </div>`;
}

async function deletePost(id) {
    if (!confirm('Xác nhận: Bạn có chắc chắn muốn xóa bài viết này khỏi hệ thống?')) return;

    try {
        const response = await fetch(`/admin/posts/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const result = await response.json();
        if (result.success) {
            document.getElementById(`row_${id}`).remove();
            alert(result.message);
            loadData();
        }
    } catch (error) {
        alert('Lỗi khi xóa bài viết!');
    }
}
</script>
@endsection