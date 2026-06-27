<x-app-layout>
    <template x-if="$store.bg.enabled">
        <div class="hidden sm:block w-full h-full absolute inset-0 z-0"
            :style="$store.theme?.isDark
                ? 'background-image: url(\\'{{ asset('images/classroom_dark.png') }}\\'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: scroll; opacity:0.6;'
                : 'background-image: url(\\'{{ asset('images/classroom_light.png') }}\\'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: scroll; opacity:1;'"></div>
    </template>
    <div x-show="!$store.bg.enabled" :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-white'" class="absolute inset-0 w-full h-full transition-colors duration-500 z-0"></div>
    
    <div class="py-8 relative z-10 w-full" x-data="{ loaded: false, init() { setTimeout(() => { this.loaded = true }, 350) } }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            @include('student.exam.partials.result-skeleton')

            {{-- MAIN CARD: Glassmorphism --}}
            <div x-cloak x-show="loaded" 
                 class="overflow-hidden rounded-3xl border shadow-[0_18px_50px_rgba(0,0,0,0.3)] backdrop-blur-xl transition-colors duration-500"
                 :class="$store.theme?.isDark ? 'bg-[#0f172a]/95 border-white/10' : 'bg-white/95 border-slate-200'">
                
                {{-- HEADER GRADIENT --}}
                <div class="px-6 py-5 text-white md:px-8"
                     :class="$store.theme?.isDark ? 'bg-gradient-to-r from-emerald-600/90 to-sky-600/90' : 'bg-gradient-to-r from-emerald-500 to-sky-500'">
                    <p class="text-xs font-bold uppercase tracking-[0.22em] text-white/80">Pengumpulan Selesai</p>
                    <h2 class="mt-2 text-3xl font-black tracking-tight">Tes selesai dengan baik.</h2>
                    <p class="mt-2 text-sm text-white/80">Terima kasih sudah menyelesaikan tes tepat waktu.</p>
                </div>

                <div class="p-6 md:p-8">
                    <div class="grid gap-6 lg:grid-cols-[1fr_0.9fr] lg:items-center">
                        
                        {{-- INNER LEFT CARD: Skor Total --}}
                        <div class="rounded-3xl border p-6 transition-colors duration-500"
                             :class="$store.theme?.isDark ? 'bg-black/40 border-white/5' : 'bg-slate-50 border-slate-200'">
                            <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Skor Total</p>
                            <div class="mt-4 flex items-end gap-3">
                                <span class="text-6xl font-black tracking-tight" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $result->score_total }}</span>
                                <span class="pb-2 text-sm font-semibold" :class="$store.theme?.isDark ? 'text-slate-400' : 'text-slate-500'">skor</span>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl border p-4 transition-colors" :class="$store.theme?.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-slate-200'">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Listening</p>
                                    <p class="mt-2 text-2xl font-black" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $result->correct_listening }}</p>
                                </div>
                                <div class="rounded-2xl border p-4 transition-colors" :class="$store.theme?.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-slate-200'">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Structure</p>
                                    <p class="mt-2 text-2xl font-black" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $result->correct_structure }}</p>
                                </div>
                                <div class="rounded-2xl border p-4 transition-colors" :class="$store.theme?.isDark ? 'bg-white/5 border-white/10' : 'bg-white border-slate-200'">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-slate-400">Reading</p>
                                    <p class="mt-2 text-2xl font-black" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $result->correct_reading }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- INNER RIGHT CARD: Langkah Berikutnya --}}
                        <div class="h-full">
                            <div class="rounded-3xl border p-6 text-white shadow-2xl transition-colors duration-500 h-full flex flex-col justify-center"
                                 :class="$store.theme?.isDark ? 'bg-gradient-to-br from-indigo-900/90 via-indigo-800/90 to-sky-900/90 border-indigo-500/30' : 'bg-gradient-to-br from-indigo-700 via-indigo-600 to-sky-600 border-indigo-300 shadow-[0_16px_40px_rgba(30,64,175,0.30)]'">
                                <p class="text-xs font-bold uppercase tracking-[0.22em] text-indigo-200">Langkah Berikutnya</p>
                                <h3 class="mt-2 text-2xl font-black">Lanjut ke dashboard atau cetak sertifikat.</h3>
                                <p class="mt-3 text-sm leading-7 text-indigo-100/90">Kamu bisa melihat riwayat lengkap atau mencetak sertifikat untuk arsip pribadi.</p>

                                <div class="mt-6 flex flex-col gap-3">
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-indigo-700 transition hover:bg-indigo-50 hover:scale-[1.02] active:scale-95 shadow-lg">
                                        Kembali ke Dashboard
                                    </a>
                                    {{-- PERBAIKAN: Tombol Cetak Sertifikat diubah menjadi navigasi ke halaman Riwayat --}}
                                    <a href="{{ route('student.results.index') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20 hover:scale-[1.02] active:scale-95">
                                        <i class="fas fa-print mr-2"></i> Cetak Sertifikat
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>