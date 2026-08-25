<div class="p-6 overflow-x-auto">
    <table class="w-full text-left border-collapse text-sm">
        <thead>
            <tr class="uppercase text-[10px] font-black tracking-widest border-b
                       bg-gray-50 text-gray-600
                       dark:bg-white/5 dark:text-slate-400 dark:border-white/5">
                <th class="py-3 px-4">No</th>
                <th class="py-3 px-4">Tanggal</th>
                <th class="py-3 px-4">Nama</th>
                <th class="py-3 px-4">NPM</th>
                <th class="py-3 px-4">Jurusan</th>
                <th class="py-3 px-4">Angkatan</th>
                <th class="py-3 px-4 text-center">Total Skor</th>
                <th class="py-3 px-4 text-center">Rincian</th>
            </tr>
        </thead>

        {{-- SKELETON: Muncul saat isLoading true --}}
        <tbody x-show="isLoading" x-cloak>
            @for ($i = 0; $i < 6; $i++)
                <x-skeleton variant="table-row" :lines="8" />
            @endfor
        </tbody>

        {{-- DATA ASLI: Muncul saat isLoading false --}}
        <tbody x-show="!isLoading" class="text-gray-700 dark:text-slate-300">
            @forelse($results as $row)
            <tr class="border-b transition-colors group
                       border-gray-100 hover:bg-gray-50
                       dark:border-white/5 dark:hover:bg-white/[0.02]"
                x-data="{ showDetail: false }">
                <td class="py-3 px-4 text-gray-500 dark:text-slate-500">
                    {{ $results->firstItem() + $loop->index }}
                </td>
                <td class="py-3 px-4 text-gray-500 dark:text-slate-500">
                    {{ $row->created_at->format('d M Y H:i') }}
                </td>
                <td class="py-3 px-4 font-semibold group-hover:text-indigo-500 transition-colors text-slate-900 dark:text-white">
                    {{ $row->user->name ?? '-' }}
                </td>
                <td class="py-3 px-4">{{ $row->user->npm ?? '-' }}</td>
                <td class="py-3 px-4">{{ $row->user->class ?? '-' }}</td>
                <td class="py-3 px-4">{{ $row->user->angkatan ?? '-' }}</td>
                <td class="py-3 px-4 text-center font-black text-blue-700 dark:text-blue-400">
                    {{ $row->score_total }}
                </td>
                <td class="py-3 px-4 text-center">
                    <button type="button" @click="showDetail = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 dark:bg-blue-500/10 dark:hover:bg-blue-500/20 dark:text-blue-300 rounded-lg text-xs font-semibold transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                        Detail
                    </button>

                    {{-- Modal Rincian Skor --}}
                    <div x-show="showDetail" x-cloak
                         class="fixed inset-0 z-50 overflow-y-auto text-left whitespace-normal"
                         style="display: none;">
                        <div class="flex items-center justify-center min-h-screen p-4 bg-black/50 backdrop-blur-sm"
                             @click.self="showDetail = false">
                            <div class="bg-white dark:bg-[#111827] border dark:border-white/10 rounded-xl max-w-sm w-full p-6 shadow-xl relative">
                                <div class="flex justify-between items-center mb-5">
                                    <h3 class="text-base font-bold text-gray-900 dark:text-white">Rincian Skor</h3>
                                    <button type="button" @click="showDetail = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-sm text-gray-500 dark:text-slate-400 mb-4">
                                    {{ $row->user->name ?? '-' }} &middot; {{ $row->created_at->format('d M Y H:i') }}
                                </p>

                                <div class="space-y-2.5">
                                    <div class="flex justify-between items-center px-4 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5">
                                        <span class="text-sm text-gray-600 dark:text-slate-300">Listening</span>
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $row->correct_listening }}</span>
                                    </div>
                                    <div class="flex justify-between items-center px-4 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5">
                                        <span class="text-sm text-gray-600 dark:text-slate-300">Structure</span>
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $row->correct_structure }}</span>
                                    </div>
                                    <div class="flex justify-between items-center px-4 py-2.5 rounded-lg bg-gray-50 dark:bg-white/5">
                                        <span class="text-sm text-gray-600 dark:text-slate-300">Reading</span>
                                        <span class="font-bold text-gray-900 dark:text-white">{{ $row->correct_reading }}</span>
                                    </div>
                                    <div class="flex justify-between items-center px-4 py-2.5 rounded-lg bg-blue-50 dark:bg-blue-500/10">
                                        <span class="text-sm font-semibold text-blue-700 dark:text-blue-300">Total Skor</span>
                                        <span class="font-black text-blue-700 dark:text-blue-300">{{ $row->score_total }}</span>
                                    </div>
                                </div>

                                <button type="button" @click="showDetail = false"
                                    class="mt-6 w-full px-4 py-2.5 bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 rounded-lg text-xs font-bold uppercase transition">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="py-12 text-center text-gray-400 italic">
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