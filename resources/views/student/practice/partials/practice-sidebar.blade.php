<div class="w-full md:w-1/4" x-show="showNavigator"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-2"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-2">
    <div class="sticky top-6 rounded-3xl border border-white/10 bg-[#161f30] p-6 shadow-[0_18px_50px_rgba(0,0,0,0.4)]">
        <h4 class="mb-4 border-b border-white/[0.06] pb-2 text-sm font-bold uppercase tracking-[0.18em] text-white/40">Daftar Soal</h4>
        <div class="grid grid-cols-5 gap-2">
            @foreach($questions as $index => $q)
            <button @click="goToQuestion({{ $index }})" :disabled="isAutoSubmitting"
                    :class="activeQuestion === {{ $index }} ? 'bg-violet-600 text-white ring-4 ring-violet-500/20' : (answers[{{ $index }}] ? 'bg-emerald-500 text-white' : 'bg-white/[0.06] text-white/40')"
                class="flex h-10 w-10 items-center justify-center rounded-xl text-xs font-bold transition-all disabled:cursor-not-allowed disabled:opacity-60">
                {{ $index + 1 }}
            </button>
            @endforeach
        </div>
        
        <div class="mt-8 space-y-2 text-xs">
            <div class="flex items-center gap-2 text-white/40"><span class="h-3 w-3 rounded-sm bg-violet-600"></span> Posisi Sekarang</div>
            <div class="flex items-center gap-2 text-white/40"><span class="h-3 w-3 rounded-sm bg-emerald-500"></span> Sudah Terjawab</div>
            <div class="flex items-center gap-2 text-white/40"><span class="h-3 w-3 rounded-sm bg-white/[0.06]"></span> Belum Terjawab</div>
        </div>
    </div>
</div>