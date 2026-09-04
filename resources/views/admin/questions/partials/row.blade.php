{{--
    Partial: baris tabel satu soal ujian.
    Dipakai di index.blade.php (loop awal) dan dikembalikan sebagai JSON
    setelah update via modal, supaya baris tabel bisa diganti tanpa reload.

    Variabel yang dibutuhkan: $q (instance Question), $no (nomor urut baris), $paketSoal (paket induk)
--}}
<tr class="transition-colors group hover:bg-slate-50 dark:hover:bg-white/[0.02]" id="row-{{ $q->id }}">
    <td class="px-6 py-3 text-sm font-semibold text-slate-500">{{ $no }}</td>
    <td class="px-6 py-3">
        <x-category-badge :category="$q->category" :solid="true" />
    </td>
    <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ Str::limit($q->question_text, 65) }}</td>
    <td class="px-6 py-3">
        @if ($q->category !== 'listening')
            <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-white/5 px-2.5 py-1 text-xs font-bold text-slate-400 dark:text-slate-500">
                -
            </span>
        @elseif ($q->audio_path)
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
                <audio src="{{ Storage::disk('public')->url($q->audio_path) }}" preload="none"></audio>
            </div>
        @elseif ($q->audio_transcript)
            <button type="button"
                onclick="generateExamQuestionAudio({{ $paketSoal->id }}, {{ $q->id }}, {{ $no }}, this)"
                class="inline-flex items-center gap-1.5 rounded-lg bg-amber-50 dark:bg-amber-500/10 px-2.5 py-1.5 text-xs font-bold text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-500/20 transition">
                <i class="fas fa-bolt"></i> Generate Audio
            </button>
        @else
            <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-white/5 px-2.5 py-1 text-xs font-bold text-slate-500 dark:text-slate-500">
                Tanpa transkrip
            </span>
        @endif
    </td>
    <td class="px-6 py-3 text-center">
        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-bold text-sm bg-slate-100 text-slate-700 dark:bg-indigo-500 dark:text-white">
            {{ $q->correct_answer }}
        </span>
    </td>
    <td class="px-6 py-3 text-center">
        <div class="inline-flex gap-2">
            <button type="button"
                @click="openViewModal({{ $q->id }})"
                class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-slate-300 bg-white text-slate-700 text-xs font-bold transition active:scale-90 hover:bg-slate-50 dark:bg-white/5 dark:text-slate-300 dark:border-white/10 dark:hover:bg-white/10">
                LIHAT
            </button>
            <button type="button"
                @click="openEditModal({{ $q->id }}, { row_no: {{ $no }} })"
                class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-blue-400 bg-blue-600 text-white text-xs font-bold transition active:scale-90 shadow-md hover:bg-blue-700 hover:border-blue-500">
                EDIT
            </button>
            <form action="{{ route('paket-soal.questions.destroy', [$paketSoal, $q->id]) }}" method="POST" class="inline" onsubmit="return false;">
                @csrf @method('DELETE')
                <button type="button" onclick="return confirmAdminDelete(this.form)"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-red-400 bg-red-600 text-white text-xs font-bold transition active:scale-90 shadow-md hover:bg-red-700 hover:border-red-500">
                    HAPUS
                </button>
            </form>
        </div>
    </td>
</tr>