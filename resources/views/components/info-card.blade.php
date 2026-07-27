{{--
    Komponen: <x-info-card>
    Kartu kecil label + value, dipakai di summary grid halaman detail (show.blade.php).

    Cara pakai:
        <x-info-card label="Kategori" :value="ucfirst($practiceQuestion->category)" delay="delay-100" />
        <x-info-card label="Status Audio" value="Ada file audio" color="emerald" :span2="true" delay="delay-300" />

    Props:
    - label (string) : judul kecil di atas
    - value (string) : nilai/isi utama
    - color (string) : warna teks value: 'default' (slate/white) | 'indigo' | 'emerald' | 'slate-muted'
    - span2 (bool)   : true = kartu melebar 2 kolom (xl:col-span-2)
    - delay (string) : kelas tailwind delay animasi, mis. 'delay-100'
--}}
@props(['label' => '', 'value' => '', 'color' => 'default', 'span2' => false, 'delay' => ''])

@php
    $colorClass = match($color) {
        'indigo' => 'text-indigo-700 dark:text-indigo-400',
        'emerald' => 'text-emerald-600',
        'slate-muted' => 'text-slate-500',
        default => 'text-slate-900 dark:text-white',
    };
@endphp

<div class="rounded-2xl border border-slate-200 bg-slate-50 dark:bg-white/5 dark:border-white/5 p-4 transition-all duration-500 {{ $delay }} {{ $span2 ? 'xl:col-span-2' : '' }}">
    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400">{{ $label }}</p>
    <p class="mt-2 text-lg font-black {{ $colorClass }}">{{ $value }}</p>
</div>