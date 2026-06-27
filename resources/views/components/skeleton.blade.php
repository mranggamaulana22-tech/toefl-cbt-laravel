@props([
    'variant' => 'block',
    'lines' => 1,
    'class' => '',
])

@php
    // Base style untuk efek pulse dan warna
    $baseClass = 'animate-pulse bg-slate-200/80 dark:bg-slate-700/70';

    // Definisi ukuran berdasarkan varian
    $variantClass = match ($variant) {
        'avatar'    => 'h-12 w-12 rounded-full',
        'title'     => 'h-5 w-40 rounded-xl',
        'chip'      => 'h-6 w-20 rounded-full',
        'table-row' => 'w-full rounded-xl',
        'stat-card' => 'h-32 w-full rounded-2xl',
        'panel'     => 'w-full rounded-3xl',
        default     => 'h-4 w-full rounded-xl',
    };
@endphp

{{-- 1. VARIAN TABLE ROW --}}
@if($variant === 'table-row')
    <tr {{ $attributes->merge(['class' => 'border-b border-slate-100 dark:border-white/5']) }}>
        @for($i = 0; $i < $lines; $i++)
            <td class="px-6 py-4">
                {{-- Logika lebar kolom otomatis: kolom pertama kecil, kedua sedang, sisanya full --}}
                <div class="h-4 {{ $baseClass }} rounded-lg {{ $i === 0 ? 'w-8' : ($i === 1 ? 'w-32' : 'w-full') }}"></div>
            </td>
        @endfor
    </tr>

{{-- 2. VARIAN STAT CARD (DASHBOARD) --}}
@elseif($variant === 'stat-card')
    <div {{ $attributes->class(['bg-white dark:bg-[#111827] border border-slate-200 dark:border-white/10 p-5 rounded-2xl flex flex-col gap-3 shadow-sm', $class]) }}>
        {{-- Placeholder Label --}}
        <div class="h-3 w-16 {{ $baseClass }} rounded-full opacity-60"></div>
        {{-- Placeholder Value (Angka Besar) --}}
        <div class="h-8 w-24 {{ $baseClass }} rounded-xl"></div>
        {{-- Placeholder Subtext --}}
        <div class="h-3 w-32 {{ $baseClass }} rounded-full opacity-60"></div>
    </div>

{{-- 3. VARIAN DEFAULT (BLOCK/LINES) --}}
@else
    <div {{ $attributes->class([$baseClass, $variantClass, $class]) }}>
        @if ($variant === 'lines')
            <div class="space-y-3 p-5">
                @for ($i = 0; $i < $lines; $i++)
                    <div class="h-4 rounded-full bg-slate-200/90 dark:bg-slate-600/80 {{ $i === $lines - 1 ? 'w-3/4' : 'w-full' }}"></div>
                @endfor
            </div>
        @endif
    </div>
@endif