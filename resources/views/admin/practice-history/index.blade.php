<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-0.5 animate-fade-in-down">
            {{-- Skeleton Header --}}
            <template x-if="!loaded">
                <x-skeleton variant="title" class="h-7 w-64 mt-1" />
            </template>
            {{-- Real Header --}}
            <h2 x-show="loaded" x-cloak class="font-semibold text-xl leading-tight" :class="$store.theme?.isDark ? 'text-white' : 'text-gray-800'">
                Riwayat Latihan Mahasiswa
            </h2>
        </div>
    </x-slot>

    <div class="py-12 transition-colors duration-500 min-h-screen" 
         :class="$store.theme?.isDark ? 'bg-[#0b0d13]' : 'bg-gray-50'"
         x-data="{ loaded: false }" 
         x-init="setTimeout(() => loaded = true, 600)">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- SKELETON FILTER CARD: Muncul saat loading --}}
            <template x-if="!loaded">
                <div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-gray-100'" 
                     class="p-4 mb-6 sm:rounded-xl border transition-all shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                        <div class="md:col-span-2 space-y-2">
                            <x-skeleton variant="title" class="w-20" />
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
                        <div class="space-y-2">
                            <x-skeleton variant="title" class="w-16" />
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
                        <div class="space-y-2">
                            <x-skeleton variant="title" class="w-16" />
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
                        <div>
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
                        <div>
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
                    </div>
                </div>
            </template>

            {{-- Filter Card dengan Animasi Slide In --}}
            <div x-show="loaded"
                 x-cloak
                 x-transition:enter="transition ease-out duration-500 delay-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-gray-100 shadow-sm'" 
                 class="p-4 mb-6 sm:rounded-xl border transition-all">
                <form method="GET" action="{{ route('practice-history.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold uppercase mb-1" :class="$store.theme?.isDark ? 'text-slate-500' : 'text-gray-500'">Cari Nama / NPM</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Ketik nama atau NPM..." 
                            :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-600' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm">
                    </div>
                    <div>
                        <label for="date_from" class="block text-xs font-semibold uppercase mb-1" :class="$store.theme?.isDark ? 'text-slate-500' : 'text-gray-500'">Dari Tanggal</label>
                        <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] }}" 
                            :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm">
                    </div>
                    <div>
                        <label for="date_to" class="block text-xs font-semibold uppercase mb-1" :class="$store.theme?.isDark ? 'text-slate-500' : 'text-gray-500'">Sampai Tanggal</label>
                        <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] }}" 
                            :class="$store.theme?.isDark ? '!bg-[#1e293b] !text-white border-white/10' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-md shadow-indigo-600/20 active:scale-95 text-sm uppercase tracking-tighter">
                            Terapkan Filter
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('practice-history.index') }}" 
                            :class="$store.theme?.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10 border-white/10' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 border-transparent'"
                            class="block w-full text-center px-4 py-2 rounded-lg font-semibold border transition active:scale-95 text-sm uppercase tracking-tighter">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table Container --}}
            <div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-slate-200 shadow-xl'" 
                 class="overflow-hidden sm:rounded-2xl border-t-8 border-blue-600 transition-all relative">
                
                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr :class="$store.theme?.isDark ? 'bg-white/5 text-slate-400 border-white/5' : 'bg-gray-50 text-gray-600'" 
                                class="uppercase text-[10px] font-black tracking-widest border-b">
                                <th class="py-3 px-4">Tanggal</th>
                                <th class="py-3 px-4">Nama</th>
                                <th class="py-3 px-4">NPM</th>
                                <th class="py-3 px-4">Kelas</th>
                                <th class="py-3 px-4 text-center">Listening</th>
                                <th class="py-3 px-4 text-center">Structure</th>
                                <th class="py-3 px-4 text-center">Reading</th>
                                <th class="py-3 px-4 text-center">Total Soal</th>
                                <th class="py-3 px-4 text-center">Total Skor</th>
                            </tr>
                        </thead>

                        {{-- SKELETON TABLE: Muncul saat !loaded --}}
                        <tbody x-show="!loaded">
                            @for ($i = 0; $i < 6; $i++)
                                <x-skeleton variant="table-row" :lines="9" />
                            @endfor
                        </tbody>

                        {{-- REAL DATA: Muncul saat loaded --}}
                        <tbody x-show="loaded" x-cloak :class="$store.theme?.isDark ? 'text-slate-300' : 'text-gray-700'">
                            @forelse($results as $row)
                                <tr class="border-b transition-colors group" 
                                    :class="$store.theme?.isDark ? 'border-white/5 hover:bg-white/[0.02] text-slate-300' : 'border-gray-100 hover:bg-gray-50 text-gray-700'">
                                    <td class="py-3 px-4" :class="$store.theme?.isDark ? 'text-slate-500' : 'text-gray-500'">{{ optional($row->submitted_at)->format('d M Y H:i') }}</td>
                                    <td class="py-3 px-4 font-semibold transition-colors group-hover:text-indigo-500" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">{{ $row->user->name ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ $row->user->npm ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ $row->user->class ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">{{ $row->correct_listening }}</td>
                                    <td class="py-3 px-4 text-center">{{ $row->correct_structure }}</td>
                                    <td class="py-3 px-4 text-center">{{ $row->correct_reading }}</td>
                                    <td class="py-3 px-4 text-center">{{ $row->total_questions }}</td>
                                    <td class="py-3 px-4 text-center font-black" :class="$store.theme?.isDark ? 'text-blue-400' : 'text-blue-700'">{{ $row->score_total }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-gray-400 italic">
                                        <div class="flex flex-col items-center gap-2 animate-pulse">
                                            <svg class="w-10 h-10 text-slate-300/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            <span>Belum ada data riwayat latihan ditemukan.</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-6" x-show="loaded" x-cloak>
                        {{ $results->links() }}
                    </div>
                    <div class="mt-6" x-show="!loaded">
                        <x-skeleton variant="chip" class="w-full h-10" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>