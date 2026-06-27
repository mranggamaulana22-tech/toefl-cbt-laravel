<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-0.5 animate-fade-in-down">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Admin Control Center</p>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ __('Admin Dashboard') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola ujian, latihan, mahasiswa, dan riwayat dari satu panel.</p>
        </div>
    </x-slot>

    {{-- State Management: isLoading untuk Skeleton, loaded untuk animasi masuk, dan Modal Konfirmasi --}}
    <div class="py-12 transition-colors duration-500 min-h-screen" 
         :class="$store.theme && $store.theme.isDark ? 'bg-[#0b0d13]' : 'bg-gray-50'"
         x-data="{ 
            isLoading: true, 
            loaded: false,
            showConfirm: false, 
            confirmTitle: '', 
            confirmMessage: '',
            confirmRoute: '',
            init() {
                // Simulasi loading data 600ms
                setTimeout(() => { 
                    this.isLoading = false;
                    // Beri jeda sedikit untuk memicu animasi masuk
                    setTimeout(() => this.loaded = true, 50);
                }, 600);
            } 
         }">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm animate-bounce-short">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">{{ session('error') }}</div>
            @endif
            
            {{-- Bagian Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-5">
                {{-- SKELETON STATS --}}
                <template x-if="isLoading">
                    <div class="contents">
                        <x-skeleton variant="stat-card" />
                        <x-skeleton variant="stat-card" />
                        <x-skeleton variant="stat-card" />
                    </div>
                </template>

                {{-- REAL STATS --}}
                <template x-if="!isLoading">
                    <div class="contents">
                        @foreach([
                            ['label' => 'Mahasiswa', 'val' => $stats['total_mahasiswa'], 'sub' => 'Total akun student aktif', 'color' => 'blue', 'icon' => 'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m8-9.25a4 4 0 11-8 0 4 4 0 018 0z'],
                            ['label' => 'Soal Ujian', 'val' => $stats['total_soal'], 'sub' => 'Bank soal TOEFL CBT', 'color' => 'purple', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                            ['label' => 'Hasil Ujian', 'val' => $stats['total_ujian'], 'sub' => 'Total submission tersimpan', 'color' => 'emerald', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                        ] as $index => $stat)
                        <div 
                            x-show="loaded"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            style="transition-delay: {{ $index * 100 }}ms"
                            :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10 hover:bg-white/[0.03]' : 'bg-white border-slate-200 hover:shadow-lg'" 
                            class="border rounded-xl p-5 flex items-center justify-between shadow-sm transition-all duration-300 transform hover:-translate-y-1 cursor-default">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">{{ $stat['label'] }}</p>
                                <p class="text-3xl font-bold dark:text-white text-slate-900 leading-none mb-1">{{ number_format($stat['val']) }}</p>
                                <p class="text-sm text-slate-500 dark:text-slate-400">{{ $stat['sub'] }}</p>
                            </div>
                            <div class="w-12 h-12 rounded-xl bg-{{ $stat['color'] }}-50 dark:bg-{{ $stat['color'] }}-500/10 flex items-center justify-center flex-shrink-0 transition-transform duration-500 group-hover:rotate-12">
                                <svg class="w-6 h-6 text-{{ $stat['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $stat['icon'] }}"/></svg>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </template>
            </div>

            {{-- Main Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-[1fr_320px] gap-4 items-start">

                <div class="flex flex-col gap-4">
                    {{-- SKELETON KONTROL SESI & AKTIVITAS --}}
                    <template x-if="isLoading">
                        <div class="space-y-4">
                            <x-skeleton variant="panel" class="h-64" />
                            <x-skeleton variant="panel" class="h-32" />
                        </div>
                    </template>

                    {{-- REAL CONTENT KIRI --}}
                    <template x-if="!isLoading">
                        <div class="contents">
                            {{-- Kontrol Sesi --}}
                            <div x-show="loaded"
                                 x-transition:enter="transition ease-out duration-700 delay-300"
                                 x-transition:enter-start="opacity-0 translate-x-[-20px]"
                                 x-transition:enter-end="opacity-100 translate-x-0"
                                 :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'" 
                                 class="border rounded-xl p-5 shadow-sm transition-all duration-300">
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-indigo-500 mb-1.5">Kontrol Sesi Ujian</p>
                                <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3">Akses ujian mahasiswa</h3>
                                <div class="flex items-center gap-4 mb-3 text-xs text-slate-500">
                                    <span>Sesi aktif: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $examSetting->current_cycle }}</span></span>
                                </div>
                                <div class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold mb-3
                                    {{ $examSetting->is_open ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400' : 'bg-red-50 text-red-600 dark:bg-red-500/10 dark:text-red-400' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $examSetting->is_open ? 'bg-emerald-500' : 'bg-red-500' }} animate-pulse"></span>
                                    {{ $examSetting->is_open ? 'Dibuka' : 'Ditutup' }}
                                </div>
                                <p class="text-xs text-slate-400 leading-relaxed mb-4">Satu sesi harus ditutup dulu sebelum membuka sesi berikutnya.</p>
                                
                                <button type="button" 
                                    @click="
                                        showConfirm = true; 
                                        confirmTitle = '{{ $examSetting->is_open ? 'Tutup Sesi Ujian?' : 'Mulai Sesi Ujian Baru?' }}'; 
                                        confirmMessage = '{{ $examSetting->is_open ? 'Mahasiswa tidak akan bisa lagi mengerjakan ujian pada sesi ini.' : 'Sesi ujian baru akan dibuat dan dapat diakses oleh mahasiswa.' }}';
                                        confirmRoute = '{{ $examSetting->is_open ? route('admin.exam.close-session') : route('admin.exam.start-session') }}';
                                    "
                                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg py-2.5 transition transform active:scale-95 shadow-lg shadow-indigo-600/20">
                                    {{ $examSetting->is_open ? 'Tutup Sesi Ujian' : 'Mulai Sesi Ujian Baru' }}
                                </button>
                            </div>

                            {{-- Aktivitas Terbaru --}}
                            <div x-show="loaded"
                                 x-transition:enter="transition ease-out duration-700 delay-400"
                                 x-transition:enter-start="opacity-0 translate-y-4"
                                 x-transition:enter-end="opacity-100 translate-y-0"
                                 :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'" 
                                 class="border rounded-xl overflow-hidden shadow-sm transition-all duration-300">
                                <div class="px-5 py-3.5 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Aktivitas Terbaru</p>
                                </div>
                                <div class="px-5 py-8 text-center text-xs text-slate-300 dark:text-slate-600 font-medium italic animate-pulse">
                                    Belum ada aktivitas tercatat.
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Kolom Kanan: Menu Cepat --}}
                <div class="flex flex-col gap-4">
                    <template x-if="isLoading">
                        <x-skeleton variant="panel" class="h-[400px]" />
                    </template>

                    <template x-if="!isLoading">
                        <div x-show="loaded" 
                             x-transition:enter="transition ease-out duration-700 delay-500"
                             x-transition:enter-start="opacity-0 translate-x-[20px]"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'" 
                             class="border rounded-xl overflow-hidden shadow-sm transition-all duration-300">
                            <div class="px-5 py-3.5 border-b border-slate-100 dark:border-white/5">
                                <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Menu Cepat</p>
                            </div>
                            @foreach([
                                ['route' => route('students.index'), 'name' => 'Mahasiswa', 'desc' => 'Kelola data student', 'color' => 'blue', 'icon' => 'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m8-9.25a4 4 0 11-8 0 4 4 0 018 0z'],
                                ['route' => route('questions.index'), 'name' => 'Soal Ujian', 'desc' => 'Atur bank soal CBT', 'color' => 'purple', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                                ['route' => route('admin.practice-questions.index'), 'name' => 'Soal Latihan', 'desc' => 'Mode praktik', 'color' => 'indigo', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                                ['route' => route('practice-history.index'), 'name' => 'Riwayat Latihan', 'desc' => 'Hasil siswa', 'color' => 'amber', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                                ['route' => route('gradebook.index'), 'name' => 'Status Sistem', 'desc' => 'Laporan hasil', 'color' => 'emerald', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            ] as $item)
                            <a href="{{ $item['route'] }}" 
                               class="group flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-white/5 transition-all duration-200"
                               :class="$store.theme && $store.theme.isDark ? 'hover:bg-white/[0.03]' : 'hover:bg-indigo-50/50'">
                                <div class="w-9 h-9 rounded-lg bg-{{ $item['color'] }}-50 dark:bg-{{ $item['color'] }}-500/10 text-{{ $item['color'] }}-600 flex items-center justify-center flex-shrink-0 transition-all duration-300 group-hover:scale-110">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}"/></svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $item['name'] }}</p>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ $item['desc'] }}</p>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </template>
                </div>
            </div>

            {{-- Modal Konfirmasi (Real Modal, No Skeleton needed) --}}
            <div x-cloak x-show="showConfirm" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
                <div @click.away="showConfirm = false" class="w-full max-w-sm bg-white dark:bg-[#111827] rounded-2xl p-6 border border-slate-200 dark:border-white/10 shadow-2xl">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2" x-text="confirmTitle"></h3>
                    <p class="text-sm text-slate-500 mb-6" x-text="confirmMessage"></p>
                    <div class="flex gap-3">
                        <form method="POST" class="flex-1" :action="confirmRoute">
                            @csrf
                            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl py-3 transition active:scale-95">Ya, Lanjutkan</button>
                        </form>
                        <button type="button" @click="showConfirm=false" class="flex-1 bg-slate-100 dark:bg-white/5 dark:text-white text-slate-700 text-sm font-bold rounded-xl py-3 transition">Batal</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>