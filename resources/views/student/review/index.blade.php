<x-app-layout>
    @include('student.review.partials.review-bg')
    @include('student.review.partials.index-styles')

    <!-- Overlay untuk page transition -->
    <div id="_rip-overlay" class="fixed inset-0 z-[100] bg-gradient-to-r from-violet-600 to-indigo-600 opacity-0 pointer-events-none transition-opacity duration-300"></div>

    @php
        $latestSession = $sessions->first();
    @endphp

    <div class="review-index-page min-h-screen text-white font-sans selection:bg-purple-500/30 overflow-hidden py-8 pt-4 md:pt-8 lg:pt-10" x-data="{ loaded: false, init() { setTimeout(() => { this.loaded = true }, 350) } }">
        <div class="fixed inset-0 z-0">
            <template x-if="$store.bg.enabled">
                <div class="w-full h-full absolute inset-0"
                    :style="$store.theme?.isDark
                        ? 'background-image: url(\'{{ asset("images/classroom_dark.png") }}\'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: scroll; opacity:0.6;'
                        : 'background-image: url(\'{{ asset("images/classroom_light.png") }}\'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: scroll; opacity:1;'"></div>
            </template>
            <div x-show="$store.bg.enabled" class="absolute inset-0 transition-all duration-700"
                :class="$store.theme?.isDark
                    ? 'bg-gradient-to-tr from-[#0b0d13] via-transparent to-transparent'
                    : 'bg-gradient-to-t from-white/70 via-white/10 to-transparent'">
            </div>
            <div x-show="!$store.bg.enabled" :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-white'" class="absolute inset-0 w-full h-full transition-colors duration-500"></div>
        </div>

        <div class="fixed top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 blur-[120px] rounded-full pointer-events-none z-0"></div>
        <div class="fixed bottom-0 left-0 w-[300px] h-[300px] bg-purple-600/10 blur-[100px] rounded-full pointer-events-none z-0"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 relative z-10">
            @include('student.review.partials.index-skeleton')

            <div x-cloak x-show="loaded" class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-semibold text-xl text-slate-800 leading-tight">Review AI Latihan</h3>
                            <p class="mt-1 text-sm text-slate-500">Pilih sesi latihan yang ingin kamu review. Fitur ini hanya untuk latihan, bukan ujian.</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('student.ai.index') }}" class="inline-flex items-center rounded-xl bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700">
                                Ke AI Analisis
                            </a>
                            <a href="{{ route('practice.start') }}" class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-emerald-200 hover:text-emerald-700">
                                Mulai Latihan
                            </a>
                        </div>
                    </div>
                </div>

                <div class="review-index-hero rounded-3xl border border-indigo-100 bg-gradient-to-r from-indigo-700 via-indigo-600 to-sky-600 px-6 py-6 text-white shadow-[0_18px_50px_rgba(30,64,175,0.20)]">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-white/70">Review AI Latihan</p>
                        <h3 class="mt-2 text-3xl font-black tracking-tight sm:text-4xl">Latihan saja</h3>
                        <p class="mt-2 max-w-2xl text-sm text-white/85 sm:text-base">
                            Lihat kembali sesi latihan yang sudah kamu submit, lalu buka review AI untuk cek jawaban, pola kesalahan, dan penjelasan per soal.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-white/90">
                            Total {{ $sessions->total() }} sesi
                        </span>
                        @if($latestSession)
                            <span class="inline-flex items-center rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-[0.18em] text-white/90">
                                Terbaru {{ $latestSession->submitted_at?->format('d M Y') ?? $latestSession->created_at->format('d M Y') }}
                            </span>
                        @endif
                    </div>
                </div>
                </div>

                <div class="grid gap-4 md:grid-cols-3">
                <div class="review-index-stat rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Total Sesi</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $sessions->total() }}</p>
                    <p class="mt-1 text-sm text-slate-500">Seluruh latihan yang bisa direview</p>
                </div>
                <div class="review-index-stat rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Sesi Saat Ini</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">{{ $sessions->count() }}</p>
                    <p class="mt-1 text-sm text-slate-500">Data yang tampil di halaman ini</p>
                </div>
                <div class="review-index-stat rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-slate-400">Mode</p>
                    <p class="mt-3 text-3xl font-black text-slate-900">Latihan</p>
                    <p class="mt-1 text-sm text-slate-500">Review AI hanya tersedia untuk latihan</p>
                </div>
                </div>

                <div class="review-index-table overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                @if($sessions->isEmpty())
                    <div class="px-6 py-16 text-center">
                        <p class="text-slate-500 italic">Belum ada sesi latihan yang bisa direview.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($sessions as $session)
                            <div class="review-index-row flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between bg-white dark:bg-slate-800 transition-colors">
                                <div>
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Sesi Latihan</p>
                                    <h4 class="mt-1 text-lg font-black text-slate-900">{{ $session->submitted_at?->format('d M Y H:i') ?? $session->created_at->format('d M Y H:i') }}</h4>
                                    <p class="mt-1 text-sm text-slate-500">Skor {{ $session->score_total }} | Listening {{ $session->correct_listening }} | Structure {{ $session->correct_structure }} | Reading {{ $session->correct_reading }}</p>
                                </div>

                                <div class="flex flex-wrap gap-3">
                                    <a href="{{ route('student.review.show', $session) }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                        Review AI
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="border-t border-slate-100 px-6 py-5">
                        {{ $sessions->links() }}
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>

    @include('student.review.partials.index-scripts')

</x-app-layout>