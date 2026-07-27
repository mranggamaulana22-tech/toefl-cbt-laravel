{{--
    Partial: isi detail soal ujian — KHUSUS untuk modal.
    TIDAK boleh ada <x-app-layout>, navbar, atau page-banner di sini.
--}}

{{-- Meta info ringkas dalam 1 baris (bukan card besar) --}}
<div class="flex flex-wrap items-center gap-x-5 gap-y-2 mb-5 pb-4 border-b border-slate-100 dark:border-white/10 text-sm">
    <div class="flex items-center gap-1.5">
        <span class="text-slate-400 text-xs uppercase tracking-wide">Kategori</span>
        <span class="font-semibold text-slate-800 dark:text-slate-200">{{ ucfirst($question->category) }}</span>
    </div>
    <div class="flex items-center gap-1.5">
        <span class="text-slate-400 text-xs uppercase tracking-wide">Jawaban Benar</span>
        <span class="inline-flex items-center justify-center w-5 h-5 rounded bg-indigo-600 text-white text-xs font-bold">{{ $question->correct_answer }}</span>
    </div>
    <div class="flex items-center gap-1.5">
        <span class="text-slate-400 text-xs uppercase tracking-wide">Audio</span>
        <span class="font-semibold {{ $question->audio_path ? 'text-emerald-600' : 'text-slate-400' }}">
            {{ $question->audio_path ? 'Ada' : 'Tidak ada' }}
        </span>
    </div>
</div>

{{-- Pertanyaan — konten paling penting, ditonjolkan --}}
<div class="mb-5">
    <p class="text-xs font-semibold uppercase tracking-wide text-indigo-500 mb-2">Pertanyaan</p>
    <p class="whitespace-pre-line leading-relaxed text-slate-800 dark:text-slate-200 text-base">{{ $question->question_text }}</p>
</div>

@if($question->passage)
    <div class="mb-5 rounded-lg bg-slate-50 dark:bg-white/5 p-4">
        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Teks Bacaan</p>
        <p class="whitespace-pre-line leading-relaxed text-slate-700 dark:text-slate-300 text-sm">{{ $question->passage }}</p>
    </div>
@endif

@if($question->audio_path)
    <div class="mb-5">
        <p class="text-xs font-semibold uppercase tracking-wide text-indigo-500 mb-2">Audio & Transcript</p>
        <audio controls class="w-full mb-2">
            <source src="{{ asset('storage/' . $question->audio_path) }}" type="audio/mpeg">
        </audio>
        @if($question->audio_transcript)
            <p class="whitespace-pre-line leading-relaxed text-slate-800 dark:text-slate-200 text-sm rounded-lg bg-slate-50 dark:bg-white/5 p-4">{{ $question->audio_transcript }}</p>
        @endif
    </div>
@endif

{{-- Pilihan jawaban --}}
<div class="mb-2">
    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400 mb-2">Pilihan Jawaban</p>
    <div class="space-y-1.5">
        @foreach(['a', 'b', 'c', 'd'] as $opt)
            <div class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm
                {{ strtoupper($opt) === $question->correct_answer
                    ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-800 dark:text-emerald-400'
                    : 'bg-slate-50 dark:bg-white/5 text-slate-700 dark:text-slate-300' }}">
                <span class="font-bold w-5">{{ strtoupper($opt) }}</span>
                <span class="flex-1">{{ $question->{'option_'.$opt} }}</span>
                @if(strtoupper($opt) === $question->correct_answer)
                    <span class="text-xs font-semibold">✓</span>
                @endif
            </div>
        @endforeach
    </div>
</div>

<div class="mt-5 flex justify-end pt-4 border-t border-slate-100 dark:border-white/10">
    <button type="button" @click="closeModal()"
        :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
        class="px-5 py-2.5 rounded-lg font-semibold text-sm transition">
        Tutup
    </button>
</div>