{{--
    Komponen: <x-form-actions>

    Tujuan: Baris tombol submit + batal di footer form (dengan border-top),
    supaya markup tombol tidak ditulis ulang di tiap halaman create/edit.

    Cara pakai - CREATE:
        <x-form-actions
            submit-label="SIMPAN SOAL"
            cancel-route="admin.practice-questions.index"
        />

    Cara pakai - EDIT (label beda):
        <x-form-actions
            submit-label="SIMPAN PERUBAHAN"
            cancel-route="admin.practice-questions.index"
        />

    Cara pakai - dengan parameter route (mis. cancel ke show detail):
        <x-form-actions
            submit-label="SIMPAN PERUBAHAN"
            cancel-route="admin.practice-questions.show"
            :cancel-params="$practiceQuestion->id"
        />

    Props:
    - submitLabel (string)  : teks tombol submit
    - submitIcon (string)   : kelas ikon FontAwesome untuk tombol submit (default 'fa-save')
    - cancelLabel (string)  : teks tombol batal (default 'BATAL')
    - cancelRoute (string)  : nama route tujuan tombol batal
    - cancelParams (mixed)  : parameter route batal (opsional)
    - delay (string)        : kelas tailwind delay animasi, mis. 'delay-[800ms]' (opsional)
--}}

@props([
    'submitLabel' => 'SIMPAN',
    'submitIcon' => 'fa-save',
    'cancelLabel' => 'BATAL',
    'cancelRoute' => null,
    'cancelParams' => null,
    'delay' => '',
])

<div class="mt-8 flex flex-wrap gap-3 pt-6 border-t border-slate-100 dark:border-white/5 transition-all duration-500 {{ $delay }}">
    <button type="submit" class="inline-flex items-center rounded-xl bg-indigo-600 px-8 py-3 text-sm font-bold text-white transition hover:bg-indigo-700 shadow-lg shadow-indigo-600/20 active:scale-95">
        @if($submitIcon)
            <i class="fas {{ $submitIcon }} mr-2"></i>
        @endif
        {{ $submitLabel }}
    </button>

    @if($cancelRoute)
        <a href="{{ route($cancelRoute, $cancelParams) }}"
            :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300 border-white/10 hover:bg-white/10' : 'bg-white border-slate-200 text-slate-700 hover:border-slate-300'"
            class="inline-flex items-center rounded-xl border px-8 py-3 text-sm font-bold transition active:scale-95">
            {{ $cancelLabel }}
        </a>
    @endif
</div>