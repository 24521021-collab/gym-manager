@extends('layout.admin_layout')

@section('title', 'Quản lý Bài viết - KOR GYM')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-2xl font-bold uppercase tracking-wider flex items-center gap-2 text-white">
        <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">article</span> 
        DANH SÁCH BÀI VIẾT
    </h2>
    <a href="{{ route('admin.posts.create') }}" class="bg-primary hover:bg-red-700 text-white font-bold py-2 px-5 rounded-md transition-colors flex items-center gap-2">
        <span class="material-symbols-outlined text-sm">add</span> THÊM BÀI VIẾT
    </a>
</div>

@if(session('success'))
    <div class="bg-green-600/20 border border-green-500 text-green-500 p-4 rounded-md mb-6 flex items-center gap-2">
        <span class="material-symbols-outlined">check_circle</span> {{ session('success') }}
    </div>
@endif

<div class="bg-[#1a1a1a] rounded-lg border border-white/10 p-6 shadow-xl">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b border-white/10 text-gray-400 text-xs uppercase tracking-wider">
                <th class="pb-4 font-bold w-24">Ảnh</th>
                <th class="pb-4 font-bold">Tiêu đề</th>
                <th class="pb-4 font-bold">Danh mục</th>
                <th class="pb-4 font-bold text-center w-32">Trạng thái</th>
                <th class="pb-4 font-bold text-right w-32">Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($posts as $post)
            <tr class="border-b border-white/5 hover:bg-white/5 transition-colors">
                <td class="py-4">
                    @if($post->header_image)
                        <img src="{{ asset('storage/' . $post->header_image) }}" alt="Ảnh bìa" class="w-16 h-16 object-cover rounded-md bg-black">
                    @else
                        <div class="w-16 h-16 bg-[#222] border border-white/10 flex items-center justify-center rounded-md text-gray-500">
                            <span class="material-symbols-outlined text-2xl">image</span>
                        </div>
                    @endif
                </td>
                <td class="py-4 font-bold text-gray-100">{{ $post->title }}</td>
                <td class="py-4 text-gray-400">{{ $post->category }}</td>
                <td class="py-4 text-center">
                    @if($post->status == 'Published' || $post->status == 'Sẵn sàng')
                        <span class="text-green-500 border border-green-500/50 px-3 py-1 rounded text-xs uppercase font-bold tracking-wider">SẴN SÀNG</span>
                    @else
                        <span class="text-yellow-500 border border-yellow-500/50 px-3 py-1 rounded text-xs uppercase font-bold tracking-wider">BẢN NHÁP</span>
                    @endif
                </td>
                <td class="py-4 text-right">
                    <a href="{{ route('admin.posts.edit', $post->id) }}" class="text-blue-500 hover:text-blue-400 transition-colors inline-block mr-3" title="Chỉnh sửa">
                        <span class="material-symbols-outlined">edit</span>
                    </a>
                    
                    <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài viết này?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-400 transition-colors" title="Xóa">
                            <span class="material-symbols-outlined">delete</span>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="py-12 text-center text-gray-500">
                    <span class="material-symbols-outlined text-4xl mb-2 block opacity-50">post_add</span>
                    <p>Chưa có bài viết nào. Hãy bấm "Thêm bài viết" để bắt đầu!</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if($posts->hasPages())
        <div class="mt-6 border-t border-white/10 pt-4">
            {{ $posts->links() }}
        </div>
    @endif
</div>
@endsection