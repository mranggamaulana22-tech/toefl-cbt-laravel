<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data :class="$store.theme?.isDark ? 'dark' : ''">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.store('theme', {
                    isDark: localStorage.getItem('theme') === null ? true : localStorage.getItem('theme') === 'dark',
                    toggle() {
                        this.isDark = !this.isDark;
                        localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
                    }
                })
            })
        </script>

        <style>
            .modern-fade-in {
                opacity: 0;
                transform: translateY(40px) scale(0.97);
                filter: blur(6px);
                animation: modernFadeIn 0.85s cubic-bezier(.22,1,.36,1) 0.05s both;
            }
            @keyframes modernFadeIn {
                0% { opacity: 0; transform: translateY(40px) scale(0.97); filter: blur(6px); }
                60% { opacity: 1; filter: blur(0.5px); }
                100% { opacity: 1; transform: translateY(0) scale(1); filter: blur(0); }
            }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased transition-colors duration-500" :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-slate-50'">
        
        <div class="relative min-h-screen transition-all duration-700">
            
            {{-- LAPISAN BACKGROUND: Dibuat jernih agar gedung terlihat jelas --}}
            <div class="absolute inset-0 z-0">
                {{-- Gambar Gedung --}}
                <img src="{{ asset('images/gedung.jpg') }}" alt="Background" class="h-full w-full object-cover transition-opacity duration-700"
                     width="1200" height="800"
                     loading="lazy"
                     :class="$store.theme?.isDark ? 'opacity-40' : 'opacity-70'">
                
                {{-- Overlay: Dibuat sangat tipis agar tidak menghalangi gambar gedung --}}
                <div class="absolute inset-0 transition-colors duration-700" 
                     :class="$store.theme?.isDark ? 'bg-black/20' : 'bg-white/10'"></div>

                {{-- Efek Gradient Halus di Background --}}
                <div class="absolute inset-0"
                     :class="$store.theme?.isDark 
                        ? 'bg-[radial-gradient(circle_at_50%_50%,rgba(88,28,135,0.15),transparent_70%)]' 
                        : 'bg-gradient-to-br from-white/20 via-transparent to-indigo-100/20'"></div>
            </div>

            {{-- LAPISAN KONTEN --}}
            <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-7xl items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="w-full max-w-md modern-fade-in">
                    
                    {{-- Logo --}}
                    <div class="mb-6 flex justify-center text-center">
                        <a href="/" class="inline-flex flex-col items-center gap-2 px-6 py-4 rounded-3xl transition-all hover:scale-105"
                           :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">
                            <x-application-logo class="h-12 w-12 mb-1" />
                            <div>
                                <span class="block text-[10px] font-black uppercase tracking-[0.3em]" :class="$store.theme?.isDark ? 'text-indigo-400' : 'text-indigo-500'">TOEFL PIKSI</span>
                                <span class="block text-lg font-bold">{{ config('app.name') }}</span>
                            </div>
                        </a>
                    </div>

                    {{-- KOTAK FORM (CARD): SOLID / TIDAK TRANSPARAN --}}
                    <div class="overflow-hidden rounded-[2.5rem] border transition-all duration-700 sm:p-10 p-7 shadow-2xl"
                         :class="$store.theme?.isDark 
                            ? 'border-white/10 bg-[#1a1033] shadow-black/60' 
                            : 'border-violet-100 bg-white shadow-[0_24px_70px_rgba(0,0,0,0.15)]'">
                        
                        <div :class="$store.theme?.isDark ? 'text-slate-200' : 'text-slate-700'">
                            {{ $slot }}
                        </div>

                        {{-- Toggle Theme --}}
                        <div class="mt-10 flex justify-center border-t pt-6" :class="$store.theme?.isDark ? 'border-white/5' : 'border-black/5'">
                            <button @click="$store.theme.toggle()" 
                                    class="flex items-center gap-3 px-4 py-2 rounded-full text-[11px] font-bold uppercase tracking-widest transition-all active:scale-95"
                                    :class="$store.theme?.isDark ? 'bg-white/5 text-slate-400 hover:text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
                                <i :class="$store.theme?.isDark ? 'fas fa-sun text-amber-400' : 'fas fa-moon text-indigo-600'"></i>
                                Mode <span x-text="$store.theme?.isDark ? 'Terang' : 'Gelap'"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Logout --}}
        <x-modal name="confirm-logout" maxWidth="md">
            <div class="p-6" :class="$store.theme?.isDark ? 'bg-[#1a1033]' : 'bg-white'">
                <h2 class="text-lg font-bold" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">Konfirmasi Logout</h2>
                <p class="mt-3 text-sm leading-relaxed" :class="$store.theme?.isDark ? 'text-slate-400' : 'text-slate-600'">
                    Anda akan keluar dari sistem TOEFL CBT.
                </p>
                <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button type="button" x-on:click="$dispatch('close-modal', 'confirm-logout')" 
                            class="inline-flex items-center justify-center rounded-2xl border px-6 py-2.5 text-sm font-bold transition active:scale-95"
                            :class="$store.theme?.isDark ? 'border-white/10 text-slate-300' : 'border-slate-200 bg-white text-slate-700'">
                        Batal
                    </button>
                    <form method="POST" action="{{ route('logout') }}" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-2xl bg-indigo-600 px-6 py-2.5 text-sm font-bold text-white transition hover:bg-indigo-700 shadow-lg active:scale-95">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </x-modal>
    </body>
</html>