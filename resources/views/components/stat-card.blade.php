{{--
    Komponen: <x-stat-card>
    Kartu statistik untuk dashboard/index (Total Soal, Listening, dst).

    PENTING: komponen ini TIDAK bergantung pada variabel Alpine "loaded" apapun,
    supaya aman dipakai di halaman manapun terlepas dari apa isi x-data induknya
    (mis. crudModal(), atau x-data kosong). Animasi masuk pakai CSS murni.

    Cara pakai:
        <x-stat-card label="Total Soal" :value="$stats['total_questions']" sub="Semua kategori soal" color="slate" :delay="0" />
        <x-stat-card label="Listening" :value="$stats['listening_count']" sub="Soal audio" color="blue" :delay="100" />

    Props:
    - label (string) : judul kecil di atas
    - value (mixed)  : angka besar
    - sub (string)   : teks kecil di bawah
    - color (string) : 'slate' | 'blue' | 'purple' | 'emerald' (warna angka)
    - delay (int)    : delay animasi CSS dalam ms (kelipatan 100 disarankan)
--}}
@props(['label' => '', 'value' => 0, 'sub' => '', 'color' => 'slate', 'delay' => 0])

@php
    $colorClass = $color === 'slate' ? 'text-slate-900 dark:text-white' : "text-{$color}-600";
@endphp

<div
    class="border rounded-2xl p-5 shadow-sm transition-all hover:scale-[1.02] duration-300 animate-fade-in-down
           bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10"
    style="animation-delay: {{ $delay }}ms">
    <p class="text-xs font-bold uppercase tracking-[0.18em] text-slate-400 mb-2">{{ $label }}</p>
    <p class="text-3xl font-black {{ $colorClass }}">{{ $value }}</p>
    <p class="text-xs text-slate-500 mt-1">{{ $sub }}</p>
</div>