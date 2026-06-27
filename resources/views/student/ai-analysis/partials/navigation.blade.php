<div class="flex justify-center mb-10">
    {{-- Container diubah menjadi dinamis mengikuti $store.theme.isDark --}}
    <div class="inline-flex p-1.5 rounded-full border shadow-inner transition-colors duration-500"
         :class="$store.theme?.isDark ? 'bg-[#0b0d13] border-white/10' : 'bg-slate-100 border-slate-200'">
        <button
            @click="switchTab('practice')"
            :class="activeTab === 'practice' 
                ? ($store.theme?.isDark ? 'bg-slate-800 text-indigo-300 shadow-md ring-1 ring-white/10 scale-100' : 'bg-white text-indigo-700 shadow-md ring-1 ring-slate-900/5 scale-100') 
                : ($store.theme?.isDark ? 'text-slate-400 hover:text-slate-200 hover:bg-white/5 scale-95 hover:scale-100' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/50 scale-95 hover:scale-100')"
            class="relative px-8 py-2.5 text-sm font-bold rounded-full transition-all duration-300 w-32 sm:w-40 text-center focus:outline-none"
        >
            Latihan
        </button>
        <button
            @click="switchTab('exam')"
            :class="activeTab === 'exam' 
                ? ($store.theme?.isDark ? 'bg-slate-800 text-indigo-300 shadow-md ring-1 ring-white/10 scale-100' : 'bg-white text-indigo-700 shadow-md ring-1 ring-slate-900/5 scale-100') 
                : ($store.theme?.isDark ? 'text-slate-400 hover:text-slate-200 hover:bg-white/5 scale-95 hover:scale-100' : 'text-slate-500 hover:text-slate-800 hover:bg-slate-200/50 scale-95 hover:scale-100')"
            class="relative px-8 py-2.5 text-sm font-bold rounded-full transition-all duration-300 w-32 sm:w-40 text-center focus:outline-none"
        >
            Ujian
        </button>
    </div>
</div>

<div class="mb-8 relative max-w-6xl mx-auto">
    <template x-if="currentHistory.length === 0">
        <div class="text-center p-8 border border-dashed border-slate-300 dark:border-slate-700 rounded-2xl bg-slate-50/50 dark:bg-slate-800/30">
            <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada sesi <span x-text="sessionTypeLabel"></span>.</p>
        </div>
    </template>
    
    <div class="flex items-center gap-2" x-show="currentHistory.length > 0">
        {{-- PERBAIKAN: Ubah batas > 4 menjadi > 3 agar panah lebih cepat muncul --}}
        <button
            x-show="currentHistory.length > 3"
            @click="$refs.sessionScroll.scrollBy({left: -320, behavior: 'smooth'})"
            class="h-9 w-9 flex shrink-0 items-center justify-center rounded-full bg-white/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 shadow hover:bg-indigo-100 dark:hover:bg-indigo-900 transition z-10 mr-1"
        >
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        
        <div class="flex-1 overflow-x-auto scrollbar-hide scroll-smooth" style="scrollbar-width: none;" x-ref="sessionScroll">
            <div class="flex gap-2 min-w-max py-2 px-1">
                <template x-for="item in currentHistory" :key="item.id">
                    <button
                        @click="selectItem(item)"
                        :class="selectedItem && selectedItem.id === item.id
                            ? 'bg-indigo-600 text-white border-indigo-600 shadow-md ring-2 ring-indigo-600 ring-offset-2 dark:ring-offset-slate-900 transform scale-[1.02]'
                            : 'bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 border-slate-200 dark:border-slate-700 hover:bg-indigo-50 dark:hover:bg-slate-700 hover:scale-[1.02]'"
                        class="flex items-center justify-between gap-3 px-5 py-2.5 rounded-full border font-medium text-sm transition-all duration-300 min-w-[180px] max-w-xs whitespace-nowrap focus:outline-none"
                    >
                        <span x-text="item.title || 'Sesi ' + item.id"></span>
                        <span class="text-[11px] opacity-70 font-normal" x-text="item.created_at"></span>
                    </button>
                </template>
            </div>
        </div>
        
        {{-- PERBAIKAN: Ubah batas > 4 menjadi > 3 --}}
        <button
            x-show="currentHistory.length > 3"
            @click="$refs.sessionScroll.scrollBy({left: 320, behavior: 'smooth'})"
            class="h-9 w-9 flex shrink-0 items-center justify-center rounded-full bg-white/80 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 shadow hover:bg-indigo-100 dark:hover:bg-indigo-900 transition z-10 ml-1"
        >
            <svg width="18" height="18" fill="none" viewBox="0 0 24 24"><path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
    </div>
</div>