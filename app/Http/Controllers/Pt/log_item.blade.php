<div class="bg-black/20 p-4 rounded-xl border border-white/5 hover:border-primary/30 transition-all group animate-in fade-in slide-in-from-top-4 duration-300" id="log-{{ $log->id }}">
    <div class="flex justify-between items-start mb-2">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-lg">history_edu</span>
            <h4 class="font-bold text-white uppercase text-xs tracking-wide" style="font-family: 'Oswald', sans-serif;">{{ $log->title }}</h4>
        </div>
        <div class="flex flex-col items-end gap-1">
            <span class="text-[9px] text-gray-500 font-mono">{{ \Carbon\Carbon::parse($log->log_date)->format('d/m/Y') }}</span>
            @php
                $statusColors = [
                    'completed' => 'bg-green-500/10 text-green-400 border-green-500/20',
                    'upcoming' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                    'draft' => 'bg-gray-500/10 text-gray-400 border-gray-500/20'
                ];
                $statusLabels = [
                    'completed' => 'Hoàn thành',
                    'upcoming' => 'Sắp tới',
                    'draft' => 'Bản nháp'
                ];
            @endphp
            <span class="px-2 py-0.5 border text-[8px] font-bold rounded uppercase {{ $statusColors[$log->status] ?? $statusColors['draft'] }}">
                {{ $statusLabels[$log->status] ?? $log->status }}
            </span>
        </div>
    </div>
    <p class="text-[11px] text-gray-400 leading-relaxed italic">"{{ $log->content }}"</p>
</div>
