<x-app-layout>
    @include('student.partials.shared-utils-styles')
    <style>
        /* ─── Page-in animation ─────────────────────────────────────── */
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* ─── Page-out transition ───────────────────────────────────── */
        @keyframes fadeOutDown {
            from { opacity: 1; transform: translateY(0); }
            to   { opacity: 0; transform: translateY(20px); }
        }

        .page-enter {
            animation: fadeIn 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
        }
        .page-exit {
            animation: fadeOutDown 0.35s cubic-bezier(0.4, 0, 1, 1) both;
            pointer-events: none;
        }

        /* Staggered children */
        .anim-chip  { animation: fadeInUp 0.50s 0.05s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .anim-title { animation: fadeInUp 0.55s 0.12s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .anim-desc  { animation: fadeInUp 0.55s 0.18s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .anim-grid  { animation: fadeInUp 0.55s 0.24s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .anim-rules { animation: fadeInUp 0.55s 0.30s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .anim-card  { animation: scaleIn 0.60s 0.20s cubic-bezier(0.22, 1, 0.36, 1) both; }
        .anim-alert { animation: fadeInUp 0.45s 0.38s cubic-bezier(0.22, 1, 0.36, 1) both; }

        /* Ambient glow pulse */
        @keyframes glowPulse {
            0%, 100% { opacity: 0.10; }
            50%       { opacity: 0.18; }
        }
        .glow-ambient { animation: glowPulse 6s ease-in-out infinite; }

        /* Button micro-interaction */
        .btn-start {
            transition: background-color 0.2s ease, transform 0.18s ease, box-shadow 0.2s ease;
        }
        .btn-start:hover:not(.disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(30, 64, 175, 0.30);
        }
        .btn-start:active:not(.disabled) { transform: translateY(0); }
    </style>

    <div
        id="page-root"
        class="min-h-screen text-white font-sans selection:bg-purple-500/30 overflow-hidden page-enter"
        {{-- TAMBAHKAN TRIGGER BACKGROUND DI SINI --}}
        x-data="{ loaded: false, init() { $store.bg.enabled = true; setTimeout(() => { this.loaded = true }, 350) } }"
    >
        <!-- BACKGROUND -->
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
        <div class="glow-ambient fixed top-0 right-0 w-[500px] h-[500px] bg-indigo-600/10 blur-[120px] rounded-full pointer-events-none z-0"></div>
        <div class="glow-ambient fixed bottom-0 left-0 w-[300px] h-[300px] bg-purple-600/10 blur-[100px] rounded-full pointer-events-none z-0" style="animation-delay:3s"></div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-10 md:pt-16 lg:pt-20">

            {{-- ── Skeleton ──────────────────────────────────────────── --}}
            @include('student.practice.partials.start-skeleton')

            {{-- ── Main card ─────────────────────────────────────────── --}}
            <div x-cloak x-show="loaded" class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-[0_18px_50px_rgba(15,23,42,0.08)]">
                <div class="grid gap-6 p-6 md:p-8 lg:grid-cols-[1.4fr_0.9fr] lg:items-start">

                    {{-- Left column --}}
                    <div>
                        <span class="anim-chip inline-flex items-center rounded-full border border-indigo-100 bg-indigo-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-700">
                            Persiapan CBT
                        </span>
                        <h3 class="anim-title mt-4 text-3xl font-black tracking-tight text-slate-900">
                            Instruksi latihan yang ringkas dan jelas.
                        </h3>
                        <p class="anim-desc mt-3 text-sm leading-7 text-slate-600">
                            Halaman ini membantu kamu masuk ke mode latihan kapan saja tanpa menunggu admin membuka sesi ujian.
                        </p>

                        <div class="anim-grid mt-6 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition-shadow duration-200 hover:shadow-md">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Waktu Ujian</p>
                                <p class="mt-1 text-base font-bold text-slate-900">120 menit</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 transition-shadow duration-200 hover:shadow-md">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Bagian</p>
                                <p class="mt-1 text-base font-bold text-slate-900">Listening, Structure, Reading</p>
                            </div>
                        </div>

                        <div class="anim-rules mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-600">
                            <p class="font-semibold text-slate-900">Sebelum mulai:</p>
                            <ul class="mt-3 space-y-2">
                                <li>Pastikan koneksi stabil dan headset siap untuk Listening.</li>
                                <li>Jangan refresh halaman atau menekan tombol back saat tes berlangsung.</li>
                                <li>Fokus pada navigasi soal, karena jawaban tersimpan otomatis selama tes.</li>
                            </ul>
                        </div>
                    </div>

                    {{-- Right column – status card --}}
                    <div class="anim-card rounded-3xl border border-indigo-300 bg-gradient-to-br from-indigo-700 via-indigo-600 to-sky-600 p-6 text-white shadow-[0_16px_40px_rgba(30,64,175,0.30)] lg:mt-10">
                        <p class="text-xs font-bold uppercase tracking-[0.22em] text-violet-100">Status Latihan</p>
                        <h4 class="mt-2 text-2xl font-black text-white">Siap Dikerjakan</h4>

                        <div class="mt-5 rounded-2xl border border-white/20 bg-white/10 px-4 py-3">
                            <p class="text-sm font-semibold text-violet-100">
                                Total soal latihan tersedia: {{ $questionCount }}
                            </p>
                        </div>

                        <div class="mt-6 space-y-3 text-sm text-violet-100/90">
                            <p><span class="font-semibold text-white">Akses:</span> latihan dapat dibuka kapan saja.</p>
                            <p><span class="font-semibold text-white">Submit:</span> skor latihan dihitung otomatis setelah dikirim.</p>
                            <p><span class="font-semibold text-white">Tips:</span> gunakan mode fullscreen agar lebih fokus.</p>
                        </div>

                        <div class="mt-6 flex flex-col gap-3">
                            @if($questionCount >= 1)
                                <a
                                    href="{{ route('practice.test') }}"
                                    data-transition
                                    class="btn-start inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-indigo-700 shadow-sm"
                                >
                                    Saya Siap, Mulai Latihan
                                </a>
                            @else
                                <button
                                    type="button"
                                    disabled
                                    class="btn-start disabled inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-bold text-indigo-700 shadow-sm pointer-events-none opacity-50"
                                >
                                    Saya Siap, Mulai Latihan
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Flash messages --}}
                @if (session('error'))
                    <div class="anim-alert mx-6 mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 md:mx-8">
                        {{ session('error') }}
                    </div>
                @endif

                @if (session('success'))
                    <div class="anim-alert mx-6 mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 md:mx-8">
                        {{ session('success') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Page transition script ─────────────────────────────────── --}}
    <script>
    (function () {
        const root = document.getElementById('page-root');

        function triggerExit(href, delay = 320) {
            if (!root) return;
            root.classList.remove('page-enter');
            root.classList.add('page-exit');
            setTimeout(() => { window.location.href = href; }, delay);
        }

        document.addEventListener('click', function (e) {
            const link = e.target.closest('a[data-transition]');
            if (!link) return;

            const href = link.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript')) return;
            if (link.target === '_blank') return;

            e.preventDefault();
            triggerExit(href);
        });

        window.addEventListener('pageshow', function (ev) {
            if (ev.persisted && root) {
                root.classList.remove('page-exit');
                root.classList.add('page-enter');
            }
        });
    })();
    </script>
</x-app-layout>