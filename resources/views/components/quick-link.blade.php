{{--
    Komponen: <x-quick-link>
    Item menu cepat (ikon + nama + deskripsi) di panel "Menu Cepat" dashboard.

    Cara pakai:
        <x-quick-link
            :route="route('students.index')"
            name="Mahasiswa"
            desc="Kelola data student"
            color="blue"
            icon="M17 20h5v-2a4 4 0 00-5-3.87M9 20H4v-2a4 4 0 015-3.87m8-9.25a4 4 0 11-8 0 4 4 0 018 0z"
        />

    Props:
    - route (string) : URL tujuan
    - name (string)  : nama menu
    - desc (string)  : deskripsi singkat
    - color (string) : nama warna Tailwind untuk ikon
    - icon (string)  : path SVG (isi atribut d="...")
--}}
@props(['route' => '#', 'name' => '', 'desc' => '', 'color' => 'blue', 'icon' => ''])

<a href="{{ $route }}"
   class="group flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-white/5 transition-all duration-200"
   :class="$store.theme?.isDark ? 'hover:bg-white/[0.03]' : 'hover:bg-indigo-50/50'">
    <div class="w-9 h-9 rounded-lg bg-{{ $color }}-50 dark:bg-{{ $color }}-500/10 text-{{ $color }}-600 flex items-center justify-center flex-shrink-0 transition-all duration-300 group-hover:scale-110">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
    </div>
    <div class="flex-1 min-w-0">
        <p class="text-xs font-bold text-slate-800 dark:text-slate-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">{{ $name }}</p>
        <p class="text-[11px] text-slate-400 dark:text-slate-500">{{ $desc }}</p>
    </div>
</a>