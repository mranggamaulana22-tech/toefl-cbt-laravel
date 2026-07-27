{{--
    Partial: baris tabel satu soal ujian.
    Dipakai di index.blade.php (loop awal) dan dikembalikan sebagai JSON
    setelah update via modal, supaya baris tabel bisa diganti tanpa reload halaman.

    Variabel yang dibutuhkan: $q (instance Question), $no (nomor urut baris)
--}}
<tr class="transition-colors group hover:bg-slate-50 dark:hover:bg-white/[0.02]" id="row-{{ $q->id }}">
    <td class="px-6 py-3 text-sm font-semibold text-slate-500">{{ $no }}</td>
    <td class="px-6 py-3">
        <x-category-badge :category="$q->category" :solid="true" />
    </td>
    <td class="px-6 py-3 text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ Str::limit($q->question_text, 65) }}</td>
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
            <form action="{{ route('questions.destroy', $q->id) }}" method="POST" onsubmit="return confirm('Hapus soal ini?');" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-red-400 bg-red-600 text-white text-xs font-bold transition active:scale-90 shadow-md hover:bg-red-700 hover:border-red-500">
                    HAPUS
                </button>
            </form>
        </div>
    </td>
</tr>