<x-app-layout>
    <div x-show="$store.bg.enabled" class="ai-bg-img absolute inset-0 w-full h-full z-0"></div>
    <div x-show="$store.bg.enabled" class="ai-bg-gradient"></div>
    <div x-show="!$store.bg.enabled" :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-white'" class="absolute inset-0 w-full h-full transition-colors duration-500 z-0"></div>
    @include('student.partials.shared-bg-styles')
    @include('student.partials.shared-utils-styles')

<style>
/* ============================================================
   RESULTS PAGE SPECIFIC ANIMATIONS
   ============================================================ */

@keyframes ambientFloat {
    0%, 100% { transform: translateY(0px) scale(1); }
    50%       { transform: translateY(-18px) scale(1.04); }
}

/* ============================================================
   AMBIENT BACKGROUND GLOW — subtle floating motion
   ============================================================ */
.fixed.top-0.right-0 {
    animation: ambientFloat 9s ease-in-out infinite;
}
.fixed.bottom-0.left-0 {
    animation: ambientFloat 12s ease-in-out infinite reverse;
}

/* ============================================================
   STAT CARDS — staggered scale-in entrance
   ============================================================ */
.results-card {
    animation: scaleIn 0.55s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1),
                box-shadow 0.25s ease;
}
.results-card:hover {
    transform: translateY(-4px) scale(1.015);
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.10);
}

/* Stagger delays — exam summary cards */
.grid:first-of-type .results-card:nth-child(1) { animation-delay: 0.10s; }
.grid:first-of-type .results-card:nth-child(2) { animation-delay: 0.20s; }
.grid:first-of-type .results-card:nth-child(3) { animation-delay: 0.30s; }

/* Stagger delays — practice summary cards */
.grid.pt-2 .results-card:nth-child(1) { animation-delay: 0.45s; }
.grid.pt-2 .results-card:nth-child(2) { animation-delay: 0.55s; }
.grid.pt-2 .results-card:nth-child(3) { animation-delay: 0.65s; }

/* Big number pop-in */
.results-card .text-3xl {
    animation: countUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    animation-delay: inherit;
}

/* ============================================================
   PANELS — fade + slide up entrance
   ============================================================ */
.results-panel {
    animation: fadeSlideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) both;
    transition: box-shadow 0.35s ease;
}
.results-panel:hover {
    box-shadow: 0 0 32px 6px rgba(99, 102, 241, 0.10), 0 18px_50px rgba(15,23,42,0.08);
}

/* Panel header subtle shimmer line */
.results-panel-head::after {
    content: '';
    display: block;
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 1px;
    background: linear-gradient(90deg,
        transparent 0%,
        rgba(99,102,241,0.35) 40%,
        rgba(99,102,241,0.35) 60%,
        transparent 100%);
    background-size: 200% 100%;
    animation: shimmer 3s linear infinite;
    opacity: 0.6;
}
.results-panel-head {
    position: relative;
}

/* ============================================================
   TABLE ROWS — staggered slide-in
   ============================================================ */
.results-table-row {
    animation: rowSlideIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    transition: background-color 0.2s ease, transform 0.2s ease;
}
.results-table-row:hover {
    transform: translateX(4px);
}

/* Row stagger (up to 10 rows) */
.results-divider tr:nth-child(1)  { animation-delay: 0.10s; }
.results-divider tr:nth-child(2)  { animation-delay: 0.18s; }
.results-divider tr:nth-child(3)  { animation-delay: 0.26s; }
.results-divider tr:nth-child(4)  { animation-delay: 0.34s; }
.results-divider tr:nth-child(5)  { animation-delay: 0.42s; }
.results-divider tr:nth-child(6)  { animation-delay: 0.50s; }
.results-divider tr:nth-child(7)  { animation-delay: 0.58s; }
.results-divider tr:nth-child(8)  { animation-delay: 0.66s; }
.results-divider tr:nth-child(9)  { animation-delay: 0.74s; }
.results-divider tr:nth-child(10) { animation-delay: 0.82s; }

/* Table header fade-in */
.results-table-head {
    animation: fadeIn 0.5s ease both;
    animation-delay: 0.05s;
}

/* ============================================================
   MOBILE CARDS — slide-up stagger
   ============================================================ */
.results-mobile-row {
    animation: fadeSlideUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.results-mobile-row:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.09);
}
.results-mobile-row:nth-child(1) { animation-delay: 0.10s; }
.results-mobile-row:nth-child(2) { animation-delay: 0.20s; }
.results-mobile-row:nth-child(3) { animation-delay: 0.30s; }
.results-mobile-row:nth-child(4) { animation-delay: 0.40s; }
.results-mobile-row:nth-child(5) { animation-delay: 0.50s; }

.results-mobile-mini {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.results-mobile-mini:hover {
    transform: scale(1.04);
    box-shadow: 0 4px 14px rgba(15, 23, 42, 0.08);
}

/* ============================================================
   BUTTONS — lift on hover
   ============================================================ */
a.bg-indigo-600,
a.bg-emerald-600 {
    transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1),
                box-shadow 0.2s ease,
                background-color 0.2s ease;
}
a.bg-indigo-600:hover,
a.bg-emerald-600:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.25);
}
a.bg-indigo-600:active,
a.bg-emerald-600:active {
    transform: scale(0.97);
}
</style>


    <div class="results-page min-h-screen text-white font-sans selection:bg-purple-500/30 overflow-hidden py-8 pt-4 sm:pt-8 lg:pt-10" x-data="{ loaded: false, init() { setTimeout(() => { this.loaded = true }, 350) } }">
        <!-- BACKGROUND SECTION -->
        <div class="fixed inset-0 z-0">
            <template x-if="$store.bg.enabled">
                <div class="w-full h-full absolute inset-0"
                    :style="$store.theme?.isDark
                        ? 'background-image: url(\'{{ asset('images/classroom_dark.png') }}\'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: scroll; opacity:0.6;'
                        : 'background-image: url(\'{{ asset('images/classroom_light.png') }}\'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: scroll; opacity:1;'"></div>
            </template>
            <div x-show="$store.bg.enabled" class="absolute inset-0 transition-all duration-700"
                :class="$store.theme?.isDark
                    ? 'bg-gradient-to-tr from-[#0b0d13] via-transparent to-transparent'
                    : 'bg-gradient-to-t from-white/70 via-white/10 to-transparent'">
            </div>
            <div x-show="!$store.bg.enabled" :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-white'" class="absolute inset-0 w-full h-full transition-colors duration-500"></div>
        </div>

        <!-- AMBIENT GLOW -->
        <div class="fixed top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 blur-[120px] rounded-full pointer-events-none z-0"></div>
        <div class="fixed bottom-0 left-0 w-[300px] h-[300px] bg-purple-600/10 blur-[100px] rounded-full pointer-events-none z-0"></div>

        <div class="relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @include('student.results.partials.index-skeleton')

            <div x-cloak x-show="loaded" x-transition:enter="transition-all duration-700 ease-out" x-transition:enter-start="opacity-0 translate-y-6" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
            <div class="grid gap-4 md:grid-cols-3" x-transition:enter="transition-all duration-700 ease-out delay-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="results-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Total Percobaan</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $summary['total_attempts'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">Semua ujian yang sudah disubmit</p>
                </div>
                <div class="results-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Skor Terakhir</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $summary['latest_score'] ?? '-' }}</p>
                    <p class="mt-1 text-sm text-slate-500">Hasil terbaru dari sesi paling akhir</p>
                </div>
                <div class="results-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Skor Tertinggi</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $summary['best_score'] ?? '-' }}</p>
                    <p class="mt-1 text-sm text-slate-500">Pencapaian terbaik sejauh ini</p>
                </div>
            </div>

            <div class="results-panel overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)]" x-transition:enter="transition-all duration-700 ease-out delay-200" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="results-panel-head flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-6 py-4">
                    <h3 class="text-sm font-bold uppercase tracking-[0.18em] text-slate-500">Riwayat Ujian (5 Terbaru)</h3>
                    <a href="{{ route('student.results.exams') }}" class="inline-flex items-center rounded-lg bg-indigo-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-indigo-700">
                        Lihat Semua
                    </a>
                </div>
                @if($results->isEmpty())
                    <div class="px-6 py-14 text-center">
                        <p class="text-slate-500 italic">Belum ada riwayat ujian.</p>
                    </div>
                @else
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="results-table-head bg-slate-50 text-xs uppercase tracking-[0.18em] text-slate-400">
                                <tr>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Total Skor</th>
                                    <th class="px-6 py-4">Listening</th>
                                    <th class="px-6 py-4">Structure</th>
                                    <th class="px-6 py-4">Reading</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="results-divider divide-y divide-slate-100">
                                @foreach($results as $result)
                                <tr class="results-table-row transition hover:bg-slate-50/70">
                                    <td class="px-6 py-4 text-slate-600">{{ $result->submitted_at?->format('d M Y H:i') ?? $result->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 text-lg font-black text-slate-900">{{ $result->score_total }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $result->correct_listening }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $result->correct_structure }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $result->correct_reading }}</td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('student.results.certificate', $result->id) }}" target="_blank" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                            Cetak Sertifikat
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="grid gap-4 p-4 md:hidden">
                        @foreach($results as $result)
                            <div class="results-mobile-row rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Tanggal</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $result->submitted_at?->format('d M Y H:i') ?? $result->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <span class="results-mobile-mini rounded-full bg-white px-3 py-1 text-sm font-black text-slate-900 shadow-sm">{{ $result->score_total }}</span>
                                </div>

                                <div class="mt-4 grid grid-cols-3 gap-3 text-center text-sm">
                                    <div class="results-mobile-mini rounded-2xl bg-white p-3">
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Listening</p>
                                        <p class="mt-1 font-bold text-slate-900">{{ $result->correct_listening }}</p>
                                    </div>
                                    <div class="results-mobile-mini rounded-2xl bg-white p-3">
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Structure</p>
                                        <p class="mt-1 font-bold text-slate-900">{{ $result->correct_structure }}</p>
                                    </div>
                                    <div class="results-mobile-mini rounded-2xl bg-white p-3">
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Reading</p>
                                        <p class="mt-1 font-bold text-slate-900">{{ $result->correct_reading }}</p>
                                    </div>
                                </div>

                                <a href="{{ route('student.results.certificate', $result->id) }}" target="_blank" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                    Cetak Sertifikat
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="grid gap-4 md:grid-cols-3 pt-2" x-transition:enter="transition-all duration-700 ease-out delay-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="results-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Total Latihan</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $practiceSummary['total_attempts'] }}</p>
                    <p class="mt-1 text-sm text-slate-500">Semua latihan yang sudah disubmit</p>
                </div>
                <div class="results-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Skor Latihan Terakhir</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $practiceSummary['latest_score'] ?? '-' }}</p>
                    <p class="mt-1 text-sm text-slate-500">Hasil terbaru dari latihan terakhir</p>
                </div>
                <div class="results-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Skor Latihan Tertinggi</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $practiceSummary['best_score'] ?? '-' }}</p>
                    <p class="mt-1 text-sm text-slate-500">Pencapaian terbaik latihan</p>
                </div>
            </div>

            <div class="results-panel overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)]" x-transition:enter="transition-all duration-700 ease-out delay-400" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="results-panel-head flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-6 py-4">
                    <h3 class="text-sm font-bold uppercase tracking-[0.18em] text-slate-500">Riwayat Latihan (5 Terbaru)</h3>
                    <a href="{{ route('student.results.practices') }}" class="inline-flex items-center rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                        Lihat Semua
                    </a>
                </div>

                @if($practiceResults->isEmpty())
                    <div class="px-6 py-14 text-center">
                        <p class="text-slate-500 italic">Belum ada riwayat latihan.</p>
                    </div>
                @else
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="results-table-head bg-slate-50 text-xs uppercase tracking-[0.18em] text-slate-400">
                                <tr>
                                    <th class="px-6 py-4">Tanggal</th>
                                    <th class="px-6 py-4">Total Skor</th>
                                    <th class="px-6 py-4">Listening</th>
                                    <th class="px-6 py-4">Structure</th>
                                    <th class="px-6 py-4">Reading</th>
                                </tr>
                            </thead>
                            <tbody class="results-divider divide-y divide-slate-100">
                                @foreach($practiceResults as $result)
                                <tr class="results-table-row transition hover:bg-slate-50/70">
                                    <td class="px-6 py-4 text-slate-600">{{ $result->submitted_at?->format('d M Y H:i') ?? $result->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-6 py-4 text-lg font-black text-slate-900">{{ $result->score_total }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $result->correct_listening }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $result->correct_structure }}</td>
                                    <td class="px-6 py-4 text-slate-700">{{ $result->correct_reading }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="grid gap-4 p-4 md:hidden">
                        @foreach($practiceResults as $result)
                            <div class="results-mobile-row rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Tanggal</p>
                                        <p class="mt-1 text-sm font-semibold text-slate-900">{{ $result->submitted_at?->format('d M Y H:i') ?? $result->created_at->format('d M Y H:i') }}</p>
                                    </div>
                                    <span class="results-mobile-mini rounded-full bg-white px-3 py-1 text-sm font-black text-slate-900 shadow-sm">{{ $result->score_total }}</span>
                                </div>

                                <div class="mt-4 grid grid-cols-3 gap-3 text-center text-sm">
                                    <div class="results-mobile-mini rounded-2xl bg-white p-3">
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Listening</p>
                                        <p class="mt-1 font-bold text-slate-900">{{ $result->correct_listening }}</p>
                                    </div>
                                    <div class="results-mobile-mini rounded-2xl bg-white p-3">
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Structure</p>
                                        <p class="mt-1 font-bold text-slate-900">{{ $result->correct_structure }}</p>
                                    </div>
                                    <div class="results-mobile-mini rounded-2xl bg-white p-3">
                                        <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Reading</p>
                                        <p class="mt-1 font-bold text-slate-900">{{ $result->correct_reading }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            </div>
        </div>
        </div>
    </div>
</x-app-layout>