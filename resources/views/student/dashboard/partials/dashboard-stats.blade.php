{{-- STATS GRID --}}
<section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
    {{-- Daily Goal Progress (Card 1) --}}
    <div x-cloak x-show="loaded"
         x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-[1600ms]"
         x-transition:enter-start="opacity-0 translate-y-12 scale-[0.95]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         style="transition-delay: 600ms;"
         class="group border rounded-[2.5rem] p-8 flex flex-col transition-all duration-300 shadow-2xl overflow-hidden"
         :class="$store.theme?.isDark 
             ? 'bg-black/40 border-white/10 shadow-black/60 hover:bg-black/60' 
             : 'bg-white/60 border-black/5 backdrop-blur-md hover:bg-white/80 shadow-lg'">
        <h3 class="text-[10px] font-black uppercase tracking-[0.15em] mb-8 transition-colors"
            :class="$store.theme?.isDark ? 'text-slate-400 group-hover:text-white' : 'text-slate-600 group-hover:text-indigo-600'">
            Daily Goal Progress
        </h3>
        <div class="flex items-end gap-2 mt-auto">
            <span class="text-4xl font-black italic tracking-tighter readable-text"
                  :class="$store.theme?.isDark ? 'text-white' : 'text-slate-950'">
                {{ auth()->user()->streak_count }}x
            </span>
            <span class="text-indigo-400 font-bold mb-1 italic uppercase text-[10px]">Streak 🔥</span>
        </div>
        <p class="text-[10px] text-slate-400 mt-1">
            {{ auth()->user()->streak_count > 5 ? 'You are on fire!' : 'Keep logging in every day!' }}
        </p>
    </div>

    {{-- Practice Track (Card 2) --}}
    <div x-cloak x-show="loaded"
         x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-[1600ms]"
         x-transition:enter-start="opacity-0 translate-y-12 scale-[0.95]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         style="transition-delay: 800ms;"
         class="group border rounded-[2.5rem] p-8 flex flex-col transition-all duration-300 shadow-2xl overflow-hidden"
         :class="$store.theme?.isDark 
             ? 'bg-black/40 border-white/10 shadow-black/60 hover:bg-black/60' 
             : 'bg-white/60 border-black/5 backdrop-blur-md hover:bg-white/80 shadow-lg'">
        <h3 class="text-[10px] font-black uppercase tracking-[0.15em] mb-8 transition-colors"
            :class="$store.theme?.isDark ? 'text-slate-400 group-hover:text-white' : 'text-slate-600 group-hover:text-indigo-600'">
            Practice Track
        </h3>
        <div class="flex items-end gap-2 mt-auto">
            <span class="text-4xl font-black italic tracking-tighter readable-text" 
                  :class="$store.theme?.isDark ? 'text-white' : 'text-slate-950'">
                {{ $studentStats['practice_attempts'] }}
            </span>
            <span class="text-indigo-400 font-bold mb-1 italic uppercase text-[10px]">Latihan Selesai 📚</span>
        </div>
        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider">
            {{ $studentStats['practice_attempts'] > 0 ? 'Hebat! Terus asah kemampuanmu.' : 'Mulai latihan pertamamu!' }}
        </p>
    </div>

    {{-- Latest Results (Card 3) --}}
    <div x-cloak x-show="loaded"
         x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-[1600ms]"
         x-transition:enter-start="opacity-0 translate-y-12 scale-[0.95]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         style="transition-delay: 1000ms;"
         class="group border rounded-[2.5rem] p-8 flex flex-col transition-all duration-300 shadow-2xl overflow-hidden"
         :class="$store.theme?.isDark 
             ? 'bg-black/40 border-white/10 shadow-black/60 hover:bg-black/60' 
             : 'bg-white/60 border-black/5 backdrop-blur-md hover:bg-white/80 shadow-lg'">
        <h3 class="text-[10px] font-black uppercase tracking-[0.15em] mb-8 transition-colors"
            :class="$store.theme?.isDark ? 'text-slate-400 group-hover:text-white' : 'text-slate-600 group-hover:text-indigo-600'">
            Latest Results
        </h3>
        <div class="flex flex-col mt-auto">
            <span class="text-3xl font-black italic tracking-tighter readable-text" 
                  :class="$store.theme?.isDark ? 'text-white' : 'text-slate-950'">
                {{ $studentStats['latest_score'] ?? '0' }}
            </span>
            <span class="text-[10px] font-bold uppercase mt-1 opacity-60"
                  :class="$store.theme?.isDark ? 'text-slate-400' : 'text-slate-700'">
                Exam Score
            </span>
        </div>
    </div>

    {{-- Practice Rank (Card 4) - UPDATE: NOW CLICKABLE --}}
    <a href="{{ route('leaderboard') }}"
         x-cloak x-show="loaded"
         x-transition:enter="transition ease-[cubic-bezier(0.22,1,0.36,1)] duration-[1600ms]"
         x-transition:enter-start="opacity-0 translate-y-12 scale-[0.95]"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         style="transition-delay: 1200ms;"
         class="group border rounded-[2.5rem] p-8 flex flex-col transition-all duration-300 shadow-2xl overflow-hidden hover:scale-[1.02] active:scale-95 cursor-pointer"
         :class="$store.theme?.isDark 
             ? 'bg-black/40 border-white/10 shadow-black/60 hover:bg-black/60' 
             : 'bg-white/60 border-black/5 backdrop-blur-md hover:bg-white/80 shadow-lg'">
        <h3 class="text-[10px] font-black uppercase tracking-[0.15em] mb-8 transition-colors"
            :class="$store.theme?.isDark ? 'text-slate-400 group-hover:text-white' : 'text-slate-600 group-hover:text-indigo-600'">
            Practice Rank
        </h3>
        <div class="flex items-end gap-2 mt-auto">
            <span class="text-4xl font-black italic tracking-tighter readable-text" 
                  :class="$store.theme?.isDark ? 'text-white' : 'text-slate-950'">
                #{{ $studentStats['practice_rank'] ?? '-' }}
            </span>
            <span class="text-indigo-400 font-bold mb-1 italic uppercase text-[10px]">Peringkat 🏆</span>
        </div>
        <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-wider group-hover:text-indigo-400 transition-colors">
            Klik untuk detail leaderboard
        </p>
    </a>

</section>
</div>
</main>
</div>
