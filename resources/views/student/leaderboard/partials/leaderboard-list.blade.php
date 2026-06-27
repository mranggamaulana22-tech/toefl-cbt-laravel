{{-- Leaderboard list with pagination --}}
<div class="space-y-4">
    @php
        $items = $rankings ?? $leaders ?? [];
    @endphp

    @if($items && count($items) > 0)
        @foreach($items as $pos => $leader)
            <div class="flex items-center justify-between p-5 rounded-2xl bg-white/3 border border-white/5 hover:bg-white/5 transition-all">
                <div class="flex items-center gap-4 flex-1">
                    <div class="w-14 h-14 rounded-full bg-gradient-to-br from-indigo-600 to-purple-600 flex items-center justify-center font-black text-white text-lg">
                        {{ $items->firstItem() + $pos }}
                    </div>
                    <div>
                        <div class="font-bold text-white">{{ $leader->user->name ?? $leader->name ?? 'Unknown' }}</div>
                        <div class="text-xs text-slate-400">{{ $leader->user->email ?? $leader->email ?? '-' }}</div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-black text-2xl text-indigo-400">{{ $leader->best_score ?? $leader->score ?? 0 }}</div>
                    <div class="text-xs text-slate-400 uppercase tracking-wider">{{ $leader->attempt_count ?? $leader->attempts ?? 0 }} attempts</div>
                </div>
            </div>
        @endforeach

        {{-- Pagination links --}}
        @if($items instanceof \Illuminate\Pagination\Paginator && $items->hasPages())
        <div class="mt-8 flex items-center justify-center gap-2">
            @if ($items->onFirstPage())
                <span class="px-3 py-2 rounded-lg text-slate-500 cursor-not-allowed">← Prev</span>
            @else
                <a href="{{ $items->previousPageUrl() }}" class="px-3 py-2 rounded-lg bg-indigo-600/20 text-indigo-300 hover:bg-indigo-600/40 transition-all">← Prev</a>
            @endif

            <div class="flex gap-1">
                @foreach ($items->getUrlRange(1, $items->lastPage()) as $page => $url)
                    @if ($page == $items->currentPage())
                        <span class="px-3 py-2 rounded-lg bg-indigo-600 text-white font-bold">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="px-3 py-2 rounded-lg bg-white/5 text-slate-300 hover:bg-white/10 transition-all">{{ $page }}</a>
                    @endif
                @endforeach
            </div>

            @if ($items->hasMorePages())
                <a href="{{ $items->nextPageUrl() }}" class="px-3 py-2 rounded-lg bg-indigo-600/20 text-indigo-300 hover:bg-indigo-600/40 transition-all">Next →</a>
            @else
                <span class="px-3 py-2 rounded-lg text-slate-500 cursor-not-allowed">Next →</span>
            @endif
        </div>
        @endif
    @else
        <div class="p-8 rounded-2xl bg-white/3 border border-white/5 text-center">
            <p class="text-slate-400">Belum ada data leaderboard.</p>
        </div>
    @endif
</div>
