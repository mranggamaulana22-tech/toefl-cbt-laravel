<x-app-layout>
    @include('student.partials.shared-utils-styles')
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">Riwayat Latihan Lengkap</h2>
                <p class="mt-1 text-sm text-slate-500">Semua hasil latihan yang sudah pernah kamu submit.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('student.ai.index') }}" class="inline-flex items-center rounded-xl bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700">
                    Halaman AI Analisis
                </a>
                <a href="{{ route('student.results.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700">
                    Kembali ke Ringkasan
                </a>
            </div>
        </div>
    </x-slot>

    <div class="practice-history-page py-8 min-h-screen relative overflow-hidden" x-data="{ loaded: false, init() { setTimeout(() => { this.loaded = true }, 350) } }">
        
        {{-- Background Image dihapus karena halaman ini seharusnya bersih dari background --}}

        <style>

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

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 relative z-10">
            @include('student.results.partials.practice-history-skeleton')

            <div x-cloak x-show="loaded" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="history-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Total Latihan</p>
                        <p class="mt-3 text-3xl font-black text-slate-900">{{ $practiceSummary['total_attempts'] }}</p>
                        <p class="mt-1 text-sm text-slate-500">Semua latihan yang sudah disubmit</p>
                    </div>
                    <div class="history-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Skor Terakhir</p>
                        <p class="mt-3 text-3xl font-black text-slate-900">{{ $practiceSummary['latest_score'] ?? '-' }}</p>
                        <p class="mt-1 text-sm text-slate-500">Hasil terbaru dari latihan terakhir</p>
                    </div>
                    <div class="history-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Skor Tertinggi</p>
                        <p class="mt-3 text-3xl font-black text-slate-900">{{ $practiceSummary['best_score'] ?? '-' }}</p>
                        <p class="mt-1 text-sm text-slate-500">Pencapaian terbaik latihan</p>
                    </div>
                </div>

                <div class="history-panel overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    @if($practiceResults->isEmpty())
                        <div class="px-6 py-14 text-center">
                            <p class="text-slate-500 italic">Belum ada riwayat latihan.</p>
                        </div>
                    @else
                        <div class="hidden md:block overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="history-table-head bg-slate-50 text-xs uppercase tracking-[0.18em] text-slate-400">
                                    <tr>
                                        <th class="px-6 py-4">Tanggal</th>
                                        <th class="px-6 py-4">Total Skor</th>
                                        <th class="px-6 py-4">Listening</th>
                                        <th class="px-6 py-4">Structure</th>
                                        <th class="px-6 py-4">Reading</th>
                                        <th class="px-6 py-4"></th>
                                    </tr>
                                </thead>
                                <tbody class="history-divider divide-y divide-slate-100">
                                    @foreach($practiceResults as $result)
                                    <tr class="history-table-row transition hover:bg-slate-50/70">
                                        <td class="px-6 py-4 text-slate-600">{{ $result->submitted_at?->format('d M Y H:i') ?? $result->created_at->format('d M Y H:i') }}</td>
                                        <td class="px-6 py-4 text-lg font-black text-slate-900">{{ $result->score_total }}</td>
                                        <td class="px-6 py-4 text-slate-700">{{ $result->correct_listening }}</td>
                                        <td class="px-6 py-4 text-slate-700">{{ $result->correct_structure }}</td>
                                        <td class="px-6 py-4 text-slate-700">{{ $result->correct_reading }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('student.review.show', $result) }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-indigo-700">
                                                Review AI
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="grid gap-4 p-4 md:hidden">
                            @foreach($practiceResults as $result)
                                <div class="history-mobile-row rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Tanggal</p>
                                            <p class="mt-1 text-sm font-semibold text-slate-900">{{ $result->submitted_at?->format('d M Y H:i') ?? $result->created_at->format('d M Y H:i') }}</p>
                                        </div>
                                        <span class="history-mobile-mini rounded-full bg-white px-3 py-1 text-sm font-black text-slate-900 shadow-sm">{{ $result->score_total }}</span>
                                    </div>

                                    <div class="mt-4 grid grid-cols-3 gap-3 text-center text-sm">
                                        <div class="history-mobile-mini rounded-2xl bg-white p-3">
                                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Listening</p>
                                            <p class="mt-1 font-bold text-slate-900">{{ $result->correct_listening }}</p>
                                        </div>
                                        <div class="history-mobile-mini rounded-2xl bg-white p-3">
                                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Structure</p>
                                            <p class="mt-1 font-bold text-slate-900">{{ $result->correct_structure }}</p>
                                        </div>
                                        <div class="history-mobile-mini rounded-2xl bg-white p-3">
                                            <p class="text-[11px] uppercase tracking-[0.18em] text-slate-400">Reading</p>
                                            <p class="mt-1 font-bold text-slate-900">{{ $result->correct_reading }}</p>
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <a href="{{ route('student.review.show', $result) }}" class="inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-xs font-bold uppercase tracking-[0.14em] text-white transition hover:bg-indigo-700">
                                            Review AI
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-slate-100 px-6 py-5">
                            {{ $practiceResults->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>