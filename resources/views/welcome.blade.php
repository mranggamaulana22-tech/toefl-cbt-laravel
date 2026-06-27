<!DOCTYPE html>
<html lang="id" x-data :class="$store.theme?.isDark ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TOEFL CBT - Piksi Ganesha</title>
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- Alpine.js untuk Logika Theme --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: { sora: ['Sora', 'sans-serif'] },
                    colors: {
                        brand: {
                            50: '#f5ecff', 100: '#e8d6ff', 200: '#d0aaff',
                            400: '#a855f7', 600: '#7e22ce', 700: '#6b21a8', 900: '#3b0764',
                        },
                        gold: { 300: '#f6d56f', 400: '#e9c44a', 500: '#d4af37' },
                    },
                },
            },
        };

        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                // DEFAULT THEME: DARK (Jika user belum pernah memilih, otomatis gelap)
                isDark: localStorage.getItem('theme') === null ? true : localStorage.getItem('theme') === 'dark',
                toggle() {
                    this.isDark = !this.isDark;
                    localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                }
            })
        })
    </script>
    <style>
        /* ── Base reset ─────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        /* ── Entrance animation keyframes ── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }
        @keyframes slideRight {
            from { opacity: 0; transform: translateX(-24px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        @keyframes scaleIn {
            from { opacity: 0; transform: scale(0.92); }
            to   { opacity: 1; transform: scale(1); }
        }
        @keyframes shimmer {
            0%   { background-position: -200% center; }
            100% { background-position:  200% center; }
        }
        @keyframes pulse-ring {
            0%   { box-shadow: 0 0 0 0 rgba(168, 85, 247, 0.35); }
            70%  { box-shadow: 0 0 0 12px rgba(168, 85, 247, 0); }
            100% { box-shadow: 0 0 0 0 rgba(168, 85, 247, 0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50%       { transform: translateY(-6px); }
        }
        @keyframes orb1 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33%       { transform: translate(30px, -20px) scale(1.08); }
            66%       { transform: translate(-20px, 15px) scale(0.95); }
        }
        @keyframes orb2 {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50%       { transform: translate(-25px, 20px) scale(1.05); }
        }
        @keyframes navSlide {
            from { opacity: 0; transform: translateY(-16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes borderGlow {
            0%, 100% { border-color: rgba(168,85,247,0.25); }
            50%       { border-color: rgba(168,85,247,0.6); }
        }

        /* ── Utility animation classes ──── */
        .anim-nav       { animation: navSlide 0.55s cubic-bezier(0.22,1,0.36,1) both; }
        .anim-hero-text { animation: fadeUp   0.7s  cubic-bezier(0.22,1,0.36,1) 0.15s both; }
        .anim-hero-sub  { animation: fadeUp   0.7s  cubic-bezier(0.22,1,0.36,1) 0.28s both; }
        .anim-hero-btns { animation: fadeUp   0.7s  cubic-bezier(0.22,1,0.36,1) 0.4s  both; }
        .anim-hero-card { animation: scaleIn  0.7s  cubic-bezier(0.22,1,0.36,1) 0.35s both; }
        .anim-sections  { animation: fadeUp   0.65s cubic-bezier(0.22,1,0.36,1) 0.08s both; }
        .anim-badge     { animation: slideRight 0.6s cubic-bezier(0.22,1,0.36,1) 0.05s both; }
        .anim-feat-1    { animation: fadeUp   0.6s  cubic-bezier(0.22,1,0.36,1) 0.55s both; }
        .anim-feat-2    { animation: fadeUp   0.6s  cubic-bezier(0.22,1,0.36,1) 0.65s both; }
        .anim-feat-3    { animation: fadeUp   0.6s  cubic-bezier(0.22,1,0.36,1) 0.75s both; }
        .anim-feat-4    { animation: fadeUp   0.6s  cubic-bezier(0.22,1,0.36,1) 0.85s both; }

        /* ── Orb decorative blobs ────────── */
        .orb-1 { animation: orb1 9s ease-in-out infinite; }
        .orb-2 { animation: orb2 12s ease-in-out infinite; }

        /* ── Floating image ─────────────── */
        .float-img { animation: float 4s ease-in-out infinite; }

        /* ── Shimmer gold badge ─────────── */
        .shimmer-badge {
            background: linear-gradient(90deg, #f6d56f 0%, #fffde4 40%, #f6d56f 60%, #d4af37 100%);
            background-size: 200% auto;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: shimmer 3s linear infinite;
        }

        /* ── Hover lift cards ────────────── */
        .hover-lift {
            transition: transform 0.25s cubic-bezier(0.22,1,0.36,1),
                        box-shadow 0.25s cubic-bezier(0.22,1,0.36,1),
                        border-color 0.25s ease;
        }
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px -8px rgba(107,33,168,0.18);
            border-color: rgba(168,85,247,0.35);
        }

        /* ── Button hover states ─────────── */
        .btn-gold {
            transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
        }
        .btn-gold:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(212,175,55,0.5);
        }
        .btn-gold:active { transform: translateY(0); }

        .btn-ghost {
            transition: transform 0.2s ease, background-color 0.2s ease;
        }
        .btn-ghost:hover { transform: translateY(-2px); }

        /* ── Nav link hover ──────────────── */
        .nav-btn {
            transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
        }
        .nav-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px -4px rgba(107,33,168,0.3);
        }

        /* ── Section card pulse border ────── */
        .section-pill {
            animation: borderGlow 3s ease-in-out infinite;
        }

        /* ── Step items stagger on load ───── */
        .step-item { opacity: 0; }
        .step-item.visible {
            animation: slideRight 0.5s cubic-bezier(0.22,1,0.36,1) both;
        }

        /* ── Progress bar accent ─────────── */
        .progress-bar {
            position: fixed; top: 0; left: 0; height: 3px;
            background: linear-gradient(90deg, #7e22ce, #a855f7, #f6d56f);
            z-index: 100;
            transform-origin: left;
            transform: scaleX(0);
            transition: transform 0.1s linear;
        }

        /* ── Gradient text ───────────────── */
        .grad-text {
            background: linear-gradient(135deg, #ffffff 30%, #e8d6ff 70%, #f6d56f);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* ── Icon ring pulse ──────────────── */
        .icon-ring { animation: pulse-ring 2.8s cubic-bezier(0.455,0.03,0.515,0.955) infinite; }

        /* ── Backdrop blur support check ──── */
        @supports (backdrop-filter: blur(8px)) {
            .glass { backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }
        }
    </style>
</head>
<body class="bg-slate-100 font-sora text-slate-900 overflow-x-hidden transition-colors duration-500 dark:bg-[#0b0d13] dark:text-slate-100">

    {{-- Loading progress bar --}}
    <div class="progress-bar" id="progressBar"></div>

    <div class="min-h-screen transition-all duration-700" 
         :style="$store.theme?.isDark 
            ? 'background: radial-gradient(ellipse 80% 60% at 70% 0%, #11001f 0%, #0b0d13 40%, #0f172a 70%, #0b0d13 100%);' 
            : 'background: radial-gradient(ellipse 80% 60% at 70% 0%, #ebe0ff 0%, #f9f5ff 40%, #f0eaff 70%, #ede9ff 100%);'">

        {{-- ── NAVBAR ────────────────────────────────────────────── --}}
        <nav class="mx-auto w-full max-w-7xl px-4 pt-5 sm:px-6 lg:px-8 anim-nav">
            <div class="flex items-center justify-between rounded-2xl border px-4 py-3 shadow-lg glass transition-all"
                 :class="$store.theme?.isDark ? 'bg-slate-900/80 border-white/10 shadow-black/40' : 'bg-white/85 border-brand-100/80 shadow-brand-100/40'">
                
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <img src="{{ asset('images/logo.png') }}"
                             alt="Logo Politeknik Piksi Ganesha"
                             width="44" height="44"
                             loading="lazy"
                             class="h-11 w-11 rounded-full border-2 border-brand-200 object-cover icon-ring">
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-[0.3em]" :class="$store.theme?.isDark ? 'text-brand-400' : 'text-brand-600'">TOEFL CBT</p>
                        <p class="text-sm font-bold text-slate-900 sm:text-base leading-tight" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">Politeknik Piksi Ganesha</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 sm:gap-4">
                    {{-- TOMBOL TOGGLE THEME --}}
                    <button @click="$store.theme.toggle()" 
                            class="relative flex h-10 w-10 items-center justify-center rounded-xl transition-all active:scale-90"
                            :class="$store.theme?.isDark ? 'bg-white/10 text-gold-300' : 'bg-brand-50 text-brand-700'">
                        <i x-show="!$store.theme.isDark" class="fas fa-moon text-lg"></i>
                        <i x-show="$store.theme.isDark" class="fas fa-sun text-lg" x-cloak></i>
                    </button>

                    @if (Route::has('login'))
                        <div class="flex items-center gap-2 sm:gap-3">
                            @auth
                                <a href="{{ url('/dashboard') }}" class="nav-btn rounded-full bg-brand-700 px-4 py-2 text-sm font-semibold text-white hover:bg-brand-900 shadow-lg shadow-brand-700/20 active:scale-95 transition-all">
                                    Dashboard
                                </a>
                            @else
                                {{-- PERBAIKAN TOMBOL LOGIN PREMIUM --}}
                                <a href="{{ route('login') }}" 
                                   class="px-5 py-2 text-sm font-bold rounded-full transition-all active:scale-95 border"
                                   :class="$store.theme?.isDark ? 'border-white/10 text-slate-300 hover:bg-white/5 hover:border-white/20' : 'border-brand-200 text-brand-700 hover:bg-brand-50'">
                                    Login
                                </a>
                                <a href="{{ route('register') }}" class="nav-btn rounded-full bg-brand-700 px-5 py-2 text-sm font-bold text-white hover:bg-brand-900 shadow-lg shadow-brand-700/20 active:scale-95 transition-all">
                                    Daftar
                                </a>
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </nav>

        {{-- ── MAIN ────────────────────────────────────────────────── --}}
        <main class="mx-auto w-full max-w-7xl px-4 pb-14 pt-8 sm:px-6 lg:px-8 lg:pt-10">

            {{-- ── HERO GRID ─────────────────────────────────────── --}}
            <section class="grid gap-6 lg:grid-cols-5 lg:gap-8">

                {{-- Hero card kiri - UNGU LEBIH GELAP SAAT DARK MODE --}}
                <div class="anim-hero-text relative overflow-hidden rounded-3xl border border-white/30 p-7 text-white shadow-2xl lg:col-span-3 lg:p-10 transition-all duration-1000"
                     :style="$store.theme?.isDark 
                        ? 'background: linear-gradient(135deg, #11001f 0%, #1e0332 45%, #2e1065 100%);' 
                        : 'background: linear-gradient(135deg, #3b0764 0%, #6b21a8 45%, #7e22ce 100%);'">

                    {{-- Orb decorations --}}
                    <div class="orb-1 absolute -right-14 -top-14 h-56 w-56 rounded-full opacity-20"
                         style="background: radial-gradient(circle, #c084fc, transparent 70%);"></div>
                    <div class="orb-2 absolute -bottom-20 -left-10 h-60 w-60 rounded-full opacity-15"
                         style="background: radial-gradient(circle, #f6d56f, transparent 70%);"></div>
                    <div class="absolute inset-0 opacity-5"
                         style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>

                    {{-- Badge --}}
                    <div class="anim-badge relative mb-5 inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5">
                        <span class="h-1.5 w-1.5 rounded-full bg-gold-300 shadow-[0_0_6px_2px_rgba(246,213,111,0.6)]"></span>
                        <span class="text-xs font-semibold uppercase tracking-[0.25em]">
                            <span class="shimmer-badge">Platform Tes Kampus</span>
                        </span>
                    </div>

                    {{-- Heading --}}
                    <h1 class="relative text-3xl font-extrabold leading-tight sm:text-4xl lg:text-[2.7rem] lg:leading-[1.18]">
                        <span class="grad-text">Uji Kompetensi TOEFL</span><br>
                        <span class="text-white">Lebih Tertib, Lebih Terukur</span>
                    </h1>

                    {{-- Sub --}}
                    <p class="anim-hero-sub relative mt-5 max-w-xl text-sm leading-relaxed text-violet-200 sm:text-[0.95rem]">
                        Sistem Computer Based Test untuk mahasiswa Politeknik Piksi Ganesha — dengan kontrol sesi admin, timer otomatis, dan evaluasi hasil terintegrasi.
                    </p>

                    {{-- CTA Buttons --}}
                    <div class="anim-hero-btns relative mt-8 flex flex-wrap gap-3">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                   class="btn-gold inline-flex items-center gap-2 rounded-full bg-gold-300 px-6 py-3 text-sm font-bold text-brand-900">
                                    Masuk Dashboard <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                            @else
                                <a href="{{ route('register') }}"
                                   class="btn-gold inline-flex items-center gap-2 rounded-full bg-gold-300 px-6 py-3 text-sm font-bold text-brand-900 shadow-lg shadow-gold-400/20">
                                    Mulai Sekarang <i class="fas fa-arrow-right text-xs"></i>
                                </a>
                                <a href="{{ route('login') }}"
                                   class="btn-ghost inline-flex items-center gap-2 rounded-full border border-white/35 bg-white/10 px-6 py-3 text-sm font-bold text-white hover:bg-white/20">
                                    Sudah Punya Akun
                                </a>
                            @endauth
                        @endif
                    </div>

                    {{-- Section pills --}}
                    <div class="relative mt-9 grid gap-3 sm:grid-cols-3" id="sectionPills">
                        @foreach([
                            ['icon' => 'fa-headphones', 'label' => 'Listening'],
                            ['icon' => 'fa-spell-check', 'label' => 'Structure'],
                            ['icon' => 'fa-book-open', 'label' => 'Reading'],
                        ] as $i => $sec)
                        <div class="section-pill group flex items-center gap-3 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 transition-colors hover:bg-white/20 cursor-default"
                             style="animation-delay: {{ 0.5 + $i * 0.12 }}s">
                            <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-white/15 text-gold-300">
                                <i class="fas {{ $sec['icon'] }} text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-[0.2em] text-violet-200">Section</p>
                                <p class="text-sm font-bold leading-tight">{{ $sec['label'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Info card kanan --}}
                <div class="anim-hero-card overflow-hidden rounded-3xl border transition-all lg:col-span-2 shadow-xl"
                     :class="$store.theme?.isDark ? 'bg-slate-900 border-white/10 shadow-black/50' : 'bg-white border-brand-100 shadow-brand-100/30'">

                    {{-- Gedung photo with float --}}
                    <div class="relative h-56 overflow-hidden border-b sm:h-64 lg:h-56" :class="$store.theme?.isDark ? 'border-white/5' : 'border-slate-100'">
                        <img src="{{ asset('images/gedung.jpg') }}"
                             alt="Gedung Politeknik Piksi Ganesha"
                             width="1200" height="800"
                             loading="lazy"
                             class="float-img h-full w-full object-cover scale-105">
                        <div class="absolute inset-0"
                             style="background: linear-gradient(to top, rgba(59,7,100,0.65) 0%, transparent 55%);"></div>
                        <div class="absolute bottom-4 left-5">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-white/20 px-3 py-1 text-xs font-semibold text-white backdrop-blur-sm border border-white/25">
                                <span class="h-1.5 w-1.5 rounded-full bg-green-400 shadow-[0_0_5px_2px_rgba(74,222,128,0.5)]"></span>
                                Sistem online
                            </span>
                        </div>
                    </div>

                    {{-- Alur tes --}}
                    <div class="p-6">
                        <h2 class="text-lg font-extrabold text-slate-900" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">Alur Tes Singkat</h2>
                        <div class="mt-4 space-y-2.5" id="stepList">
                            @foreach([
                                'Admin membuka sesi ujian agar mahasiswa bisa mulai mengerjakan.',
                                'Mahasiswa mengerjakan soal dengan timer dan navigasi nomor soal.',
                                'Hasil tersimpan otomatis setelah submit dan dapat dipantau admin.',
                            ] as $idx => $step)
                            <div class="step-item hover-lift flex items-start gap-3 rounded-xl p-3 border transition-all"
                                 :class="$store.theme?.isDark ? 'bg-white/5 border-white/5' : 'bg-slate-50 border-slate-100'">
                                <span class="mt-0.5 inline-flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-bold text-brand-700">
                                    {{ $idx + 1 }}
                                </span>
                                <p class="text-sm text-slate-600 leading-relaxed" :class="$store.theme?.isDark ? 'text-slate-400' : 'text-slate-600'">{{ $step }}</p>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- ── FEATURE CARDS ────────────────────────────────── --}}
            <section class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach([
                    ['icon' => 'fa-user-lock',  'title' => 'Akses Terkontrol', 'desc' => 'Mahasiswa hanya dapat mengerjakan saat sesi dibuka admin.', 'anim' => 'anim-feat-1'],
                    ['icon' => 'fa-stopwatch',   'title' => 'Timer Real-Time',  'desc' => 'Waktu ujian berjalan otomatis dengan tampilan yang fokus.',  'anim' => 'anim-feat-2'],
                    ['icon' => 'fa-list-check',  'title' => 'Navigasi Cepat',   'desc' => 'Nomor soal membantu perpindahan dengan status jawab jelas.',  'anim' => 'anim-feat-3'],
                    ['icon' => 'fa-chart-line',  'title' => 'Laporan Terpusat', 'desc' => 'Riwayat skor mahasiswa tersedia untuk evaluasi dan rekap.',  'anim' => 'anim-feat-4'],
                ] as $feat)
                <article class="{{ $feat['anim'] }} hover-lift rounded-2xl border p-5 shadow-sm transition-all cursor-default"
                         :class="$store.theme?.isDark ? 'bg-slate-900 border-white/5 shadow-black/40' : 'bg-white border-slate-200 shadow-sm'">
                    <div class="mb-3 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-700 transition-transform group-hover:scale-110">
                        <i class="fas {{ $feat['icon'] }}"></i>
                    </div>
                    <h3 class="text-sm font-bold uppercase tracking-[0.12em]" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $feat['title'] }}</h3>
                    <p class="mt-2 text-sm text-slate-500 leading-relaxed">{{ $feat['desc'] }}</p>
                </article>
                @endforeach
            </section>
        </main>

        {{-- ── FOOTER ──────────────────────────────────────────── --}}
        <footer class="border-t px-4 py-6 text-center text-xs font-medium glass transition-all"
                :class="$store.theme?.isDark ? 'border-white/5 bg-slate-900/60 text-slate-500' : 'border-brand-100/60 bg-white/60 text-slate-400'">
            TOEFL CBT &nbsp;·&nbsp; Politeknik Piksi Ganesha Indonesia
        </footer>
    </div>

    {{-- ── SCRIPTS ─────────────────────────────────────────────── --}}
    <script>
        /* Loading progress bar */
        const bar = document.getElementById('progressBar');
        let prog = 0;
        const progInterval = setInterval(() => {
            prog = Math.min(prog + Math.random() * 18, 90);
            bar.style.transform = `scaleX(${prog / 100})`;
        }, 120);
        window.addEventListener('load', () => {
            clearInterval(progInterval);
            bar.style.transform = 'scaleX(1)';
            setTimeout(() => { bar.style.opacity = '0'; bar.style.transition = 'opacity 0.4s'; }, 300);
        });

        /* Step items staggered entrance via IntersectionObserver */
        const steps = document.querySelectorAll('.step-item');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const idx = Array.from(steps).indexOf(el);
                    el.style.animationDelay = `${0.45 + idx * 0.13}s`;
                    el.classList.add('visible');
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.2 });
        steps.forEach(s => observer.observe(s));

        /* Subtle parallax on hero bg orbs on mouse move */
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth  - 0.5) * 18;
            const y = (e.clientY / window.innerHeight - 0.5) * 12;
            document.querySelectorAll('.orb-1, .orb-2').forEach((orb, i) => {
                const factor = i === 0 ? 1 : -0.6;
                orb.style.transform = `translate(${x * factor}px, ${y * factor}px)`;
            });
        });

        /* Respect reduced motion */
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            document.querySelectorAll('[class*="anim-"], .float-img, .orb-1, .orb-2, .shimmer-badge, .icon-ring').forEach(el => {
                el.style.animation = 'none';
                el.style.opacity   = '1';
                el.style.transform = 'none';
            });
        }
    </script>
</body>
</html>