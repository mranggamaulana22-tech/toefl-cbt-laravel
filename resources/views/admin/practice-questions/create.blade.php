<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-0.5 animate-fade-in-down">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Soal Latihan</p>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Tambah Soal Latihan Baru</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Buat soal latihan dengan layout yang konsisten dan modern.</p>
        </div>
    </x-slot>

    <div class="py-8 transition-colors duration-500 min-h-screen"
         :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-slate-50'">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-slate-200 shadow-[0_12px_32px_rgba(15,23,42,0.08)]'"
                 class="overflow-hidden rounded-3xl border transition-all">

                <x-page-banner
                    eyebrow="Form Input"
                    title="Rancang soal latihan yang rapi dan mudah dikelola"
                    subtitle="Gunakan pilihan kategori yang valid agar data konsisten dengan engine latihan."
                />

                <form action="{{ route('admin.practice-questions.store') }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
                    @csrf

                    <div class="grid gap-6">
                        <x-form-field
                            type="select"
                            name="category"
                            label="Kategori"
                            :required="true"
                            :value="old('category')"
                            :options="['listening' => 'Listening', 'structure' => 'Structure', 'reading' => 'Reading']"
                            placeholder="Pilih kategori..."
                        />

                        <x-form-field
                            type="textarea"
                            name="passage"
                            label="Passage (Opsional)"
                            :value="old('passage')"
                            rows="4"
                            placeholder="Masukkan passage jika ada..."
                        />

                        <x-form-field
                            type="file"
                            name="audio_path"
                            label="File Audio (Opsional)"
                            accept="audio/*"
                            hint="Format: MP3, WAV, OGG (maks. 20MB)"
                        />

                        <x-form-field
                            type="textarea"
                            name="audio_transcript"
                            label="Transcript Audio"
                            :value="old('audio_transcript')"
                            rows="4"
                            placeholder="Wajib diisi jika file audio diunggah..."
                        />

                        <x-form-field
                            type="textarea"
                            name="question_text"
                            label="Pertanyaan"
                            :required="true"
                            :value="old('question_text')"
                            rows="3"
                            placeholder="Masukkan pertanyaan..."
                        />

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach(['a', 'b', 'c', 'd'] as $opt)
                                <x-form-field
                                    type="text"
                                    name="option_{{ $opt }}"
                                    label="Opsi {{ strtoupper($opt) }}"
                                    :required="true"
                                    :value="old('option_'.$opt)"
                                    placeholder="Masukkan opsi {{ strtoupper($opt) }}..."
                                />
                            @endforeach
                        </div>

                        <div class="md:w-1/3">
                            <x-form-field
                                type="select"
                                name="correct_answer"
                                label="Jawaban Benar"
                                :required="true"
                                :danger="true"
                                :value="old('correct_answer')"
                                :options="['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']"
                                placeholder="Pilih jawaban benar..."
                            />
                        </div>
                    </div>

                    <x-form-actions
                        submit-label="SIMPAN SOAL"
                        cancel-route="admin.practice-questions.index"
                    />
                </form>
            </div>
        </div>
    </div>
</x-app-layout>