<div x-show="selectedItem" 
     x-transition:enter="transition ease-out duration-500 delay-100"
     x-transition:enter-start="opacity-0 scale-95 translate-y-4"
     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
     class="space-y-6 max-w-6xl mx-auto" x-cloak>
     
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <button
            id="ai-generate-btn"
            class="px-5 py-2.5 rounded-xl font-bold border border-indigo-600 dark:border-indigo-500 bg-indigo-600 hover:bg-indigo-700 text-white transition-all shadow-md flex items-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed transform active:scale-95"
            @click="generateForSelected()"
            :disabled="generating || !selectedItem"
        >
            <template x-if="generating">
                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                </svg>
            </template>
            <svg x-show="!generating" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
            <span data-role="label" x-text="generating ? 'Memproses...' : 'Analisa AI'"></span>
        </button>
        <span class="rounded-full border px-4 py-1.5 text-xs font-bold tracking-wide uppercase transition-colors duration-300" :class="aiStatusClass()" x-text="aiStatusLabel()"></span>
        <span class="ai-raw-model rounded-full border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-4 py-1.5 text-xs font-bold tracking-wide text-slate-600 dark:text-slate-300 uppercase" x-text="`${modelFamily()} · ${resolvedModel()}`"></span>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <div class="ai-card h-full bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700/60 rounded-3xl p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <p class="text-xl font-extrabold text-slate-900 dark:text-white">Kekuatan</p>
            </div>
            <p class="text-base font-medium leading-relaxed text-slate-700 dark:text-slate-300" x-text="analysis.data?.strengths || 'Belum ada interpretasi kekuatan. Klik Generate Analisis.'"></p>
        </div>
        
        <div class="ai-card h-full bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700/60 rounded-3xl p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <p class="text-xl font-extrabold text-slate-900 dark:text-white">Kelemahan</p>
            </div>
            <p class="text-base font-medium leading-relaxed text-slate-700 dark:text-slate-300" x-text="analysis.data?.weaknesses || 'Belum ada interpretasi kelemahan. Klik Generate Analisis.'"></p>
        </div>
    </div>

    <div class="grid gap-4 lg:grid-cols-2">
        <div class="ai-card h-full bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700/60 rounded-3xl p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
            <div class="mb-4 flex items-center justify-between">
                <p class="ai-label text-lg font-bold text-indigo-700 dark:text-indigo-300">Pola Kesalahan</p>
            </div>
            <p class="text-base font-medium leading-relaxed text-slate-700 dark:text-slate-300" x-text="analysis.data?.error_pattern || 'Belum ada pola kesalahan dari AI. Klik Generate Analisis.'"></p>
        </div>
        
        <div class="ai-card h-full bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700/60 rounded-3xl p-6 transition-all duration-300 hover:shadow-lg hover:-translate-y-1">
            <p class="ai-label mb-4 text-lg font-bold text-indigo-700 dark:text-indigo-300">Motivasi Belajar</p>
            <p x-show="analysis.data?.motivation" class="text-base font-medium leading-relaxed text-slate-700 dark:text-slate-300" x-text="analysis.data?.motivation"></p>
            <p x-show="!analysis.data?.motivation" class="text-base italic text-slate-400 dark:text-slate-500">Belum ada motivasi belajar dari AI. Klik Generate analisis.</p>
        </div>
    </div>

    <div class="ai-card space-y-5 bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700/60 rounded-3xl p-6 transition-all duration-300 hover:shadow-lg">
        <p class="ai-label text-lg font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-700 pb-3">Rencana 3 Hari Kedepan</p>
        
        <div class="flex flex-wrap gap-2" x-show="planRows().length > 0">
            <template x-for="day in planRows()" :key="day.day">
                <button type="button" 
                    class="px-4 py-2 rounded-lg font-bold text-sm transition-all duration-200 border"
                    :class="activePlanDay === day.day 
                        ? 'bg-indigo-600 text-white border-indigo-600 shadow-md transform scale-105' 
                        : 'bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 hover:scale-105'" 
                    @click="setPlanDay(day.day)" x-text="'Hari ' + day.day">
                </button>
            </template>
        </div>
        <p x-show="planRows().length === 0" class="text-sm italic text-slate-400">Belum ada rencana 3 hari dari AI.</p>
        
        <div class="rounded-2xl p-5 bg-slate-50 dark:bg-[#121212] border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-slate-100" 
             x-show="selectedPlan()"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100">
            <p class="text-xl font-bold text-slate-900 dark:text-slate-50 mb-2" x-text="`Hari ${selectedPlan()?.day} — ${selectedPlan()?.title}`"></p>
            <p class="text-base leading-relaxed text-slate-700 dark:text-slate-200" x-text="selectedPlan()?.desc"></p>
            <div class="mt-4 flex flex-wrap gap-2">
                <template x-for="(tag, idx) in (selectedPlan()?.tags || [])" :key="idx">
                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-full text-xs font-bold text-black shadow-sm" x-text="tag"></span>
                </template>
            </div>
        </div>
        
        <div class="grid sm:grid-cols-2 gap-4 mt-4">
            <div class="rounded-2xl border border-green-200 dark:border-green-900/50 bg-green-50/80 dark:bg-green-900/20 p-5 hover:bg-green-100/50 dark:hover:bg-green-900/40 transition-colors">
                <div class="flex items-center gap-2 mb-2 text-green-700 dark:text-green-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    <p class="font-bold">Prioritas</p>
                </div>
                <p x-show="analysis.data?.priority_tip" class="text-sm font-medium leading-relaxed text-green-900 dark:text-green-200" x-text="analysis.data?.priority_tip"></p>
                <p x-show="!analysis.data?.priority_tip" class="text-sm italic text-green-700/60 dark:text-green-500/60">Kosong.</p>
            </div>
            <div class="rounded-2xl border border-red-200 dark:border-red-900/50 bg-red-50/80 dark:bg-red-900/20 p-5 hover:bg-red-100/50 dark:hover:bg-red-900/40 transition-colors">
                <div class="flex items-center gap-2 mb-2 text-red-700 dark:text-red-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <p class="font-bold">Peringatan</p>
                </div>
                <p x-show="analysis.data?.warning" class="text-sm font-medium leading-relaxed text-red-900 dark:text-red-200" x-text="analysis.data?.warning"></p>
                <p x-show="!analysis.data?.warning" class="text-sm italic text-red-700/60 dark:text-red-500/60">Kosong.</p>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-slate-800 shadow-sm border border-slate-100 dark:border-slate-700/60 mt-4 rounded-3xl p-6" x-data="{ showRaw: false }">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Output Mentah AI</p>
            {{-- Tombol Tampilkan JSON sudah diperbarui dengan class dinamis --}}
            <button type="button" 
                class="px-4 py-2 rounded-lg text-xs font-bold transition-all focus:outline-none" 
                :class="$store.theme?.isDark ? 'bg-slate-700 text-white hover:bg-slate-600 border border-slate-600' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border border-slate-200'"
                @click="showRaw = !showRaw">
                <span x-show="!showRaw">Tampilkan JSON</span>
                <span x-show="showRaw">Sembunyikan JSON</span>
            </button>
        </div>
        <div x-show="showRaw" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-collapse class="mt-4">
            <pre x-show="analysis.raw" class="max-h-96 overflow-auto whitespace-pre-wrap rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 p-4 text-[13px] leading-relaxed text-slate-700 dark:text-slate-300 font-mono" x-text="analysis.raw"></pre>
            <p x-show="!analysis.raw" class="text-sm italic text-slate-400 text-center py-4">Belum ada output AI.</p>
        </div>
    </div>
</div>