{{--
    Partial: baris tabel satu soal latihan.
    Gaya disamakan dengan questions/partials/row.blade.php: badge solid, tombol solid uppercase.
    Variabel yang dibutuhkan: $question (instance PracticeQuestion), $no (nomor urut baris)
--}}
<tr class="transition-colors group hover:bg-slate-50 dark:hover:bg-white/[0.02]" id="row-{{ $question->id }}">
    <td class="px-6 py-4 text-sm font-semibold text-slate-500">{{ $no ?? $question->id }}</td>
    <td class="px-6 py-4">
        <x-category-badge :category="$question->category" :solid="true" />
    </td>
    <td class="px-6 py-4">
        <div class="max-w-2xl text-slate-700 dark:text-slate-300 line-clamp-2">{{ $question->question_text }}</div>
    </td>
    <td class="px-6 py-4">
        @if ($question->category !== 'listening')
            <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-white/5 px-2.5 py-1 text-xs font-bold text-slate-400 dark:text-slate-500">
                -
            </span>
        @elseif ($question->audio_path)
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-full bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 text-xs font-bold text-emerald-700 dark:text-emerald-400">
                    <i class="fas fa-check-circle mr-1"></i> Siap
                </span>
                <button type="button"
                    onclick="this.nextElementSibling.play()"
                    class="text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition"
                    title="Putar preview">
                    <i class="fas fa-play-circle"></i>
                </button>
                <audio src="{{ Storage::disk('public')->url($question->audio_path) }}" preload="none"></audio>
            </div>
        @elseif ($question->audio_transcript)
            <button type="button"
                onclick="generateQuestionAudio({{ $question->id }}, {{ $no ?? $question->id }}, this)"
                class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 dark:bg-amber-500/10 px-2.5 py-1.5 text-xs font-bold text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-500/20 transition">
                <i class="fas fa-bolt"></i> Generate Audio
            </button>
        @else
            <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-white/5 px-2.5 py-1 text-xs font-bold text-slate-500 dark:text-slate-500">
                Tanpa transkrip
            </span>
        @endif
    </td>
    <td class="px-6 py-4">
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-bold text-sm bg-slate-100 text-slate-700 dark:bg-indigo-500 dark:text-white">
            {{ $question->correct_answer }}
        </span>
    </td>
    <td class="px-6 py-4 text-center">
        <div class="inline-flex gap-2">
            <button type="button"
                @click="openViewModal({{ $question->id }})"
                class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-slate-300 bg-white text-slate-700 text-xs font-bold transition active:scale-90 hover:bg-slate-50 dark:bg-white/5 dark:text-slate-300 dark:border-white/10 dark:hover:bg-white/10">
                LIHAT
            </button>
            <button type="button"
                @click="openEditModal({{ $question->id }}, { row_no: {{ $no ?? $question->id }} })"
                class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-blue-400 bg-blue-600 text-white text-xs font-bold transition active:scale-90 shadow-md hover:bg-blue-700 hover:border-blue-500">
                EDIT
            </button>
            <form action="{{ route('admin.practice-questions.destroy', $question) }}" method="POST" class="inline" onsubmit="return false;">
                @csrf @method('DELETE')
                <button type="button" onclick="return confirmAdminDelete(this.form)"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-red-400 bg-red-600 text-white text-xs font-bold transition active:scale-90 shadow-md hover:bg-red-700 hover:border-red-500">
                    HAPUS
                </button>
            </form>
        </div>
    </td>
</tr>