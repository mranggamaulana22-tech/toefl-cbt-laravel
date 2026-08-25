<!DOCTYPE html>
{{-- 1. Tambahkan binding class dark agar dark: mode Tailwind aktif --}}
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data :class="{ 'dark': $store.theme?.isDark }">
    @php
        $isStudentShell = auth()->check() && auth()->user()->role === 'student';
        $isAdminShell = auth()->check() && auth()->user()->role === 'admin';
        $isAssessmentInProgress = $isStudentShell && (request()->routeIs('exam.test') || request()->routeIs('practice.test'));
    @endphp
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet">

        {{-- Font Awesome — dipakai di banyak halaman admin (ikon plus, trash, check-circle, dll)
             tapi sebelumnya tidak dimuat di layout ini, hanya ada di welcome.blade.php.
             Akibatnya semua ikon fas fa-* di halaman admin tidak tampil sama sekali. --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        {{-- SweetAlert2 CSS & Animate.css untuk animasi pop-up yang smooth --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

        {{-- Player Lottie untuk file .json (specific version to prevent duplicate registration) --}}
        <script src="https://unpkg.com/@lottiefiles/lottie-player@2.0.2/dist/lottie-player.js" defer></script>
        <script>
            (function() {
                const theme = localStorage.getItem('theme');
                if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            })();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>

    {{-- 2. Warna body sekarang murni CSS (lihat tokens.css) + dark: Tailwind, tidak lagi menunggu Alpine --}}
    <body class="font-sans antialiased transition-colors duration-300 overflow-x-hidden {{ $isStudentShell ? 'student-theme' : '' }} {{ $isAdminShell ? 'admin-theme' : '' }} min-h-screen
                 bg-white text-slate-900
                 dark:bg-[#0b0d13] dark:text-white">

        <script>
            document.addEventListener('alpine:init', () => {
                if (!window.Alpine.store('bg')) {
                    window.Alpine.store('bg', {
                        enabled: localStorage.getItem('bg_enabled') !== 'false',
                        toggle() {
                            this.enabled = !this.enabled;
                            localStorage.setItem('bg_enabled', this.enabled);
                        }
                    });
                }
            });
        </script>

        <div class="min-h-screen">
            @if (! $isAssessmentInProgress)
                @include('layouts.navigation')
            @endif

            <div class="min-h-screen">
                @if (isset($header))
                    {{-- 3. Header sekarang pakai dark: Tailwind, tidak menunggu Alpine load --}}
                    <header class="backdrop-blur-xl border-b shadow-[0_8px_30px_rgba(15,23,42,0.03)]
                                   bg-white/80 border-slate-200/80
                                   dark:bg-[#111827]/80 dark:border-white/5">
                        <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main>
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{-- 4. Modal Logout — dark: Tailwind, hanya render saat modal dibuka jadi risiko flash kecil,
             tapi tetap dikonsistenkan --}}
        <x-modal name="confirm-logout" maxWidth="md">
            <div class="p-6 transition-colors bg-white dark:bg-[#111827]">
                <h2 class="text-lg font-semibold text-slate-900 dark:text-white">Konfirmasi Logout</h2>
                <p class="mt-2 text-sm leading-6 text-slate-600 dark:text-slate-400">
                    Anda akan keluar dari akun ini. Lanjutkan hanya jika memang ingin mengakhiri sesi saat ini.
                </p>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" x-on:click="$dispatch('close-modal', 'confirm-logout')"
                            class="inline-flex items-center justify-center rounded-xl border px-4 py-2 text-sm font-semibold transition
                                   bg-white text-slate-700 border-slate-200 hover:border-slate-300
                                   dark:bg-white/5 dark:text-white dark:border-white/10 dark:hover:bg-white/10">
                        Batal
                    </button>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>

        {{-- SweetAlert2 JS CDN JavaScript Injection --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        @stack('scripts')
    </body>
</html>