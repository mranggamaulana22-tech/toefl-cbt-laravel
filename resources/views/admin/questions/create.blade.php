<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-0.5 animate-fade-in-down">
            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-indigo-500">Bank Soal &mdash; {{ $paketSoal->nama }}</p>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Input Soal Baru</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Tambahkan soal TOEFL baru ke dalam paket ini.</p>
        </div>
    </x-slot>

    <div class="py-8 transition-colors duration-500 min-h-screen"
         :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-slate-50'">

        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-slate-200 shadow-[0_12px_32px_rgba(15,23,42,0.08)]'"
                 class="overflow-hidden rounded-3xl border transition-all">

                <x-page-banner
                    eyebrow="Form Input"
                    title="Rancang soal ujian yang rapi dan mudah dikelola"
                    subtitle="Gunakan pilihan kategori yang valid agar data konsisten dengan engine ujian."
                />

                <form action="{{ route('paket-soal.questions.store', $paketSoal) }}" method="POST" enctype="multipart/form-data" class="p-6 md:p-8">
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
                            type="file"
                            name="audio"
                            label="File Audio (MP3)"
                            accept="audio/*"
                        />

                        <x-form-field
                            type="textarea"
                            name="audio_transcript"
                            label="Transcript Audio"
                            :value="old('audio_transcript')"
                            rows="4"
                            placeholder="Wajib diisi jika file audio dilampirkan."
                            hint="Transcript akan muncul saat siswa mereview hasil latihan."
                        />

                        <x-form-field
                            type="textarea"
                            name="passage"
                            label="Teks Bacaan (Khusus Reading)"
                            :value="old('passage')"
                            rows="4"
                            placeholder="Tempelkan teks bacaan di sini jika soal kategori Reading..."
                        />

                        <x-form-field
                            type="textarea"
                            name="question_text"
                            label="Pertanyaan"
                            :required="true"
                            :value="old('question_text')"
                            rows="2"
                            placeholder="Masukkan isi pertanyaan..."
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
                            />
                        </div>
                    </div>

                    <x-form-actions
                        submit-label="Simpan Soal"
                        cancel-route="paket-soal.questions.index"
                        :cancel-params="$paketSoal->id"
                    />
                </form>
            </div>
        </div>
    </div>
</x-app-layout>