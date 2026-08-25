<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-0.5">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Admin Control Center</p>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ __('Admin Dashboard') }}</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola ujian, latihan, mahasiswa, dan riwayat dari satu panel.</p>
        </div>
    </x-slot>

    <div class="py-6 bg-gray-50 dark:bg-[#0b0d13]"
         x-data="{
            showConfirm: false,
            confirmTitle: '',
            confirmMessage: '',
            confirmRoute: '',
         }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm text-emerald-700 shadow-sm animate-bounce-short">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-700 shadow-sm">{{ session('error') }}</div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 mb-4">
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
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-4 items-start">

                {{-- Kontrol Sesi --}}
                <div class="border rounded-2xl overflow-hidden shadow-sm bg-white border-slate-200 dark:bg-[#111827] dark:border-slate-700">

                    {{-- Header kartu: judul + status pill --}}
                    <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-indigo-500 mb-0.5">Kontrol Sesi Ujian</p>
                            <h3 class="text-sm font-semibold text-slate-900 dark:text-white">Akses ujian mahasiswa</h3>
                        </div>
                        <div class="flex flex-col items-end gap-1 text-right">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold
                                {{ $examSetting->is_open
                                    ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400'
                                    : 'bg-slate-100 text-slate-500 dark:bg-white/5 dark:text-slate-400' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $examSetting->is_open ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                {{ $examSetting->is_open ? 'Sedang Dibuka' : 'Ditutup' }}
                            </span>
                            <span class="text-[11px] text-slate-400">Sesi ke-{{ $examSetting->current_cycle }}</span>
                        </div>
                    </div>

                    <div class="p-5 max-w-md">
                        @if ($examSetting->is_open && $examSetting->access_code)
                            <div class="mb-4 rounded-xl border border-indigo-200 bg-indigo-50 px-4 py-3 dark:border-indigo-500/30 dark:bg-indigo-500/10">
                                <div class="flex items-center justify-between mb-1.5">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-indigo-500">Kode Akses Ujian</p>
                                    <button type="button"
                                        @click="
                                            showConfirm = true;
                                            confirmTitle = 'Generate Ulang Kode Akses?';
                                            confirmMessage = 'Kode lama tidak akan bisa dipakai lagi. Pastikan mahasiswa belum mulai input kode lama.';
                                            confirmRoute = '{{ route('admin.exam.regenerate-code') }}';
                                        "
                                        class="inline-flex items-center gap-1 text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-indigo-200">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        Generate Ulang
                                    </button>
                                </div>

                                <p id="access-code-value" class="text-2xl font-black font-mono tracking-[0.3em] text-indigo-700 dark:text-indigo-300 select-all">
                                    {{ $examSetting->access_code }}
                                </p>

                                <div class="flex items-center justify-between mt-1.5">
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                        Bacakan kode ini ke mahasiswa
                                        @if ($examSetting->access_code_generated_at)
                                            &middot; {{ $examSetting->access_code_generated_at->diffForHumans() }}
                                        @endif
                                    </p>
                                    <button type="button"
                                        onclick="navigator.clipboard.writeText('{{ $examSetting->access_code }}')"
                                        class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-300 dark:hover:text-indigo-200">
                                        Copy
                                    </button>
                                </div>
                            </div>

                            @if($examSetting->paketSoal)
                                <div class="mb-4 flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Sesi ini memakai <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $examSetting->paketSoal->nama }}</span>
                                </div>
                            @endif
                        @else
                            <div class="mb-4">
                                <label for="paket_soal_id" class="block text-[11px] font-bold uppercase tracking-[0.14em] text-slate-500 mb-1.5">
                                    Paket Soal untuk Sesi Ini
                                </label>
                                <select id="paket_soal_id" name="paket_soal_id" form="start-session-form"
                                    class="w-full px-3.5 py-2.5 rounded-lg text-sm font-medium border bg-white border-slate-200 text-slate-900 dark:bg-[#1e293b] dark:border-white/10 dark:text-white">
                                    <option value="">Pilih paket soal...</option>
                                    @foreach($pakets as $paket)
                                        <option value="{{ $paket['model']->id }}" {{ !$paket['is_complete'] ? 'disabled' : '' }}>
                                            {{ $paket['model']->nama }} {{ $paket['is_complete'] ? '(140/140)' : '(belum lengkap)' }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1.5 text-[11px] text-slate-400">
                                    Hanya paket dengan status lengkap (140 soal) yang bisa dipakai untuk sesi ujian.
                                </p>
                            </div>
                        @endif

                        <div class="flex flex-col gap-2">
                            <form id="start-session-form" method="POST" action="{{ route('admin.exam.start-session') }}">
                                @csrf
                                <button type="submit"
                                    {{ $examSetting->is_open ? 'disabled' : '' }}
                                    class="w-full text-sm font-bold rounded-lg py-2.5 transition transform shadow-lg
                                        {{ $examSetting->is_open
                                            ? 'cursor-not-allowed bg-slate-300 text-slate-600 shadow-none dark:bg-white/10 dark:text-slate-400'
                                            : 'bg-indigo-600 hover:bg-indigo-700 text-white active:scale-95 shadow-indigo-600/20' }}">
                                    {{ $examSetting->is_open ? 'Sesi Sedang Aktif' : 'Mulai Sesi Ujian Baru' }}
                                </button>
                            </form>

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
                </div>

                {{-- Ringkasan Paket Soal --}}
                <div class="border rounded-2xl overflow-hidden shadow-sm bg-white border-slate-200 dark:bg-[#111827] dark:border-slate-700">
                    <div class="px-5 py-3.5 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">Ringkasan Paket</p>
                        <a href="{{ route('paket-soal.index') }}" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                            Lihat semua ({{ $pakets->count() }})
                        </a>
                    </div>
                    <div class="divide-y divide-slate-100 dark:divide-slate-800">
                        {{-- Dibatasi maksimal 5 paket, supaya panel ini tidak jadi
                             memanjang tak terkendali begitu jumlah paket bertambah
                             banyak seiring makin sering admin ganti sesi ujian. --}}
                        @forelse($pakets->take(5) as $paket)
                            <a href="{{ route('paket-soal.questions.index', $paket['model']) }}"
                               class="flex items-center justify-between gap-2 px-5 py-3 transition-colors hover:bg-slate-50 dark:hover:bg-white/[0.03]">
                                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $paket['model']->nama }}</span>
                                @if($paket['is_complete'])
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <i class="fas fa-check-circle"></i> Lengkap
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                                        Belum Lengkap
                                    </span>
                                @endif
                            </a>
                        @empty
                            <p class="px-5 py-6 text-center text-xs text-slate-400 italic">Belum ada paket soal.</p>
                        @endforelse

                        @if($pakets->count() > 5)
                            <a href="{{ route('paket-soal.index') }}"
                               class="block px-5 py-2.5 text-center text-xs font-semibold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 transition-colors hover:bg-slate-50 dark:hover:bg-white/[0.03]">
                                +{{ $pakets->count() - 5 }} paket lainnya
                            </a>
                        @endif
                    </div>
                    <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-800">
                        <a href="{{ route('paket-soal.index') }}"
                           class="inline-flex w-full items-center justify-center gap-2 px-3 py-2 rounded-lg text-xs font-semibold transition bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                            <i class="fas fa-plus"></i> Kelola Paket Soal
                        </a>
                    </div>
                </div>
            </div>

            <x-confirm-modal />

        </div>
    </div>
</x-app-layout>