<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-0.5 animate-fade-in-down">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Bank Soal</p>
            {{-- Skeleton Header Title --}}
            <template x-if="!loaded">
                <div class="space-y-2">
                    <x-skeleton variant="title" class="h-8 w-48" />
                    <x-skeleton variant="title" class="h-4 w-64 opacity-50" />
                </div>
            </template>
            <div x-show="loaded">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Input Soal Baru</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Tambahkan soal TOEFL baru ke dalam database sistem.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-12 transition-colors duration-500 min-h-screen" 
         :class="$store.theme && $store.theme.isDark ? 'bg-[#0b0d13]' : 'bg-gray-50'"
         x-data="{ loaded: false }" 
         x-init="setTimeout(() => loaded = true, 600)">
        
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-gray-100 shadow-sm'" 
                 class="p-8 sm:rounded-xl border transition-all">
                
                {{-- BANNER GRADIENT (Always Visible or Custom Skeleton) --}}
                <div class="bg-gradient-to-r from-indigo-700 via-indigo-600 to-sky-600 px-6 py-6 text-white md:px-8 rounded-2xl mb-8">
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-indigo-100/80">Form Input</p>
                    <h3 class="mt-2 text-2xl font-black tracking-tight">Rancang soal latihan yang rapi dan mudah dikelola</h3>
                </div>

                {{-- SKELETON FORM: Muncul saat !loaded --}}
                <div x-show="!loaded" class="space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2"><x-skeleton variant="title" class="w-20" /><x-skeleton class="h-11 w-full" /></div>
                        <div class="space-y-2"><x-skeleton variant="title" class="w-24" /><x-skeleton class="h-11 w-full" /></div>
                    </div>
                    <div class="space-y-2"><x-skeleton variant="title" class="w-32" /><x-skeleton class="h-32 w-full" /></div>
                    <div class="space-y-2"><x-skeleton variant="title" class="w-32" /><x-skeleton class="h-32 w-full" /></div>
                    <div class="space-y-2"><x-skeleton variant="title" class="w-28" /><x-skeleton class="h-20 w-full" /></div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-skeleton class="h-11 w-full" /><x-skeleton class="h-11 w-full" />
                        <x-skeleton class="h-11 w-full" /><x-skeleton class="h-11 w-full" />
                    </div>
                    <div class="flex gap-3 pt-6 border-t border-slate-100 dark:border-white/5">
                        <x-skeleton class="h-14 flex-1 rounded-xl" />
                        <x-skeleton class="h-14 w-32 rounded-xl" />
                    </div>
                </div>

                {{-- REAL FORM: Muncul saat loaded --}}
                <form x-show="loaded"
                      x-transition:enter="transition ease-out duration-600"
                      x-transition:enter-start="opacity-0 translate-y-12"
                      x-transition:enter-end="opacity-100 translate-y-0"
                      action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8" x-cloak>
                    @csrf
                    
                    {{-- Row 1: Kategori & Audio --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 transition-all duration-500 delay-100">
                        <div>
                            <label class="block font-bold mb-2" :class="$store.theme && $store.theme.isDark ? 'text-slate-300' : 'text-gray-700'">Kategori</label>
                            <select name="category" id="category" 
                                :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-white/10' : 'bg-white text-gray-900 border-gray-300'"
                                class="w-full rounded-lg transition focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="listening">🎧 Listening</option>
                                <option value="structure">📝 Structure</option>
                                <option value="reading">📖 Reading</option>
                            </select>
                        </div>
                        
                        <div id="audio-input">
                            <label class="block font-bold mb-2" :class="$store.theme && $store.theme.isDark ? 'text-slate-300' : 'text-gray-700'">File Audio (MP3)</label>
                            <input type="file" name="audio" 
                                :class="$store.theme && $store.theme.isDark ? 'text-slate-400 border-white/10' : 'text-gray-600 border-gray-300'"
                                class="w-full border rounded-lg p-2 file:mr-4 file:py-1.5 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-bold 
                                       file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 transition">
                            @error('audio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Row 2: Transcript --}}
                    <div class="transition-all duration-500 delay-200">
                        <label class="block font-bold mb-2" :class="$store.theme && $store.theme.isDark ? 'text-slate-300' : 'text-gray-700'">Transcript Audio</label>
                        <textarea name="audio_transcript" rows="4" 
                            :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-600' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg transition focus:ring-2 focus:ring-indigo-500" 
                            placeholder="Wajib diisi jika file audio dilampirkan.">{{ old('audio_transcript') }}</textarea>
                        <p class="mt-1.5 text-[11px]" :class="$store.theme && $store.theme.isDark ? 'text-slate-500' : 'text-gray-500'">💡 Transcript akan muncul saat siswa mereview hasil latihan.</p>
                    </div>

                    {{-- Row 3: Reading Passage --}}
                    <div class="transition-all duration-500 delay-300">
                        <label class="block font-bold mb-2" :class="$store.theme && $store.theme.isDark ? 'text-slate-300' : 'text-gray-700'">Teks Bacaan (Khusus Reading)</label>
                        <textarea name="passage" rows="4" 
                            :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-600' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg transition focus:ring-2 focus:ring-indigo-500" 
                            placeholder="Tempelkan teks bacaan di sini jika soal kategori Reading...">{{ old('passage') }}</textarea>
                    </div>

                    {{-- Row 4: Pertanyaan --}}
                    <div class="transition-all duration-500 delay-400">
                        <label class="block font-bold mb-2" :class="$store.theme && $store.theme.isDark ? 'text-slate-300' : 'text-gray-700'">Pertanyaan</label>
                        <textarea name="question_text" rows="2" required 
                            :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-600' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg transition focus:ring-2 focus:ring-indigo-500" 
                            placeholder="Masukkan isi pertanyaan..."></textarea>
                    </div>

                    {{-- Row 5: Opsi Jawaban --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 transition-all duration-500 delay-500">
                        @foreach(['a', 'b', 'c', 'd'] as $opt)
                            <div class="group">
                                <input type="text" name="option_{{ $opt }}" placeholder="Opsi {{ strtoupper($opt) }}" required 
                                    value="{{ old('option_'.$opt) }}" 
                                    :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-600' : 'bg-white border-gray-300 text-gray-900'"
                                    class="w-full rounded-lg transition group-hover:border-indigo-400 focus:ring-2 focus:ring-indigo-500">
                            </div>
                        @endforeach
                    </div>

                    {{-- Row 6: Correct Answer --}}
                    <div class="md:w-1/3 transition-all duration-500 delay-600">
                        <label class="block font-bold text-red-600 mb-2">Jawaban Benar</label>
                        <select name="correct_answer" 
                            :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-red-900/30' : 'bg-white text-slate-900 border-red-300'"
                            class="w-full rounded-lg transition focus:ring-2 focus:ring-red-500">
                            <option value="A">Opsi A</option>
                            <option value="B">Opsi B</option>
                            <option value="C">Opsi C</option>
                            <option value="D">Opsi D</option>
                        </select>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-3 pt-6 border-t border-slate-100 dark:border-white/5 transition-all duration-500 delay-700">
                        <button type="submit" class="flex-1 px-8 py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold shadow-lg shadow-indigo-600/20 transition-all active:scale-95 uppercase tracking-widest text-sm">
                            <i class="fas fa-save mr-2"></i> Simpan Soal
                        </button>
                        <a href="{{ route('questions.index') }}" 
                            :class="$store.theme && $store.theme.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10 border-white/5' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 border-transparent'"
                            class="px-8 py-4 rounded-xl font-bold transition-all text-sm uppercase tracking-widest text-center active:scale-95 border">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>