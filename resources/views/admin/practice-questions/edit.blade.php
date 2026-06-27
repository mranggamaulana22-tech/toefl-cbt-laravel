<x-app-layout>
    <x-slot name="header">
        {{-- Animasi Fade In Down pada Header --}}
        <div class="flex flex-col gap-0.5 animate-fade-in-down">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Soal Latihan</p>
            
            {{-- Skeleton Header Title --}}
            <template x-if="!loaded">
                <div class="space-y-2 mt-1">
                    <x-skeleton variant="title" class="h-8 w-64" />
                    <x-skeleton variant="title" class="h-4 w-80 opacity-50" />
                </div>
            </template>

            {{-- Real Header --}}
            <div x-show="loaded" x-cloak>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tambah Soal Latihan Baru</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Buat soal latihan dengan layout yang konsisten dan modern.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-8 transition-colors duration-500 min-h-screen" 
         :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-slate-50'"
         x-data="{ loaded: false }" 
         x-init="setTimeout(() => loaded = true, 600)">
        
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            
            {{-- SKELETON LOADER CONTAINER --}}
            <div x-show="!loaded" 
                 :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-slate-200'"
                 class="overflow-hidden rounded-3xl border p-0 transition-all shadow-sm">
                {{-- Banner Skeleton --}}
                <x-skeleton variant="panel" class="h-40 rounded-none mb-0" />
                {{-- Form Fields Skeleton --}}
                <div class="p-8 space-y-8">
                    <div class="space-y-2"><x-skeleton variant="title" class="w-20" /><x-skeleton class="h-11 w-full rounded-xl" /></div>
                    <div class="space-y-2"><x-skeleton variant="title" class="w-24" /><x-skeleton class="h-32 w-full rounded-xl" /></div>
                    <div class="space-y-2"><x-skeleton variant="title" class="w-28" /><x-skeleton class="h-11 w-full rounded-xl" /></div>
                    <div class="space-y-2"><x-skeleton variant="title" class="w-32" /><x-skeleton class="h-32 w-full rounded-xl" /></div>
                    <div class="space-y-2"><x-skeleton variant="title" class="w-24" /><x-skeleton class="h-24 w-full rounded-xl" /></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-skeleton class="h-11 w-full rounded-xl" /><x-skeleton class="h-11 w-full rounded-xl" />
                        <x-skeleton class="h-11 w-full rounded-xl" /><x-skeleton class="h-11 w-full rounded-xl" />
                    </div>
                    <div class="flex gap-3 pt-6 border-t border-slate-100 dark:border-white/5">
                        <x-skeleton class="h-12 w-40 rounded-xl" />
                        <x-skeleton class="h-12 w-24 rounded-xl" />
                    </div>
                </div>
            </div>

            {{-- Main Card dengan Animasi Slide Up --}}
            <div x-show="loaded"
                 x-cloak
                 x-transition:enter="transition ease-out duration-600"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-slate-200 shadow-[0_12px_32px_rgba(15,23,42,0.08)]'" 
                 class="overflow-hidden rounded-3xl border transition-all">
                
                <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-sky-600 px-6 py-6 text-white md:px-8">
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-100/80 animate-fade-in-left">Form Input</p>
                    <h3 class="mt-2 text-2xl font-black tracking-tight animate-fade-in-left">Rancang soal latihan yang rapi dan mudah dikelola</h3>
                    <p class="mt-2 text-sm text-indigo-100/90 animate-fade-in-left">Gunakan pilihan kategori yang valid agar data konsisten dengan engine latihan.</p>
                </div>

                <form action="{{ route('admin.practice-questions.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
                    @csrf

                    <div class="grid gap-6">
                        {{-- Kategori - delay 100 --}}
                        <div class="transition-all duration-500 delay-100">
                            <label for="category" class="block text-sm font-semibold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-slate-900'">Kategori <span class="text-red-600">*</span></label>
                            <select id="category" name="category" 
                                :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10' : 'bg-white text-slate-900 border-slate-200'"
                                class="w-full rounded-xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 transition">
                                <option value="">Pilih kategori...</option>
                                <option value="listening" @selected(old('category') === 'listening')>Listening</option>
                                <option value="structure" @selected(old('category') === 'structure')>Structure</option>
                                <option value="reading" @selected(old('category') === 'reading')>Reading</option>
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Passage - delay 200 --}}
                        <div class="transition-all duration-500 delay-200">
                            <label for="passage" class="block text-sm font-semibold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-slate-900'">Passage (Opsional)</label>
                            <textarea id="passage" name="passage" rows="4"
                                :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-500' : 'bg-white text-slate-900 border-slate-200 placeholder-slate-400'"
                                class="w-full rounded-xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 transition"
                                placeholder="Masukkan passage jika ada...">{{ old('passage') }}</textarea>
                            @error('passage')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Audio - delay 300 --}}
                        <div class="transition-all duration-500 delay-300">
                            <label for="audio_path" class="block text-sm font-semibold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-slate-900'">File Audio (Opsional)</label>
                            <input type="file" id="audio_path" name="audio_path" accept="audio/*"
                                :class="$store.theme?.isDark ? 'text-slate-400 border-white/10' : 'text-slate-900 border-slate-200'"
                                class="w-full rounded-xl border px-4 py-3 transition file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700">
                            <p class="mt-1 text-xs text-slate-500">Format: MP3, WAV, OGG (maks. 20MB)</p>
                            @error('audio_path')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Transcript - delay 400 --}}
                        <div class="transition-all duration-500 delay-[400ms]">
                            <label for="audio_transcript" class="block text-sm font-semibold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-slate-900'">Transcript Audio</label>
                            <textarea id="audio_transcript" name="audio_transcript" rows="4"
                                :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-500' : 'bg-white text-slate-900 border-slate-200 placeholder-slate-400'"
                                class="w-full rounded-xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 transition"
                                placeholder="Wajib diisi jika file audio diunggah...">{{ old('audio_transcript') }}</textarea>
                            @error('audio_transcript')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Pertanyaan - delay 500 --}}
                        <div class="transition-all duration-500 delay-[500ms]">
                            <label for="question_text" class="block text-sm font-semibold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-slate-900'">Pertanyaan <span class="text-red-600">*</span></label>
                            <textarea id="question_text" name="question_text" rows="3"
                                :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-500' : 'bg-white text-slate-900 border-slate-200 placeholder-slate-400'"
                                class="w-full rounded-xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 transition"
                                placeholder="Masukkan pertanyaan...">{{ old('question_text') }}</textarea>
                            @error('question_text')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Opsi Jawaban - delay 600 --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 transition-all duration-500 delay-[600ms]">
                            @foreach(['a', 'b', 'c', 'd'] as $opt)
                            <div>
                                <label for="option_{{ $opt }}" class="block text-sm font-semibold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-slate-900'">Opsi {{ strtoupper($opt) }} <span class="text-red-600">*</span></label>
                                <input type="text" id="option_{{ $opt }}" name="option_{{ $opt }}" value="{{ old('option_'.$opt) }}"
                                    :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-600' : 'bg-white text-slate-900 border-slate-200 placeholder-slate-400'"
                                    class="w-full rounded-xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 transition"
                                    placeholder="Masukkan opsi {{ strtoupper($opt) }}...">
                                @error('option_'.$opt)
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            @endforeach
                        </div>

                        {{-- Jawaban Benar - delay 700 --}}
                        <div class="md:w-1/3 transition-all duration-500 delay-[700ms]">
                            <label for="correct_answer" class="block text-sm font-semibold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-red-600'">Jawaban Benar <span class="text-red-600">*</span></label>
                            <select id="correct_answer" name="correct_answer"
                                :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-red-900/30' : 'bg-white text-slate-900 border-red-300'"
                                class="w-full rounded-xl px-4 py-3 focus:border-indigo-500 focus:ring-indigo-500 transition">
                                <option value="">Pilih jawaban benar...</option>
                                <option value="A" @selected(old('correct_answer') === 'A')>A</option>
                                <option value="B" @selected(old('correct_answer') === 'B')>B</option>
                                <option value="C" @selected(old('correct_answer') === 'C')>C</option>
                                <option value="D" @selected(old('correct_answer') === 'D')>D</option>
                            </select>
                            @error('correct_answer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Action Buttons - delay 800 --}}
                    <div class="mt-8 flex flex-wrap gap-3 pt-6 border-t border-slate-100 dark:border-white/5 transition-all duration-500 delay-[800ms]">
                        <button type="submit" class="inline-flex items-center rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 active:scale-95">
                            <i class="fas fa-save mr-2"></i> SIMPAN SOAL
                        </button>
                        <a href="{{ route('admin.practice-questions.index') }}" 
                            :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300'"
                            class="inline-flex items-center rounded-xl border px-8 py-3 text-sm font-bold transition active:scale-95">
                            BATAL
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>