<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">Riwayat Ujian Lengkap</h2>
                <p class="mt-1 text-sm text-slate-500">Semua hasil ujian yang sudah pernah kamu submit.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('student.ai.index') }}" class="inline-flex items-center rounded-xl bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700">
                    Halaman AI Analisis
                </a>
                <a href="{{ route('student.results.index') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700">
                    Kembali ke Ringkasan
                </a>
            </div>
        </div>
    </x-slot>

    {{-- Elemen Background Image dihapus agar tidak merusak layout --}}

    <div class="exam-history-page py-8 bg-slate-50 min-h-screen" x-data="{ loaded: false, init() { setTimeout(() => { this.loaded = true }, 350) } }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- SKELETON LOADING --}}
            @include('student.results.partials.exam-history-skeleton')

            {{-- MAIN CONTENT --}}
            <div x-cloak x-show="loaded" class="space-y-6">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="history-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Total Percobaan</p>
                        <p class="mt-3 text-3xl font-black text-slate-900">{{ $summary['total_attempts'] }}</p>
                        <p class="mt-1 text-sm text-slate-500">Semua ujian yang sudah disubmit</p>
                    </div>
                    <div class="history-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Skor Terakhir</p>
                        <p class="mt-3 text-3xl font-black text-slate-900">{{ $summary['latest_score'] ?? '-' }}</p>
                        <p class="mt-1 text-sm text-slate-500">Hasil terbaru dari sesi paling akhir</p>
                    </div>
                    <div class="history-card rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Skor Tertinggi</p>
                        <p class="mt-3 text-3xl font-black text-slate-900">{{ $summary['best_score'] ?? '-' }}</p>
                        <p class="mt-1 text-sm text-slate-500">Pencapaian terbaik ujian</p>
                    </div>
                </div>

                <div class="history-panel overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    @if($results->isEmpty())
                        <div class="px-6 py-14 text-center">
                            <p class="text-slate-500 italic">Belum ada riwayat ujian.</p>
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
                                        <th class="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="history-divider divide-y divide-slate-100">
                                    @foreach($results as $result)
                                    <tr class="history-table-row transition hover:bg-slate-50/70">
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

                                    <a href="{{ route('student.results.certificate', $result->id) }}" target="_blank" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                        Cetak Sertifikat
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-slate-100 px-6 py-5">
                            {{ $results->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>