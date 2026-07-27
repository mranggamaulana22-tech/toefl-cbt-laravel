{{--
    Komponen: <x-row-actions>
    Tombol Edit + Hapus per baris tabel, dengan form delete + confirm dialog.

    Cara pakai:
        <x-row-actions
            :edit-route="route('admin.practice-questions.edit', $question)"
            :delete-route="route('admin.practice-questions.destroy', $question)"
            confirm-message="Yakin ingin menghapus soal latihan ini?"
        />

        Dengan tombol "Lihat" tambahan (opsional):
        <x-row-actions
            view-route="[url show]"
            edit-route="[url edit]"
            delete-route="[url destroy]"
        />

    Props:
    - viewRoute (string|null)   : url tombol "Lihat" (opsional, disembunyikan jika null)
    - editRoute (string)        : url tombol "Edit"
    - deleteRoute (string)      : url form delete (method DELETE)
    - confirmMessage (string)   : teks konfirmasi sebelum hapus
    - variant (string)          : 'soft' (default, gaya practice-questions) | 'solid' (gaya questions, tombol besar berwarna solid)
--}}
@props([
    'viewRoute' => null,
    'editRoute',
    'deleteRoute',
    'confirmMessage' => 'Yakin ingin menghapus data ini?',
    'variant' => 'soft',
])

@if($variant === 'solid')
    <div class="inline-flex gap-2 transition-all duration-300">
        <a href="{{ $editRoute }}" class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-blue-400 bg-blue-600 text-white text-xs font-bold transition active:scale-90 shadow-md hover:bg-blue-700 hover:border-blue-500">
            EDIT
        </a>
        <form action="{{ $deleteRoute }}" method="POST" onsubmit="return confirm('{{ $confirmMessage }}');" class="inline">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center px-3 py-1.5 rounded-lg border-2 border-red-400 bg-red-600 text-white text-xs font-bold transition active:scale-90 shadow-md hover:bg-red-700 hover:border-red-500">
                HAPUS
            </button>
        </form>
    </div>
@else
    <div class="inline-flex items-center gap-2">
        @if($viewRoute)
            <a href="{{ $viewRoute }}" :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10' : 'bg-slate-100 text-slate-700 hover:bg-slate-200'" class="inline-flex items-center rounded-lg px-3 py-2 text-xs font-semibold transition active:scale-90">
                Lihat
            </a>
        @endif
        <a href="{{ $editRoute }}" class="inline-flex items-center rounded-lg bg-blue-50 dark:bg-blue-500/10 px-3 py-2 text-xs font-semibold text-blue-700 dark:text-blue-400 transition hover:bg-blue-100 active:scale-90">
            Edit
        </a>
        <form action="{{ $deleteRoute }}" method="POST" class="inline" onsubmit="return confirm('{{ $confirmMessage }}');">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center rounded-lg bg-red-50 dark:bg-red-500/10 px-3 py-2 text-xs font-semibold text-red-700 dark:text-red-400 transition hover:bg-red-100 active:scale-90">
                Hapus
            </button>
        </form>
    </div>
@endif