{{--
    Komponen: <x-stat-card-icon>
    Varian x-stat-card dengan ikon di sisi kanan (dipakai di dashboard admin).

    PENTING: komponen ini TIDAK bergantung pada variabel Alpine "loaded" apapun,
    supaya aman dipakai di halaman manapun. Animasi masuk pakai CSS murni.

    Cara pakai:
        <x-stat-card-icon
            label="Mahasiswa"
            :value="$stats['total_mahasiswa']"
            sub="Total akun student aktif"
            color="blue"
            icon="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m8-9.25a4 4 0 11-8 0 4 4 0 018 0z"
            :delay="0"
        />

    Props:
    - label (string) : judul kecil
    - value (mixed)  : angka besar (otomatis di-number_format)
    - sub (string)   : teks kecil di bawah angka
    - color (string) : 'blue' | 'purple' | 'emerald' | 'amber' | 'indigo' dst (nama warna Tailwind)
    - icon (string)  : path SVG (isi atribut d="...")
    - delay (int)    : delay animasi CSS dalam ms
--}}
@props(['label' => '', 'value' => 0, 'sub' => '', 'color' => 'blue', 'icon' => '', 'delay' => 0])

<div
    class="border rounded-xl p-5 flex items-center justify-between shadow-sm transition-all duration-300 transform hover:-translate-y-1 cursor-default animate-fade-in-down
           bg-white border-slate-200 hover:shadow-lg
           dark:bg-[#111827] dark:border-white/10 dark:hover:bg-white/[0.03]"
    style="animation-delay: {{ $delay }}ms">
    <div>
        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1.5">{{ $label }}</p>
        <p class="text-3xl font-bold dark:text-white text-slate-900 leading-none mb-1">{{ number_format($value) }}</p>
        <p class="text-sm text-slate-500 dark:text-slate-400">{{ $sub }}</p>
    </div>
    <div class="w-12 h-12 rounded-xl bg-{{ $color }}-50 dark:bg-{{ $color }}-500/10 flex items-center justify-center flex-shrink-0">
        <svg class="w-6 h-6 text-{{ $color }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
    </div>
</div>