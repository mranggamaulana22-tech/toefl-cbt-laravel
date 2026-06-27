<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 animate-fade-in-down">
            {{-- Skeleton Header Title --}}
            <template x-if="!loaded">
                <x-skeleton variant="title" class="h-8 w-48" />
            </template>
            <h2 x-show="loaded" class="font-semibold text-xl leading-tight" :class="$store.theme?.isDark ? 'text-white' : 'text-gray-800'">
                Edit Soal TOEFL
            </h2>

            <a href="{{ route('questions.index') }}" 
               class="inline-block px-4 py-2 bg-gray-700 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 transition active:scale-95">
                Kembali ke Daftar Soal
            </a>
        </div>
    </x-slot>

    <div class="py-12 transition-colors duration-500 min-h-screen" 
         :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-gray-50'"
         x-data="{ loaded: false }" 
         {{-- Kita set 600ms agar skeleton terlihat berdenyut premium --}}
         x-init="setTimeout(() => loaded = true, 600)">
        
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-gray-100'" 
                 class="p-8 shadow-sm sm:rounded-xl border transition-all">
                
                {{-- SKELETON FORM: Muncul saat !loaded --}}
                <div x-show="!loaded" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <x-skeleton variant="title" class="w-20" />
                            <x-skeleton class="h-11 w-full" />
                        </div>
                        <div class="space-y-2">
                            <x-skeleton variant="title" class="w-24" />
                            <x-skeleton class="h-11 w-full" />
                        </div>
                    </div>
                    <div class="space-y-2">
                        <x-skeleton variant="title" class="w-32" />
                        <x-skeleton class="h-32 w-full" />
                    </div>
                    <div class="space-y-2">
                        <x-skeleton variant="title" class="w-28" />
                        <x-skeleton class="h-20 w-full" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-skeleton class="h-11 w-full" />
                        <x-skeleton class="h-11 w-full" />
                        <x-skeleton class="h-11 w-full" />
                        <x-skeleton class="h-11 w-full" />
                    </div>
                    <div class="flex gap-3 pt-6 border-t border-slate-100 dark:border-white/5">
                        <x-skeleton class="h-12 w-40 rounded-xl" />
                        <x-skeleton class="h-12 w-24 rounded-xl" />
                    </div>
                </div>

                {{-- REAL FORM: Muncul saat loaded --}}
                <form x-show="loaded" 
                      x-transition:enter="transition ease-out duration-600"
                      x-transition:enter-start="opacity-0 translate-y-4"
                      x-transition:enter-end="opacity-100 translate-y-0"
                      action="{{ route('questions.update', $question->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Row 1: Kategori & Audio --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 transition-all duration-700 delay-100">
                        <div>
                            <label class="block font-bold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-gray-700'">Kategori</label>
                            <select name="category" id="category" 
                                :class="$store.theme?.isDark ? '!bg-[#1e293b] border-white/10 text-white' : 'bg-white border-gray-300 text-gray-900'"
                                class="w-full rounded-lg transition focus:ring-indigo-500">
                                <option value="listening" @selected(old('category', $question->category) === 'listening')>Listening</option>
                                <option value="structure" @selected(old('category', $question->category) === 'structure')>Structure</option>
                                <option value="reading" @selected(old('category', $question->category) === 'reading')>Reading</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-bold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-gray-700'">File Audio (MP3)</label>
                            <input type="file" name="audio" 
                                :class="$store.theme?.isDark ? 'text-slate-400' : 'text-gray-600'"
                                class="w-full border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            @if($question->audio_path)
                                <p class="text-xs mt-2 animate-pulse" :class="$store.theme?.isDark ? 'text-emerald-500' : 'text-emerald-600'">
                                    <i class="fas fa-check-circle mr-1"></i> Audio saat ini: {{ basename($question->audio_path) }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Row 2: Transcript --}}
                    <div class="transition-all duration-700 delay-200">
                        <label class="block font-bold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-gray-700'">Transcript Audio</label>
                        <textarea name="audio_transcript" rows="4" 
                            :class="$store.theme?.isDark ? '!bg-[#1e293b] border-white/10 text-white placeholder-slate-500' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg transition focus:ring-indigo-500" 
                            placeholder="Wajib diisi jika soal memiliki audio.">{{ old('audio_transcript', $question->audio_transcript) }}</textarea>
                    </div>

                    {{-- Row 3: Reading Passage --}}
                    <div class="transition-all duration-700 delay-300">
                        <label class="block font-bold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-gray-700'">Teks Bacaan (Hanya untuk Reading)</label>
                        <textarea name="passage" rows="4" 
                            :class="$store.theme?.isDark ? '!bg-[#1e293b] border-white/10 text-white' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg transition focus:ring-indigo-500">{{ old('passage', $question->passage) }}</textarea>
                    </div>

                    {{-- Row 4: Question Text --}}
                    <div class="transition-all duration-700 delay-400">
                        <label class="block font-bold mb-2" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-gray-700'">Pertanyaan</label>
                        <textarea name="question_text" rows="2" required 
                            :class="$store.theme?.isDark ? '!bg-[#1e293b] border-white/10 text-white' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg transition focus:ring-indigo-500">{{ old('question_text', $question->question_text) }}</textarea>
                    </div>

                    {{-- Row 5: Options --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 transition-all duration-700 delay-500">
                        @foreach(['a', 'b', 'c', 'd'] as $opt)
                            <input type="text" name="option_{{ $opt }}" placeholder="Opsi {{ strtoupper($opt) }}" required 
                                value="{{ old('option_'.$opt, $question->{'option_'.$opt}) }}" 
                                :class="$store.theme?.isDark ? '!bg-[#1e293b] border-white/10 text-white placeholder-slate-500' : 'bg-white border-gray-300 text-gray-900'"
                                class="rounded-lg transition focus:ring-indigo-500 hover:border-indigo-400">
                        @endforeach
                    </div>

                    {{-- Row 6: Correct Answer --}}
                    <div class="md:w-1/3 transition-all duration-700 delay-600">
                        <label class="block font-bold text-red-600 mb-2">Jawaban Benar</label>
                        <select name="correct_answer" 
                            :class="$store.theme?.isDark ? '!bg-[#1e293b] border-red-900/30 text-white' : 'bg-white border-red-300 text-gray-900'"
                            class="w-full rounded-lg transition focus:ring-red-500">
                            @foreach(['A', 'B', 'C', 'D'] as $ans)
                                <option value="{{ $ans }}" @selected(old('correct_answer', $question->correct_answer) === $ans)>{{ $ans }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-3 pt-6 border-t border-slate-100 dark:border-white/5 transition-all duration-700 delay-700">
                        <button type="submit" class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-lg shadow-blue-600/20 transition-all active:scale-95">
                            SIMPAN PERUBAHAN
                        </button>
                        <a href="{{ route('questions.index') }}" 
                            :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-8 py-3 rounded-xl font-bold transition-all active:scale-95">
                            BATAL
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>