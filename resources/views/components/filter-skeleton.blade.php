{{--
    Komponen: <x-filter-skeleton>
    Skeleton loader untuk filter card (search + beberapa field + tombol aksi),
    dipakai di halaman index yang punya form filter (gradebook, practice-history, students).

    Cara pakai (default - 5 kolom seperti gradebook/practice-history):
        <x-filter-skeleton />

    Cara pakai custom jumlah kolom (mis. students yang cuma 4 kolom):
        <x-filter-skeleton :columns="4" :search-span="2" />

    Props:
    - columns (int)    : jumlah kolom grid pada md breakpoint (default 5)
    - searchSpan (int) : md:col-span untuk kolom search (default 2)
    - extraFields (int): jumlah field skeleton kecil tambahan selain search (default columns - searchSpan - 1)
--}}
@props(['columns' => 5, 'searchSpan' => 2, 'extraFields' => null])

@php
    $extra = $extraFields ?? max($columns - $searchSpan - 1, 0);
@endphp

<div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-gray-100'"
     class="p-4 mb-6 sm:rounded-xl border transition-all shadow-sm">
    <div class="grid grid-cols-1 md:grid-cols-{{ $columns }} gap-3 items-end">
        <div class="md:col-span-{{ $searchSpan }} space-y-2">
            <x-skeleton variant="title" class="w-32" />
            <x-skeleton class="h-10 w-full rounded-lg" />
        </div>
        @for ($i = 0; $i < $extra; $i++)
            <div class="space-y-2">
                <x-skeleton variant="title" class="w-20" />
                <x-skeleton class="h-10 w-full rounded-lg" />
            </div>
        @endfor
        <div><x-skeleton class="h-10 w-full rounded-lg" /></div>
    </div>
</div>