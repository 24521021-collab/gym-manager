@extends('layout.frontend')

@section('title', 'Don hang cua toi')

@section('content')
<section class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <p class="text-primary text-xs font-bold uppercase tracking-widest">Member Orders</p>
            <h1 class="font-headline text-3xl md:text-4xl text-white uppercase tracking-tight">Don hang cua toi</h1>
            <p class="text-sm text-gray-400 mt-1">Theo doi lich su mua goi tap, san pham va lop hoc.</p>
        </div>
        <form method="GET" action="{{ route('orders.index') }}" class="flex gap-2">
            <select name="status" class="bg-black/30 border border-white/10 rounded-xl text-sm text-white px-3 py-2">
                <option value="">Tat ca trang thai</option>
                @foreach(['Pending' => 'Cho xac nhan', 'Paid' => 'Da thanh toan', 'Cancelled' => 'Da huy'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <button class="bg-primary hover:bg-red-700 text-white text-xs font-bold uppercase px-4 py-2 rounded-xl">Loc</button>
        </form>
    </div>

    @if(session('success'))
        <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 p-4 rounded-xl text-sm font-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 rounded-xl text-sm font-bold">{{ session('error') }}</div>
    @endif

    <div class="bg-[#1A1A1A] border border-white/10 rounded-2xl overflow-hidden">
        <div class="hidden md:grid grid-cols-12 gap-4 px-5 py-3 bg-black/30 text-[10px] uppercase tracking-widest text-gray-500 font-bold">
            <span class="col-span-2">Ma don</span>
            <span class="col-span-3">Ngay dat</span>
            <span class="col-span-3">Mat hang</span>
            <span class="col-span-2">Trang thai</span>
            <span class="col-span-2 text-right">Tong tien</span>
        </div>

        @forelse($orders as $order)
            <a href="{{ route('orders.show', $order->id) }}" class="grid grid-cols-1 md:grid-cols-12 gap-3 md:gap-4 px-5 py-4 border-t border-white/5 hover:bg-white/[0.03] transition-colors">
                <div class="md:col-span-2">
                    <p class="text-xs text-gray-500 md:hidden">Ma don</p>
                    <p class="text-white font-bold font-mono">#ORD-{{ $order->id }}</p>
                </div>
                <div class="md:col-span-3">
                    <p class="text-xs text-gray-500 md:hidden">Ngay dat</p>
                    <p class="text-gray-300 text-sm">{{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y H:i') }}</p>
                </div>
                <div class="md:col-span-3">
                    <p class="text-xs text-gray-500 md:hidden">Mat hang</p>
                    <p class="text-gray-300 text-sm">{{ $order->items->count() }} dong san pham/dich vu</p>
                </div>
                <div class="md:col-span-2">
                    @php
                        $badge = [
                            'Paid' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                            'Cancelled' => 'bg-red-500/10 text-red-400 border-red-500/20',
                            'Pending' => 'bg-yellow-500/10 text-yellow-400 border-yellow-500/20',
                        ][$order->payment_status] ?? 'bg-white/10 text-gray-300 border-white/10';
                    @endphp
                    <span class="inline-flex border {{ $badge }} rounded-full px-3 py-1 text-[10px] font-bold uppercase">{{ $order->payment_status }}</span>
                </div>
                <div class="md:col-span-2 md:text-right">
                    <p class="text-primary font-bold font-mono">{{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                </div>
            </a>
        @empty
            <div class="p-10 text-center text-gray-500 text-sm italic">Ban chua co don hang nao.</div>
        @endforelse
    </div>

    <div>{{ $orders->links() }}</div>
</section>
@endsection
