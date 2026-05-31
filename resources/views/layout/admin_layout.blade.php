
<html class="dark" lang="vi">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'KOR GYM')</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background min-h-screen flex text-sm">

<nav class="bg-[#1a1a1a] text-white hidden md:flex flex-col fixed left-0 top-0 h-screen w-64 z-50 border-r border-white/10 shadow-xl">
    <div class="px-6 py-8 border-b border-white/10">
        <h1 class="font-headline text-2xl text-primary font-black uppercase mb-1 tracking-wider"><a href="/">KOR GYM ADMIN</a></h1>
        <p class="text-gray-400 text-xs uppercase font-bold tracking-widest">Hệ thống quản trị</p>
    </div>
    
    <div class="flex-1 flex flex-col gap-1 py-4">
        {{-- 1. Tổng quan & Phân tích --}}
        <a class="{{ request()->is('admin/dashboard*') ? 'bg-primary/20 text-primary border-l-4 border-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} px-6 py-3.5 flex items-center gap-3 transition-all" href="/admin/dashboard">
            <span class="material-symbols-outlined" style=" request()->is('admin/dashboard*') ? "font-variation-settings: 'FILL' 1;" : "" }}">analytics</span>
            <span class="text-sm font-bold">Tổng quan & Phân tích</span>
        </a>

        {{-- 2. Quản lý Hội viên --}}
        <a class="{{ request()->is('admin/memberships*') ? 'bg-primary/20 text-primary border-l-4 border-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} px-6 py-3.5 flex items-center gap-3 transition-all" href="/admin/memberships">
            <span class="material-symbols-outlined" style="{{ request()->is('admin/memberships*') ? "font-variation-settings: 'FILL' 1;" : "" }}">group</span>
            <span class="text-sm font-bold">Quản lý Hội viên</span>
        </a>

        {{-- 3. Kho Hàng & Sản phẩm --}}
        <a class="{{ request()->is('admin/products*') ? 'bg-primary/20 text-primary border-l-4 border-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} px-6 py-3.5 flex items-center gap-3 transition-all" href="/admin/products">
            <span class="material-symbols-outlined" style="{{ request()->is('admin/products*') ? "font-variation-settings: 'FILL' 1;" : "" }}">inventory_2</span>
            <span class="text-sm font-bold">Kho Hàng & Sản phẩm</span>
        </a>

        {{-- 4. Lịch sử Giao dịch --}}
        <a class="{{ request()->is('admin/transaction*') ? 'bg-primary/20 text-primary border-l-4 border-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} px-6 py-3.5 flex items-center gap-3 transition-all" href="/admin/transaction">
            <span class="material-symbols-outlined" style="{{ request()->is('admin/transaction*') ? "font-variation-settings: 'FILL' 1;" : "" }}">receipt_long</span>
            <span class="text-sm font-bold">Lịch sử Giao dịch</span>
        </a>

        {{-- 5. Quản lý Lớp học --}}
        <a class="{{ request()->routeIs('admin.gym-classes.*') ? 'bg-primary/20 text-primary border-l-4 border-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} px-6 py-3.5 flex items-center gap-3 transition-all" href="{{ route('admin.gym-classes.index') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('admin.gym-classes.*') ? "font-variation-settings: 'FILL' 1;" : "" }}">groups</span>
            <span class="text-sm font-bold">Quản lý Lớp học</span>
        </a>

        {{-- 6. Quản lý Gói tập --}}
        <a class="{{ request()->routeIs('packages.*') ? 'bg-primary/20 text-primary border-l-4 border-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} px-6 py-3.5 flex items-center gap-3 transition-all" href="{{ route('packages.index') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('packages.*') ? "font-variation-settings: 'FILL' 1;" : "" }}">card_membership</span>
            <span class="text-sm font-bold">Quản lý Gói tập</span>
        </a>

        {{-- 7. Lịch HLV (Booking) --}}
        <a class="{{ request()->is('schedule.html') || request()->is('admin/pt-bookings*') ? 'bg-primary/20 text-primary border-l-4 border-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} px-6 py-3.5 flex items-center gap-3 transition-all" href="/admin/pt-bookings">
            <span class="material-symbols-outlined" style="{{ request()->is('schedule.html') || request()->is('admin/schedule*') ? "font-variation-settings: 'FILL' 1;" : "" }}">calendar_month</span>
            <span class="text-sm font-bold">Lịch HLV (Booking)</span>
        </a>

        {{-- 8. Quản lý Bài đăng --}}
        <a class="{{ request()->routeIs('admin.posts.*') ? 'bg-primary/20 text-primary border-l-4 border-primary' : 'text-gray-400 hover:bg-white/5 hover:text-white' }} px-6 py-3.5 flex items-center gap-3 transition-all" href="{{ route('admin.posts.index') }}">
            <span class="material-symbols-outlined" style="{{ request()->routeIs('admin.posts.*') ? "font-variation-settings: 'FILL' 1;" : "" }}">newspaper</span>
            <span class="text-sm font-bold">Quản lý Bài đăng</span>
        </a>
    </div>
    
    <div class="p-6 border-t border-white/10 mt-auto flex flex-col gap-3">
        <a href="/" class="w-full bg-white/10 text-white text-xs font-bold uppercase py-3 px-4 rounded hover:bg-white/20 transition-all text-center flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">open_in_new</span> VỀ APP HỘI VIÊN
        </a>
    </div>
</nav>

<main class="flex-1 md:ml-64 p-6 md:p-10 w-full space-y-6">
    @yield('content')
</main>

@stack('scripts')
@yield('scripts')
</body>
</html>