@extends('layout.frontend')

@section('title', 'Chi tiet don hang')

@section('content')
<section class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <a href="{{ route('orders.index') }}" class="text-xs text-gray-400 hover:text-white">&larr; Quay lai danh sach</a>
            <h1 class="font-headline text-3xl md:text-4xl text-white uppercase tracking-tight mt-2">Don #ORD-{{ $order->id }}</h1>
            <p class="text-sm text-gray-400 mt-1">Dat luc {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</p>
        </div>
        <div class="flex gap-2">
            @if($order->payment_status === 'Pending')
                <form method="POST" action="{{ route('orders.cancel', $order->id) }}" onsubmit="return confirm('Ban chac chan muon huy don hang nay?')">
                    @csrf
                    @method('PATCH')
                    <button class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold uppercase px-4 py-2.5 rounded-xl">Huy don</button>
                </form>
            @endif
            <a href="{{ route('cart.index') }}" class="bg-white/10 hover:bg-white/20 text-white text-xs font-bold uppercase px-4 py-2.5 rounded-xl">Gio hang</a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm font-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl text-sm font-bold">{{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-[#1A1A1A] border border-white/10 rounded-2xl overflow-hidden">
            <div class="px-5 py-4 border-b border-white/10">
                <h2 class="font-headline text-xl uppercase text-white">Mat hang trong don</h2>
            </div>
            <div class="divide-y divide-white/5">
                @foreach($order->items as $item)
                    <div class="p-5 grid grid-cols-12 gap-4 items-center">
                        <div class="col-span-12 md:col-span-6">
                            <p class="text-white font-bold">{{ $item->name }}</p>
                            <p class="text-xs text-gray-500 uppercase mt-1">{{ $item->item_type }}</p>
                        </div>
                        <div class="col-span-4 md:col-span-2 text-gray-400 text-sm">x{{ $item->quantity }}</div>
                        <div class="col-span-4 md:col-span-2 text-gray-400 text-sm font-mono">{{ number_format($item->price, 0, ',', '.') }}đ</div>
                        <div class="col-span-4 md:col-span-2 text-right text-primary font-bold font-mono">{{ number_format($item->subtotal, 0, ',', '.') }}đ</div>
                    </div>
                @endforeach
            </div>
        </div>

        <aside class="bg-[#1A1A1A] border border-white/10 rounded-2xl p-5 h-fit space-y-4">
            <h2 class="font-headline text-xl uppercase text-white">Tong ket</h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between gap-4">
                    <span class="text-gray-400">Trang thai</span>
                    <span class="text-white font-bold">{{ $order->payment_status }}</span>
                </div>
                <div class="flex justify-between gap-4">
                    <span class="text-gray-400">Phuong thuc</span>
                    <span class="text-white font-bold">{{ $order->payment_method }}</span>
                </div>
                <div class="flex justify-between gap-4 border-t border-white/10 pt-4">
                    <span class="text-gray-400">Tong tien</span>
                    <span class="text-primary font-black font-mono">{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
                </div>
            </div>
            @if($order->payment_status === 'Pending')
                <p class="text-xs text-yellow-400 bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-3">Don hang dang cho admin xac nhan. Ban co the huy don truoc khi thanh toan.</p>
            @endif
        </aside>
    </div>
</section>
@endsection
