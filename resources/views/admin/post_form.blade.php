@extends('layout.admin_layout')

@php
    // Kiểm tra xem đây là chế độ Edit hay Create
    $isEdit = isset($post) && $post->id;
    $title = $isEdit ? 'CHỈNH SỬA BÀI VIẾT' : 'THÊM BÀI VIẾT MỚI';
    $icon = $isEdit ? 'edit' : 'add_circle';
    $route = $isEdit ? route('admin.posts.update', $post->id) : route('admin.posts.store');
@endphp

@section('title', ($isEdit ? 'Chỉnh sửa' : 'Thêm') . ' Bài viết - KOR GYM')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold uppercase text-white flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">{{ $icon }}</span> {{ $title }}
        </h2>
        <a href="{{ route('admin.posts.index') }}" class="text-gray-400 hover:text-white transition-colors flex items-center gap-1 text-sm">
            <span class="material-symbols-outlined text-sm">arrow_back</span> QUAY LẠI
        </a>
    </div>

    <div class="bg-[#1a1a1a] rounded-lg border border-white/10 p-8 shadow-xl">
        <form action="{{ $route }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            
            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Tiêu đề bài viết</label>
                <input type="text" name="title" value="{{ old('title', $post->title ?? '') }}" required class="w-full bg-black border border-white/10 rounded-md px-4 py-3 text-white focus:border-primary focus:outline-none" placeholder="Nhập tiêu đề bài viết...">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Danh mục</label>
                    <select name="category" required class="w-full bg-black border border-white/10 rounded-md px-4 py-3 text-white focus:border-primary focus:outline-none [color-scheme:dark]">
                        @php $currentCat = old('category', $post->category ?? 'Dinh dưỡng'); @endphp
                        <option value="Dinh dưỡng" {{ $currentCat == 'Dinh dưỡng' ? 'selected' : '' }}>Dinh dưỡng</option>
                        <option value="Kỹ thuật tập luyện" {{ $currentCat == 'Kỹ thuật tập luyện' ? 'selected' : '' }}>Kỹ thuật tập luyện</option>
                        <option value="Sự kiện" {{ $currentCat == 'Sự kiện' ? 'selected' : '' }}>Sự kiện</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Tác giả (PT)</label>
                    <select name="author_id" required class="w-full bg-black border border-white/10 rounded-md px-4 py-3 text-white focus:border-primary focus:outline-none [color-scheme:dark]">
                        <option value="">-- Chọn tác giả --</option>
                        @foreach($authors as $pt)
                            @php 
                                $selected = (old('author_id', $post->author_id ?? '') == $pt->user->id) ? 'selected' : '';
                            @endphp
                            <option value="{{ $pt->user->id }}" {{ $selected }}>{{ $pt->user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Trạng thái</label>
                    <select name="status" required class="w-full bg-black border border-white/10 rounded-md px-4 py-3 text-white focus:border-primary focus:outline-none [color-scheme:dark]">
                        @php $currentStatus = old('status', $post->status ?? 'Draft'); @endphp
                        <option value="Draft" {{ $currentStatus == 'Draft' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="Sẵn sàng" {{ $currentStatus == 'Sẵn sàng' ? 'selected' : '' }}>Hoàn thiện (Đăng trang chủ)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-400 mb-2">
                        Ảnh bìa (Header Image) {{ $isEdit ? '(Để trống nếu không đổi)' : '' }}
                    </label>
                    <input type="file" name="header_image" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-primary file:text-white hover:file:bg-red-700">
                    
                    @if($isEdit && $post->header_image)
                        <div class="mt-4">
                            <p class="text-[10px] text-gray-500 uppercase mb-2">Ảnh hiện tại:</p>
                            <img src="{{ asset('images/posts/' . $post->header_image) }}" class="w-32 h-20 object-cover rounded-md border border-white/10 bg-black">
                        </div>
                    @endif
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-400 mb-2">Nội dung bài viết</label>
                <textarea name="content" id="editor" rows="10" required class="w-full bg-black border border-white/10 rounded-md px-4 py-3 text-white focus:border-primary focus:outline-none" placeholder="Viết nội dung bài viết tại đây...">{{ old('content', $post->content ?? '') }}</textarea>
            </div>

            <div class="pt-4 flex gap-3">
                <button type="submit" class="bg-primary hover:bg-red-700 text-white font-bold py-3 px-8 rounded-md transition-all shadow-lg shadow-primary/20 uppercase tracking-widest text-sm flex-1">
                    {{ $isEdit ? 'Lưu thay đổi' : 'Đăng bài viết ngay' }}
                </button>
                
                @if(!$isEdit)
                    <button type="reset" class="bg-white/5 hover:bg-white/10 text-gray-400 font-bold py-3 px-6 rounded-md transition-all border border-white/10 uppercase text-xs">
                        Làm trống form
                    </button>
                @else
                    <a href="{{ route('admin.posts.index') }}" class="bg-white/5 hover:bg-white/10 text-gray-400 font-bold py-3 px-6 rounded-md transition-all border border-white/10 uppercase text-xs flex items-center justify-center text-center">
                        Hủy bỏ
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
<script>
    //tích hợp CKEDITOR giúp lưu lại các kiểu chữ, phông chữ khác nhau trên các bài viết , trong phần nội dung bài viết
    // Kích hoạt CKEditor cho textarea có id là "editor"
    CKEDITOR.replace('editor', {
        height: 400,
        removeButtons: 'PasteFromWord', // CKEditor tự động xử lý tốt việc dán từ Word
    });
</script>
@endsection
