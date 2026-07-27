<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-0.5">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Admin Control Center</p>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ __('Admin Dashboard') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola ujian, latihan, mahasiswa, dan riwayat dari satu panel.</p>
        </div>
    </x-slot>

    <div class="py-12 transition-colors duration-500 min-h-screen"
         :class="$store.theme && $store.theme.isDark ? 'bg-[#0b0d13]' : 'bg-gray-50'"
         x-data="{
            showConfirm: false,
            confirmTitle: '',
            confirmMessage: '',
            confirmRoute: '',
         }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm animate-bounce-short">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">{{ session('error') }}</div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-5">
                @foreach([
                    ['label' => 'Mahasiswa', 'val' => $stats['total_mahasiswa'], 'sub' => 'Total akun student aktif', 'color' => 'blue', 'icon' => 'M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m8-9.25a4 4 0 11-8 0 4 4 0 018 0z'],
                    ['label' => 'Soal Ujian', 'val' => $stats['total_soal'], 'sub' => 'Bank soal TOEFL CBT', 'color' => 'purple', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['label' => 'Hasil Ujian', 'val' => $stats['total_ujian'], 'sub' => 'Total submission tersimpan', 'color' => 'emerald', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                ] as $index => $stat)
                    <x-stat-card-icon
                        :label="$stat['label']"
                        :value="$stat['val']"
                        :sub="$stat['sub']"
                        :color="$stat['color']"
                        :icon="$stat['icon']"
                        :delay="$index * 100"
                    />
                @endforeach
            </div>

            {{-- Main Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-[1fr_320px] gap-4 items-start">

                <div class="flex flex-col gap-4">
                    {{-- Kontrol Sesi --}}
                    <div :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'"
                         class="border rounded-xl p-5 shadow-sm transition-all duration-300">
                        <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-indigo-500 mb-1.5">Kontrol Sesi Ujian</p>
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3">Akses ujian mahasiswa</h3>
                        <div class="flex items-center gap-4 mb-3 text-xs text-slate-500">
                            <span>Status: 
                                <span class="font-semibold {{ $examSetting->is_open ? 'text-emerald-600' : 'text-slate-500' }}">
                                    {{ $examSetting->is_open ? 'Sedang Dibuka' : 'Ditutup' }}
                                </span>
                            </span>
                            <span>Sesi aktif: <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $examSetting->current_cycle }}</span></span>
                        </div>

                        <div class="flex flex-col gap-2">
                            <button type="button"
                                @click="
                                    showConfirm = true;
                                    confirmTitle = 'Mulai Sesi Ujian Baru?';
                                    confirmMessage = 'Sesi ujian baru akan dibuat dan dapat diakses oleh mahasiswa.';
                                    confirmRoute = '{{ route('admin.exam.start-session') }}';
                                "
                                {{ $examSetting->is_open || !$examReadiness['can_start'] ? 'disabled' : '' }}
                                class="w-full text-sm font-bold rounded-lg py-2.5 transition transform shadow-lg
                                    {{ $examSetting->is_open || !$examReadiness['can_start']
                                        ? 'cursor-not-allowed bg-slate-300 text-slate-600 shadow-none dark:bg-white/10 dark:text-slate-400'
                                        : 'bg-indigo-600 hover:bg-indigo-700 text-white active:scale-95 shadow-indigo-600/20' }}">
                                {{ $examSetting->is_open ? 'Sesi Sedang Aktif' : ($examReadiness['can_start'] ? 'Mulai Sesi Ujian Baru' : 'Bank Soal Belum Cukup') }}
                            </button>

                            <button type="button"
                                @click="
                                    showConfirm = true;
                                    confirmTitle = 'Tutup Sesi Ujian?';
                                    confirmMessage = 'Mahasiswa tidak akan bisa lagi mengerjakan ujian pada sesi ini.';
                                    confirmRoute = '{{ route('admin.exam.close-session') }}';
                                "
                                {{ !$examSetting->is_open ? 'disabled' : '' }}
                                class="w-full text-sm font-bold rounded-lg py-2.5 transition transform shadow-lg
                                    {{ !$examSetting->is_open
                                        ? 'cursor-not-allowed bg-slate-300 text-slate-600 shadow-none dark:bg-white/10 dark:text-slate-400'
                                        : 'bg-rose-600 hover:bg-rose-700 text-white active:scale-95 shadow-rose-600/20' }}">
                                Tutup Sesi Ujian
                            </button>
                        </div>
                    </div>

                    {{-- Aktivitas Terbaru --}}
                    <div :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'"
                         class="border rounded-xl overflow-hidden shadow-sm transition-all duration-300">
                        <div class="px-5 py-3.5 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                            <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-400">Aktivitas Terbaru</p>
                        </div>
                        <div class="px-5 py-8 text-center text-xs text-slate-300 dark:text-slate-600 font-medium italic animate-pulse">
                            Belum ada aktivitas tercatat.
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Menu Cepat --}}
                <div class="flex flex-col gap-4">
                    <div :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'"
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
                            <x-quick-link :route="$item['route']" :name="$item['name']" :desc="$item['desc']" :color="$item['color']" :icon="$item['icon']" />
                        @endforeach
                    </div>
                </div>
            </div>

            <x-confirm-modal />

        </div>
    </div>
</x-app-layout>