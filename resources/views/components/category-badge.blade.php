{{--
    Komponen: <x-category-badge>
    Badge kategori soal (Listening/Structure/Reading) dengan warna otomatis.

    Cara pakai:
        <x-category-badge :category="$question->category" />
        Versi solid (gaya questions/index): <x-category-badge :category="$q->category" solid="true" />

    Props:
    - category (string) : 'listening' | 'structure' | 'reading'
    - solid (bool)       : true = latar warna solid + teks putih (gaya questions/index),
                            false = latar soft/pastel (gaya practice-questions/index, default)
--}}
@props(['category' => '', 'solid' => false])

@php
    $map = [
        'listening' => ['soft' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400', 'solid' => 'bg-blue-600 text-white'],
        'structure' => ['soft' => 'bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400', 'solid' => 'bg-purple-600 text-white'],
        'reading'   => ['soft' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400', 'solid' => 'bg-emerald-600 text-white'],
    ];
    $classes = $map[$category]['soft'] ?? 'bg-slate-100 text-slate-700';
    if ($solid) {
        $classes = $map[$category]['solid'] ?? 'bg-slate-600 text-white';
    }
@endphp

<span {{ $attributes->class(["inline-flex items-center rounded-full px-2.5 py-1 text-xs font-bold tracking-wide transition-transform group-hover:scale-105", $classes]) }}>
    {{ ucfirst($category) }}
</span>