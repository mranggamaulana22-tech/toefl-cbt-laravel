<x-app-layout>
    <x-slot name="header">
        {{-- Animasi Fade In Down untuk Header --}}
        <div class="flex flex-col gap-0.5 animate-fade-in-down">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Bank Soal</p>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Manajemen Soal TOEFL CBT</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola bank soal ujian, tambah soal baru, atau perbarui konten yang ada.</p>
        </div>
    </x-slot>

    <div class="py-8 transition-colors duration-500 min-h-screen" 
         :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-slate-50'" 
         x-data="{ 
            loaded: false, 
            isLoading: true,
            init() { 
                // Simulasi loading awal 600ms untuk memunculkan skeleton
                setTimeout(() => { 
                    this.isLoading = false; 
                    // Picu animasi staggered setelah skeleton hilang
                    setTimeout(() => this.loaded = true, 50);
                }, 600);
            } 
         }">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm animate-bounce-short">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                {{-- SKELETON STATS --}}
                <template x-if="isLoading">
                    <div class="contents">
                        <x-skeleton variant="stat-card" />
                        <x-skeleton variant="stat-card" />
                        <x-skeleton variant="stat-card" />
                        <x-skeleton variant="stat-card" />
                    </div>
                </template>

                {{-- REAL STATS --}}
                <template x-if="!isLoading">
                    <div class="contents">
                        @php
                            $statsItems = [
                                ['label' => 'Total Soal', 'val' => $stats['total_questions'], 'sub' => 'Semua kategori soal', 'color' => 'slate'],
                                ['label' => 'Listening', 'val' => $stats['listening_count'], 'sub' => 'Soal audio comprehension', 'color' => 'blue'],
                                ['label' => 'Structure', 'val' => $stats['structure_count'], 'sub' => 'Grammar & syntax', 'color' => 'purple'],
                                ['label' => 'Reading', 'val' => $stats['reading_count'], 'sub' => 'Reading comprehension', 'color' => 'emerald'],
                            ];
                        @endphp
                        @foreach($statsItems as $index => $s)
                            <div 
                                x-show="loaded"
                                x-transition:enter="transition ease-out duration-500"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                style="transition-delay: {{ $index * 100 }}ms"
                                :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'" 
                                class="border rounded-2xl p-5 shadow-sm transition-all hover:scale-[1.02] duration-300">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400 mb-2">{{ $s['label'] }}</p>
                                <p class="text-3xl font-black {{ $s['color'] === 'slate' ? 'text-slate-900 dark:text-white' : 'text-'.$s['color'].'-600' }}">
                                    {{ $s['val'] }}
                                </p>
                                <p class="text-xs text-slate-500">{{ $s['sub'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </template>
            </div>

            {{-- Filter & Action Bar --}}
            <div x-show="loaded"
                 x-transition:enter="transition ease-out duration-500 delay-[400ms]"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'" 
                 class="border rounded-2xl p-6 shadow-sm mb-6 transition-all">
                <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                    <form method="GET" action="{{ route('questions.index') }}" class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <div>
                            <label for="category" class="block text-xs font-bold uppercase tracking-[0.18em] text-slate-500 mb-2">Filter Kategori</label>
                            <select name="category" id="category" :class="$store.theme?.isDark ? 'bg-[#1e293b] border-white/10 text-white' : 'bg-white border-slate-200 text-slate-900'" class="w-full px-4 py-2.5 rounded-lg text-sm font-medium hover:border-indigo-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                                <option value="">Semua Kategori</option>
                                <option value="listening" @selected($category === 'listening')>🎧 Listening</option>
                                <option value="structure" @selected($category === 'structure')>📝 Structure</option>
                                <option value="reading" @selected($category === 'reading')>📖 Reading</option>
                            </select>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            Terapkan Filter
                        </button>
                        <a href="{{ route('questions.index') }}" :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10 border-white/10' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-transparent'" class="px-5 py-2.5 text-sm font-semibold rounded-lg transition text-center active:scale-95 border">
                            Reset
                        </a>
                    </form>

                    <div class="flex flex-col sm:flex-row gap-2 lg:justify-end">
                        <a href="{{ route('questions.export.csv', ['category' => $category]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            <i class="fas fa-file-csv"></i> Export CSV
                        </a>
                        <a href="{{ route('questions.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            <i class="fas fa-plus"></i> Tambah Soal
                        </a>
                    </div>
                </div>
            </div>

            {{-- Questions Table --}}
            <div x-show="loaded"
                 x-transition:enter="transition ease-out duration-500 delay-[600ms]"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'" 
                 class="border rounded-2xl overflow-hidden shadow-sm transition-all relative">
                
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr :class="$store.theme?.isDark ? 'bg-white/5 border-white/5' : 'bg-gray-50 border-slate-200'" class="border-b">
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">No</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Kategori</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Pertanyaan</th>
                                <th class="px-6 py-3 text-xs font-bold uppercase tracking-[0.12em] text-slate-500 text-center">Jawaban</th>
                                <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Aksi</th>
                            </tr>
                        </thead>

                        {{-- SKELETON BODY --}}
                        <tbody x-show="isLoading" x-cloak>
                            @for ($i = 0; $i < 5; $i++)
                                <x-skeleton variant="table-row" :lines="5" />
                            @endfor
                        </tbody>

                        {{-- REAL DATA BODY --}}
                        <tbody x-show="!isLoading" class="divide-y" :class="$store.theme?.isDark ? 'divide-white/5' : 'divide-slate-200'">
                            @forelse($questions as $q)
                            <tr class="transition-colors group" :class="$store.theme?.isDark ? 'hover:bg-white/[0.02]' : 'hover:bg-slate-50'">
                                <td class="px-6 py-3 text-sm font-semibold text-slate-500">{{ $questions->firstItem() + $loop->index }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wide transition-transform group-hover:scale-105
                                        {{ $q->category === 'listening' ? 'bg-blue-600 text-white' : ($q->category === 'structure' ? 'bg-purple-600 text-white' : 'bg-emerald-600 text-white') }}">
                                        {{ $q->category }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ Str::limit($q->question_text, 65) }}</td>
                                <td class="px-6 py-3 text-center">
                                    <span :class="$store.theme?.isDark ? 'bg-indigo-500 text-white' : 'bg-slate-100 text-slate-700'" class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-bold text-sm">
                                        {{ $q->correct_answer }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-center">
                                    <div class="inline-flex gap-2 transition-all duration-300">
                                        <a href="{{ route('questions.edit', $q->id) }}"
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-blue-400 bg-blue-600 text-white text-xs font-bold transition active:scale-90 shadow-md focus:outline-none focus:ring-2 focus:ring-blue-300 dark:bg-blue-500 dark:border-blue-300 dark:text-white hover:bg-blue-700 hover:border-blue-500">
                                            EDIT
                                        </a>
                                        <form action="{{ route('questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-red-400 bg-red-600 text-white text-xs font-bold transition active:scale-90 shadow-md focus:outline-none focus:ring-2 focus:ring-red-300 dark:bg-red-500 dark:border-red-300 dark:text-white hover:bg-red-700 hover:border-red-500">
                                                HAPUS
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-12 text-center text-slate-500 italic">Belum ada soal ditemukan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile Card View (With Skeleton Support) --}}
                <div class="md:hidden">
                    <template x-if="isLoading">
                        <div class="p-4 space-y-4">
                            <x-skeleton variant="panel" class="h-24" />
                            <x-skeleton variant="panel" class="h-24" />
                        </div>
                    </template>
                    <template x-if="!isLoading">
                        <div class="divide-y" :class="$store.theme?.isDark ? 'divide-white/5' : 'divide-slate-200'">
                            @foreach($questions as $q)
                                {{-- Konten Card Mobile Kamu --}}
                                <div class="px-4 py-4">...</div>
                            @endforeach
                        </div>
                    </template>
                </div>

                {{-- Pagination --}}
                @if($questions->hasPages())
                    <div x-show="!isLoading" :class="$store.theme?.isDark ? 'bg-white/[0.03] border-white/5' : 'bg-slate-50 border-slate-200'" class="px-6 py-4 border-t">
                        {{ $questions->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>