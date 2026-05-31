@extends('layout.frontend')
@section('content')
<style>
    /* Hỗ trợ hiệu ứng lướt mượt khi tự động nhảy tới ID bài viết */
    html { scroll-behavior: smooth; }

    /* Định dạng nội dung bài viết động để không bị sát nhau và giữ gạch đầu dòng */
    .post-content-render {
        /* Đã xóa white-space: pre-line để tránh lỗi giãn cách khi render HTML từ CKEditor */
    }
    .post-content-render p {
        margin-bottom: 1.25rem; /* Khoảng cách giữa các đoạn văn */
    }
    .post-content-render ul {
        list-style-type: disc !important; /* Hiện dấu gạch đầu dòng */
        padding-left: 1.5rem !important;
        margin-bottom: 1.25rem;
    }
    .post-content-render li {
        margin-bottom: 0.5rem;
    }
</style>

<main class="max-w-4xl mx-auto px-4 md:px-8 py-10">
    
   
    @foreach($posts ?? [] as $post)
    <!-- THANH PHÂN CÁCH TRƯỢT XUỐNG BÀI VIẾT TIẾP THEO -->
    <div class="flex items-center gap-4 my-16 opacity-50">
        <div class="flex-1 h-px bg-white/20"></div>
        <span class="text-gray-400 text-xs font-mono uppercase tracking-widest flex items-center gap-1">
            Bài viết tiếp theo <span class="material-symbols-outlined text-[14px]">arrow_downward</span>
        </span>
        <div class="flex-1 h-px bg-white/20"></div>
    </div>

    <!-- BÀI VIẾT ĐỘNG TỪ CSDL (ID DÙNG ĐỂ TỰ ĐỘNG CUỘN TỪ TRANG CHỦ) -->
    <section id="post-{{ $post->slug }}" class="space-y-8 scroll-mt-24">
        <header class="space-y-4 text-center">
            @php
                $catColor = str_contains(strtolower($post->category), 'sự kiện') ? 'bg-yellow-500' : 'bg-primary';
            @endphp
            <span class="inline-block {{ $catColor }} text-white text-xs font-bold uppercase px-3 py-1 rounded-full shadow-md tracking-wider">
                {{ $post->category }}
            </span>
            <h1 class="font-headline text-3xl md:text-5xl text-white uppercase tracking-tight font-bold leading-tight" style="font-family: 'Oswald', sans-serif;">
                {{ $post->title }}
            </h1>
            <div class="flex justify-center items-center gap-4 text-xs text-gray-400 font-mono pt-2">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">calendar_today</span> {{ $post->created_at->format('d/m/Y') }}</span>
                <span class="flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">person</span> 
                    {{ $post->author->full_name ?? 'Ban quản trị KOR' }}
                </span>
            </div>
        </header>

        <div class="w-full h-[400px] md:h-[500px] rounded-2xl overflow-hidden shadow-2xl">
            <img src="{{ $post->header_image ? asset('images/posts/' . $post->header_image) : 'https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=1200' }}" 
                 alt="{{ $post->title }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
        </div>

        <article class="post-content-render bg-[#1A1A1A] border border-white/10 rounded-2xl p-6 md:p-10 shadow-xl text-gray-300 leading-relaxed text-sm md:text-base">
            {!! $post->content !!}
        </article>
    </section>
    @endforeach

    {{-- BỔ SUNG PHÂN TRANG (Nếu có nhiều hơn 10 bài viết) --}}
    @if(isset($posts) && method_exists($posts, 'links') && $posts->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $posts->links() }}
        </div>
    @endif

    <!-- ĐIỀU HƯỚNG BÀI VIẾT QUAY LẠI TRANG CHỦ -->
    <div class="text-center pt-16 mt-8 border-t border-white/10">
        <a href="{{ route('trang_chu') }}" class="inline-flex items-center gap-2 bg-white/5 hover:bg-white/10 border border-white/10 text-white font-headline text-sm uppercase px-6 py-3 rounded-xl transition-all" style="font-family: 'Oswald', sans-serif;">
            <span class="material-symbols-outlined text-sm">arrow_back</span> Quay lại Cổng Hội Viên
        </a>
    </div>

</main>
@endsection
