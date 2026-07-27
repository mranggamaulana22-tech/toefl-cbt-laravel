{{--
    Partial: isi form edit soal latihan — KHUSUS untuk modal.
    TIDAK boleh ada <x-app-layout>, navbar, atau page-banner di sini.
    Modal (index.blade.php) sudah menyediakan bingkai + judul sendiri.
--}}
<form
    action="{{ route('admin.practice-questions.update', $practiceQuestion->id) }}"
    method="POST"
    enctype="multipart/form-data"
    @submit.prevent="submitEditForm($event, {{ $practiceQuestion->id }})"
>
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-form-field
            type="select"
            name="category"
            label="Kategori"
            :required="true"
            :value="old('category', $practiceQuestion->category)"
            :options="['listening' => 'Listening', 'structure' => 'Structure', 'reading' => 'Reading']"
        />

        <x-form-field
            type="select"
            name="correct_answer"
            label="Jawaban Benar"
            :required="true"
            :value="old('correct_answer', $practiceQuestion->correct_answer)"
            :options="['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D']"
        />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        @foreach(['a', 'b', 'c', 'd'] as $opt)
            <x-form-field
                type="text"
                name="option_{{ $opt }}"
                label="Opsi {{ strtoupper($opt) }}"
                :required="true"
                :value="old('option_'.$opt, $practiceQuestion->{'option_'.$opt})"
            />
        @endforeach
    </div>

    <div class="mt-4">
        <x-form-field
            type="textarea"
            name="question_text"
            label="Pertanyaan"
            :required="true"
            :value="old('question_text', $practiceQuestion->question_text)"
            rows="4"
        />
    </div>

    <div class="mt-4">
        <x-form-field
            type="textarea"
            name="passage"
            label="Passage (Opsional)"
            :value="old('passage', $practiceQuestion->passage)"
            rows="4"
            placeholder="Kosongkan jika tidak ada..."
        />
    </div>

    <div class="mt-4">
        <x-form-field
            type="file"
            name="audio_path"
            label="Ganti Audio (Opsional)"
            accept="audio/*"
        >
            @if($practiceQuestion->audio_path)
                <p class="text-xs mt-1 text-slate-500">File saat ini: {{ basename($practiceQuestion->audio_path) }}</p>
            @endif
        </x-form-field>
    </div>

    <div class="mt-4">
        <x-form-field
            type="textarea"
            name="audio_transcript"
            label="Transcript Audio"
            :value="old('audio_transcript', $practiceQuestion->audio_transcript)"
            rows="4"
            placeholder="Wajib jika ada audio"
        />
    </div>

    <div class="mt-6 flex gap-2 pt-4 border-t border-slate-100 dark:border-white/10">
        <button type="submit"
            :disabled="isSubmitting"
            class="flex-1 inline-flex items-center justify-center rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700 disabled:opacity-50">
            <span x-show="!isSubmitting">Simpan Perubahan</span>
            <span x-show="isSubmitting">Menyimpan...</span>
        </button>
        <button type="button" @click="closeModal()"
            :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
            class="px-5 py-2.5 rounded-lg font-semibold text-sm transition">
            Batal
        </button>
    </div>
</form>