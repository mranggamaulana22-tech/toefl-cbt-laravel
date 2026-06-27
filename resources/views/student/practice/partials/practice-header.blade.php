{{-- ===== TOP BAR: Branding + Participant ===== --}}
<div class="flex items-center justify-between border-b border-white/[0.06] bg-[#111827] px-5 py-2.5">
    <div class="flex items-center gap-2.5">
        <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-purple-700 text-xs font-bold text-white shadow">T</div>
        <div>
            <div class="text-[13px] font-semibold text-white/85 leading-none">TOEFL Practice Test</div>
            <div class="mt-0.5 text-[10px] text-white/35 leading-none">Preparation Mode</div>
        </div>
    </div>
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2">
            <div class="flex h-7 w-7 items-center justify-center rounded-full border border-violet-500/40 bg-violet-500/15 text-[11px] font-semibold text-violet-300">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <span class="text-[12px] text-white/50">{{ auth()->user()->name ?? 'Peserta' }}</span>
        </div>
        <div class="h-4 w-px bg-white/10"></div>
        <span class="text-[11px] text-white/30">Sesi Latihan</span>
    </div>
</div>

{{-- ===== MAIN HEADER: Soal + Timer + Progress ===== --}}
<div class="grid grid-cols-3 items-center gap-4 border-b border-white/[0.06] bg-[#161f30] px-5 py-4">

    {{-- Left: Soal info + badges --}}
    <div class="flex flex-col gap-2">
        <div class="flex items-baseline gap-1.5">
            <span class="text-[12px] text-white/40">Soal</span>
            <span x-text="activeQuestion + 1" class="text-2xl font-bold text-white leading-none"></span>
            <span class="text-[13px] text-white/25">/</span>
            <span class="text-[13px] text-white/35" x-text="totalQuestions"></span>
        </div>
        <div class="flex flex-wrap items-center gap-1.5">
            <span class="inline-flex items-center rounded-md border border-violet-500/30 bg-violet-500/15 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-violet-300"
                  x-text="questionCategories[activeQuestion] || '-'"></span>
            <span class="inline-flex items-center rounded-md border px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider"
                  :class="tabViolationCount >= 2 ? 'border-red-500/40 bg-red-500/15 text-red-300' : 'border-amber-500/30 bg-amber-500/15 text-amber-300'">
                Pelanggaran: <span class="ml-1" x-text="tabViolationCount"></span>/3
            </span>
        </div>
    </div>

    {{-- Center: Timer --}}
    <div class="flex flex-col items-center gap-1.5">
        <div class="rounded-xl border border-white/10 bg-[#1e2a3a] px-5 py-2 text-center">
            <div class="text-[9px] uppercase tracking-widest text-white/30 mb-0.5">Sisa Waktu</div>
            <div class="font-mono text-xl font-semibold leading-none tracking-wider"
                 :class="timerWarning ? 'text-red-400' : 'text-emerald-400'"
                 x-text="formatTime(timeLeft)"></div>
        </div>
        <div class="h-1 w-32 overflow-hidden rounded-full bg-white/[0.07]">
            <div class="h-full rounded-full transition-all duration-1000"
                 :class="timerWarning ? 'bg-red-500' : 'bg-emerald-500'"
                 :style="'width:' + timerPercent + '%'"></div>
        </div>
    </div>

    {{-- Right: Progress + Toggle Navigator --}}
    <div class="flex flex-col items-end gap-2">
        <div class="flex flex-col items-end gap-1">
            <div class="text-[9px] uppercase tracking-widest text-white/30">Progres Pengerjaan</div>
            <div class="h-1.5 w-36 overflow-hidden rounded-full bg-white/[0.07]">
                <div class="h-full rounded-full bg-gradient-to-r from-violet-500 to-purple-400 transition-all duration-300"
                     :style="'width:' + progressPercent + '%'"></div>
            </div>
            <div class="text-[11px] text-white/40">
                <span class="font-medium text-white/70" x-text="answeredCount"></span> dari <span x-text="totalQuestions"></span> terjawab
            </div>
        </div>
        <button type="button" @click="showNavigator = !showNavigator" :disabled="isAutoSubmitting"
            class="inline-flex items-center gap-1.5 rounded-lg border border-white/10 bg-white/[0.05] px-2.5 py-1 text-[11px] font-medium text-white/50 transition hover:border-white/20 hover:text-white/80 disabled:cursor-not-allowed disabled:opacity-60">
            <svg x-show="!showNavigator" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor" x-cloak>
                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm1 3a1 1 0 100 2h12a1 1 0 100-2H4z" clip-rule="evenodd" />
            </svg>
            <svg x-show="showNavigator" xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
            <span x-text="showNavigator ? 'Sembunyikan Navigasi' : 'Tampilkan Navigasi'"></span>
        </button>
    </div>
</div>