<!DOCTYPE html>
<html class="dark" lang="vi">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>@yield('title', 'KOR GYM')</title>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background text-on-background min-h-screen antialiased selection:bg-primary-container selection:text-white">

<nav class="sticky top-0 z-50 bg-[#131313]/90 backdrop-blur-xl border-b border-white/10 shadow-lg">
    <div class="max-w-7xl mx-auto px-4 md:px-8 h-16 flex items-center justify-between gap-4">
        <a href="/" class="font-headline text-2xl font-bold text-primary tracking-tighter uppercase italic">KOR GYM</a>
        
        <div class="flex-1"></div>

        <div class="flex items-center gap-5">
            <a href="/shop" class="text-primary border-b-2 border-primary pb-1 font-headline text-lg uppercase tracking-tight hidden md:block">Cửa hàng</a>
            <a href="/booking-pt" class="text-gray-400 hover:text-primary transition-colors font-headline text-lg uppercase tracking-tight hidden md:block">Đặt Lịch PT</a>
            <a href="/notifications" class="text-primary hover:bg-white/5 p-2 rounded-full transition-colors flex items-center justify-center relative ml-2">
                <span class="material-symbols-outlined">notifications</span>
            </a>
            <a href="/profile" class="w-8 h-8 rounded-full border border-white/20 overflow-hidden block hover:border-primary transition-colors">
                <img alt="Avatar" class="w-full h-full object-cover" src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?q=80&w=150&auto=format&fit=crop"/>
            </a>
        </div>
    </div>
</nav>

<main class="max-w-7xl mx-auto px-4 md:px-8 py-8">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>