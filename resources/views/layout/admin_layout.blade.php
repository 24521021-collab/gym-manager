
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
        <h1 class="font-headline text-2xl text-primary font-black uppercase mb-1 tracking-wider" ><a href="/">KOR GYM ADMIN</a></h1>
        <p class="text-gray-400 text-xs uppercase font-bold tracking-widest">Hệ thống quản trị</p>
    </div>
    <div class="flex-1 flex flex-col gap-1 py-4">
        <a class="text-gray-400 hover:bg-white/5 hover:text-white px-6 py-3.5 flex items-center gap-3 transition-all" href="admin.html">
            <span class="material-symbols-outlined">analytics</span><span class="text-sm font-bold">Tổng quan & Phân tích</span>
        </a>
        <a class="text-gray-400 hover:bg-white/5 hover:text-white px-6 py-3.5 flex items-center gap-3 transition-all" href="members.html">
            <span class="material-symbols-outlined">group</span><span class="text-sm font-bold">Quản lý Hội viên</span>
        </a>
        <a class="text-gray-400 hover:bg-white/5 hover:text-white px-6 py-3.5 flex items-center gap-3 transition-all" href="inventory.html">
            <span class="material-symbols-outlined">inventory_2</span><span class="text-sm font-bold">Kho Hàng & Sản phẩm</span>
        </a>
        <a class="bg-primary/20 text-primary border-l-4 border-primary px-6 py-3.5 flex items-center gap-3 transition-all" href="/admin/transaction">
            <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">receipt_long</span><span class="text-sm font-bold">Lịch sử Giao dịch</span>
        </a>
        <a class="text-gray-400 hover:bg-white/5 hover:text-white px-6 py-3.5 flex items-center gap-3 transition-all" href="schedule.html">
            <span class="material-symbols-outlined">calendar_month</span><span class="text-sm font-bold">Lịch HLV (Booking)</span>
        </a>
    </div>
    <div class="p-6 border-t border-white/10 mt-auto flex flex-col gap-3">
        <a href="/" class="w-full bg-white/10 text-white text-xs font-bold uppercase py-3 px-4 rounded hover:bg-white/20 transition-all text-center flex items-center justify-center gap-2">
            <span class="material-symbols-outlined text-sm">open_in_new</span> VỀ APP HỘI VIÊN
        </a>
    </div>
</nav>

<main main class="flex-1 md:ml-64 p-6 md:p-10 w-full space-y-6">
    @yield('content')
</main>
</div>

@stack('scripts')
@yield('scripts')
</body>
</html>
