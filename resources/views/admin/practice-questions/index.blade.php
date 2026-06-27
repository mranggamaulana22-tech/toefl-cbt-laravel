<x-app-layout>
    <x-slot name="header">
        {{-- Animasi Fade In Down pada Header --}}
        <div class="flex flex-col gap-0.5 animate-fade-in-down">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Soal Latihan</p>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Manajemen Bank Soal Latihan</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Kelola soal latihan, audio, dan jawaban dengan tampilan yang seragam dan premium.</p>
        </div>
    </x-slot>

    <div class="py-8 transition-colors duration-500 min-h-screen" 
         :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-slate-50'"
         x-data="{ 
            loaded: false, 
            isLoading: true,
            init() {
                setTimeout(() => {
                    this.isLoading = false;
                    setTimeout(() => this.loaded = true, 50);
                }, 600);
            }
         }">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm animate-bounce-short">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-5 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm animate-bounce-short">
                    {{ session('error') }}
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
                        @foreach([
                            ['label' => 'Total Soal', 'val' => $stats['total_questions'], 'sub' => 'Semua latihan tersedia', 'color' => 'slate'],
                            ['label' => 'Listening', 'val' => $stats['listening_count'], 'sub' => 'Soal audio', 'color' => 'blue'],
                            ['label' => 'Structure', 'val' => $stats['structure_count'], 'sub' => 'Grammar & syntax', 'color' => 'purple'],
                            ['label' => 'Reading', 'val' => $stats['reading_count'], 'sub' => 'Reading comprehension', 'color' => 'emerald'],
                        ] as $index => $stat)
                        <div 
                            x-show="loaded"
                            x-transition:enter="transition ease-out duration-500"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            style="transition-delay: {{ $index * 100 }}ms"
                            :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'" 
                            class="border rounded-2xl p-5 shadow-sm transition-all hover:scale-[1.02] duration-300">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400 mb-2">{{ $stat['label'] }}</p>
                            <p class="text-3xl font-black {{ $stat['color'] === 'slate' ? 'text-slate-900 dark:text-white' : 'text-'.$stat['color'].'-600' }}">{{ $stat['val'] }}</p>
                            <p class="text-xs text-slate-500 mt-1">{{ $stat['sub'] }}</p>
                        </div>
                        @endforeach
                    </div>
                </template>
            </div>

            {{-- Filter & Action Bar --}}
            <div x-show="loaded"
                 x-transition:enter="transition ease-out duration-500 delay-500"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'" 
                 class="border rounded-2xl p-6 shadow-sm mb-6 transition-all">
                <div class="flex flex-col lg:flex-row lg:items-end gap-4">
                    <form method="GET" action="{{ route('admin.practice-questions.index') }}" class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-[0.18em] text-slate-500 mb-2">Kategori</label>
                            <select name="category" :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10' : 'bg-white text-slate-900 border-slate-200'" class="w-full px-4 py-2.5 rounded-lg text-sm font-medium hover:border-indigo-300 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 transition">
                                <option value="">Semua Kategori</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" @selected($category === $cat)>{{ ucfirst($cat) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            Cari
                        </button>
                        <a href="{{ route('admin.practice-questions.index') }}" :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="px-5 py-2.5 text-sm font-semibold rounded-lg transition text-center active:scale-95">
                            Reset
                        </a>
                    </form>

                    <div class="flex flex-col sm:flex-row gap-2 lg:justify-end">
                        <a href="{{ route('admin.practice-questions.export.csv', ['category' => $category]) }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Export CSV
                        </a>
                        <a href="{{ route('admin.practice-questions.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition active:scale-95 shadow-sm">
                            <i class="fas fa-plus"></i>
                            Tambah Soal Latihan
                        </a>
                    </div>
                </div>
            </div>

            {{-- Table Container --}}
            <div x-show="loaded"
                 x-transition:enter="transition ease-out duration-500 delay-700"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'" 
                 class="border rounded-2xl overflow-hidden shadow-sm transition-all relative">
                
                @if ($practiceQuestions->count() > 0)
                    <div class="hidden md:block overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr :class="$store.theme?.isDark ? 'bg-white/5 border-white/5' : 'bg-slate-50 border-slate-200'" class="border-b">
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Kategori</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Soal</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Audio</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Jawaban</th>
                                    <th class="px-6 py-3 text-center text-xs font-bold uppercase tracking-[0.12em] text-slate-500">Aksi</th>
                                </tr>
                            </thead>

                            {{-- SKELETON BODY --}}
                            <tbody x-show="isLoading" x-cloak>
                                @for ($i = 0; $i < 5; $i++)
                                    <x-skeleton variant="table-row" :lines="5" />
                                @endfor
                            </tbody>

                            {{-- DATA BODY --}}
                            <tbody x-show="!isLoading" class="divide-y" :class="$store.theme?.isDark ? 'divide-white/5' : 'divide-slate-200'">
                                @foreach ($practiceQuestions as $question)
                                    <tr class="transition-colors group" :class="$store.theme?.isDark ? 'hover:bg-white/[0.02]' : 'hover:bg-slate-50'">
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold tracking-wide transition-transform group-hover:scale-105
                                                {{ $question->category === 'listening' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : ($question->category === 'structure' ? 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400') }}">
                                                {{ ucfirst($question->category) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="max-w-2xl text-slate-700 dark:text-slate-300 line-clamp-2">{{ $question->question_text }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($question->audio_path)
                                                <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                                                    <i class="fas fa-check-circle mr-1"></i> Ada
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-white/5 px-2.5 py-1 text-xs font-bold text-slate-500 dark:text-slate-500">
                                                    Tidak ada
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300' : 'bg-slate-100 text-slate-700'" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-sm font-bold transition-colors group-hover:bg-indigo-500 group-hover:text-white">
                                                {{ $question->correct_answer }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="inline-flex items-center gap-2">
                                                <a href="{{ route('admin.practice-questions.show', $question) }}" :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="inline-flex items-center rounded-lg px-3 py-2 text-xs font-semibold transition active:scale-90">
                                                    Lihat
                                                </a>
                                                <a href="{{ route('admin.practice-questions.edit', $question) }}" class="inline-flex items-center rounded-lg bg-blue-50 dark:bg-blue-500/10 px-3 py-2 text-xs font-semibold text-blue-700 dark:text-blue-400 transition hover:bg-blue-100 active:scale-90">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.practice-questions.destroy', $question) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus soal latihan ini?');">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center rounded-lg bg-red-50 dark:bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-700 dark:text-red-400 transition hover:bg-red-100 active:scale-90">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Mobile View --}}
                    <div class="md:hidden divide-y" :class="$store.theme?.isDark ? 'divide-white/5' : 'divide-slate-200'">
                        <template x-if="isLoading">
                            <div class="p-4 space-y-4">
                                <x-skeleton variant="panel" class="h-24" />
                                <x-skeleton variant="panel" class="h-24" />
                            </div>
                        </template>
                        <template x-if="!isLoading">
                            <div class="contents">
                                @foreach ($practiceQuestions as $question)
                                    <div class="px-4 py-4 transition-colors" :class="$store.theme?.isDark ? 'hover:bg-white/[0.02]' : 'hover:bg-slate-50'">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0 flex-1">
                                                <div class="mb-2 flex items-center gap-2">
                                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px] font-bold tracking-wide
                                                        {{ $question->category === 'listening' ? 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' : ($question->category === 'structure' ? 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400' : 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400') }}">
                                                        {{ ucfirst($question->category) }}
                                                    </span>
                                                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ $question->audio_path ? 'Audio tersedia' : 'Tanpa audio' }}</span>
                                                </div>
                                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 line-clamp-2">{{ $question->question_text }}</p>
                                            </div>
                                            <span :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300' : 'bg-slate-100 text-slate-700'" class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-sm font-bold flex-shrink-0">
                                                {{ $question->correct_answer }}
                                            </span>
                                        </div>
                                        <div class="mt-4 flex gap-2">
                                            <a href="{{ route('admin.practice-questions.show', $question) }}" class="flex-1 rounded-lg px-3 py-2 text-center text-xs font-semibold border border-transparent transition active:scale-95 :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300' : 'bg-slate-100 text-slate-700'">Lihat</a>
                                            <a href="{{ route('admin.practice-questions.edit', $question) }}" class="flex-1 rounded-lg bg-blue-50 dark:bg-blue-500/10 px-3 py-2 text-center text-xs font-semibold text-blue-700 transition active:scale-95">Edit</a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </template>
                    </div>

                    {{-- Pagination --}}
                    <div x-show="!isLoading" :class="$store.theme?.isDark ? 'bg-white/[0.03] border-white/5' : 'bg-slate-50 border-slate-200'" class="border-t px-6 py-4">
                        {{ $practiceQuestions->links() }}
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="px-6 py-12 text-center animate-pulse">
                        <svg class="mx-auto mb-3 h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <p class="text-sm text-slate-500 italic">Belum ada soal latihan.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>