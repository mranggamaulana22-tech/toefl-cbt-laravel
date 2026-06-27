<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-0.5 animate-fade-in-down">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Detail Soal Latihan</p>
            
            {{-- Skeleton Header --}}
            <template x-if="!loaded">
                <div class="space-y-2 mt-1">
                    <x-skeleton variant="title" class="h-8 w-64" />
                    <x-skeleton variant="title" class="h-4 w-96 opacity-50" />
                </div>
            </template>

            {{-- Real Header --}}
            <div x-show="loaded" x-cloak>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Detail Bank Soal Latihan</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Lihat detail lengkap soal sebelum diperbarui atau dihapus.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 transition-colors duration-500 min-h-screen" 
         :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-slate-50'"
         x-data="{ loaded: false }" 
         x-init="setTimeout(() => loaded = true, 600)">
        
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- SKELETON LOADER CONTAINER --}}
            <div x-show="!loaded" class="space-y-6">
                {{-- Skeleton Header Card --}}
                <x-skeleton variant="panel" class="h-40 rounded-3xl" />
                
                {{-- Skeleton Grid --}}
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <x-skeleton class="h-24 rounded-2xl" />
                    <x-skeleton class="h-24 rounded-2xl" />
                    <x-skeleton class="h-24 rounded-2xl xl:col-span-2" />
                </div>

                {{-- Skeleton Content --}}
                <div class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                    <div class="space-y-6">
                        <x-skeleton class="h-40 rounded-2xl" />
                        <x-skeleton class="h-64 rounded-2xl" />
                    </div>
                    <div class="space-y-4">
                        <x-skeleton class="h-20 rounded-2xl" />
                        <x-skeleton class="h-20 rounded-2xl" />
                        <x-skeleton class="h-20 rounded-2xl" />
                        <x-skeleton class="h-20 rounded-2xl" />
                    </div>
                </div>
            </div>

            {{-- REAL CONTENT CONTAINER --}}
            <div x-show="loaded"
                 x-cloak
                 x-transition:enter="transition ease-out duration-600"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-slate-200 shadow-[0_12px_32px_rgba(15,23,42,0.08)]'"
                 class="overflow-hidden rounded-3xl border transition-all">
                
                {{-- Gradient Header Section --}}
                <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-sky-600 px-6 py-6 text-white md:px-8">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div class="animate-fade-in-left">
                            <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-100/80">Soal Latihan Detail</p>
                            <h3 class="mt-2 text-2xl font-black tracking-tight">{{ ucfirst($practiceQuestion->category) }} Session</h3>
                            <p class="mt-2 text-sm text-indigo-100/90">Periksa isi soal, jawaban, dan aset pendukung sebelum melakukan perubahan.</p>
                        </div>
                        <div class="flex flex-wrap gap-2 animate-fade-in-right">
                            <a href="{{ route('admin.practice-questions.edit', $practiceQuestion) }}" 
                               class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-sm font-bold text-indigo-700 transition hover:bg-indigo-50 active:scale-95 shadow-sm">
                                Edit Soal
                            </a>
                            <a href="{{ route('admin.practice-questions.index') }}" 
                               class="inline-flex items-center rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/15 active:scale-95">
                                Kembali
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    {{-- Summary Grid --}}
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 dark:bg-white/5 dark:border-white/5 p-4 transition-all duration-500 delay-100">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Kategori</p>
                            <p class="mt-2 text-lg font-black text-slate-900 dark:text-white">{{ ucfirst($practiceQuestion->category) }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 dark:bg-white/5 dark:border-white/5 p-4 transition-all duration-500 delay-200">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Jawaban Benar</p>
                            <p class="mt-2 text-lg font-black text-indigo-700 dark:text-indigo-400">{{ $practiceQuestion->correct_answer }}</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 dark:bg-white/5 dark:border-white/5 p-4 xl:col-span-2 transition-all duration-500 delay-300">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Status Audio</p>
                            <p class="mt-2 text-lg font-black {{ $practiceQuestion->audio_path ? 'text-emerald-600' : 'text-slate-500' }}">
                                {{ $practiceQuestion->audio_path ? 'Ada file audio' : 'Tidak ada audio' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
                        {{-- Left Column: Question Content --}}
                        <div class="space-y-6">
                            <div class="rounded-2xl border border-slate-200 bg-white dark:bg-white/5 dark:border-white/5 p-5 shadow-sm transition-all duration-500 delay-400">
                                <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Pertanyaan</p>
                                <p class="mt-3 whitespace-pre-line leading-7 text-slate-700 dark:text-slate-300">{{ $practiceQuestion->question_text }}</p>
                            </div>

                            @if($practiceQuestion->passage)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 dark:bg-white/5 dark:border-white/5 p-5 transition-all duration-500 delay-500">
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Passage</p>
                                    <p class="mt-3 whitespace-pre-line leading-7 text-slate-700 dark:text-slate-300">{{ $practiceQuestion->passage }}</p>
                                </div>
                            @endif

                            @if($practiceQuestion->audio_path)
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 dark:bg-white/5 dark:border-white/5 p-5 transition-all duration-500 delay-600">
                                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Audio</p>
                                    <audio controls class="mt-3 w-full rounded-lg">
                                        <source src="{{ asset('storage/' . $practiceQuestion->audio_path) }}" type="audio/mpeg">
                                    </audio>

                                    <div class="mt-4 rounded-xl border border-sky-100 bg-white dark:bg-indigo-900/10 dark:border-indigo-500/20 p-4">
                                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-indigo-500">Transcript Audio</p>
                                        <p class="mt-2 whitespace-pre-line leading-7 text-slate-700 dark:text-slate-400 text-sm">{{ $practiceQuestion->audio_transcript ?: '-' }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Right Column: Choices --}}
                        <div class="rounded-2xl border border-slate-200 bg-white dark:bg-white/5 dark:border-white/5 p-5 shadow-sm transition-all duration-500 delay-700">
                            <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">Pilihan Jawaban</p>
                            <div class="mt-4 space-y-3">
                                @foreach(['a', 'b', 'c', 'd'] as $opt)
                                    <div class="rounded-xl border px-4 py-3 transition-all duration-300 group hover:border-indigo-300
                                        {{ strtoupper($opt) === $practiceQuestion->correct_answer 
                                            ? 'border-emerald-200 bg-emerald-50 dark:bg-emerald-500/10 dark:border-emerald-500/20' 
                                            : 'border-slate-200 bg-slate-50 dark:bg-white/5 dark:border-white/5' }}">
                                        <div class="flex items-center justify-between">
                                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] 
                                                {{ strtoupper($opt) === $practiceQuestion->correct_answer ? 'text-emerald-600' : 'text-slate-400 group-hover:text-indigo-500' }}">
                                                Opsi {{ strtoupper($opt) }}
                                            </p>
                                            @if(strtoupper($opt) === $practiceQuestion->correct_answer)
                                                <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Jawaban Benar</span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-sm font-medium {{ strtoupper($opt) === $practiceQuestion->correct_answer ? 'text-emerald-900 dark:text-emerald-400' : 'text-slate-800 dark:text-slate-300' }}">
                                            {{ $practiceQuestion->{'option_'.$opt} }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>