<div class="p-6 overflow-x-auto">
    <table class="w-full text-left border-collapse text-sm">
        <thead>
            <tr :class="$store.theme?.isDark ? 'bg-white/5 text-slate-400 border-white/5' : 'bg-gray-50 text-gray-600'" 
                class="uppercase text-[10px] font-black tracking-widest border-b">
                <th class="py-3 px-4">Tanggal</th>
                <th class="py-3 px-4">Nama</th>
                <th class="py-3 px-4">NPM</th>
                <th class="py-3 px-4 text-center">Listening</th>
                <th class="py-3 px-4 text-center">Structure</th>
                <th class="py-3 px-4 text-center">Reading</th>
                <th class="py-3 px-4 text-center">Total Skor</th>
            </tr>
        </thead>

        {{-- SKELETON: Muncul saat isLoading true --}}
        <tbody x-show="isLoading" x-cloak>
            @for ($i = 0; $i < 6; $i++)
                <x-skeleton variant="table-row" :lines="7" />
            @endfor
        </tbody>

        {{-- DATA ASLI: Muncul saat isLoading false --}}
        <tbody x-show="!isLoading" :class="$store.theme?.isDark ? 'text-slate-300' : 'text-gray-700'">
            @forelse($results as $row)
            <tr class="border-b transition-colors group" 
                :class="$store.theme?.isDark ? 'border-white/5 hover:bg-white/[0.02]' : 'border-gray-100 hover:bg-gray-50'">
                <td class="py-3 px-4" :class="$store.theme?.isDark ? 'text-slate-500' : 'text-gray-500'">
                    {{ $row->created_at->format('d M Y H:i') }}
                </td>
                <td class="py-3 px-4 font-semibold group-hover:text-indigo-500 transition-colors" :class="$store.theme?.isDark ? 'text-white' : 'text-slate-900'">
                    {{ $row->user->name ?? '-' }}
                </td>
                <td class="py-3 px-4">{{ $row->user->npm ?? '-' }}</td>
                <td class="py-3 px-4 text-center font-medium">{{ $row->correct_listening }}</td>
                <td class="py-3 px-4 text-center font-medium">{{ $row->correct_structure }}</td>
                <td class="py-3 px-4 text-center font-medium">{{ $row->correct_reading }}</td>
                <td class="py-3 px-4 text-center font-black" :class="$store.theme?.isDark ? 'text-indigo-400' : 'text-indigo-700'">
                    {{ $row->score_total }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-12 text-center text-gray-400 italic">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-10 h-10 text-slate-300/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Belum ada data hasil ujian ditemukan.</span>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination Wrapper --}}
    <div class="mt-6">
        <div x-show="!isLoading">
            {{ $results->links() }}
        </div>
        {{-- Skeleton untuk Pagination --}}
        <div x-show="isLoading" x-cloak>
            <x-skeleton variant="chip" class="w-full h-10 rounded-xl" />
        </div>
    </div>
</div>