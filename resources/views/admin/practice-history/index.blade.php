<x-app-layout>
    <div class="py-12 min-h-screen bg-gray-50 dark:bg-[#0b0d13]">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in-down">

            {{-- Filter Card --}}
            <div class="p-4 mb-6 sm:rounded-xl border
                        bg-white border-gray-100 shadow-sm
                        dark:bg-[#111827] dark:border-white/10 dark:shadow-none">
                <form method="GET" action="{{ route('practice-history.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Cari Nama / NPM</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Ketik nama atau NPM..."
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm
                                   bg-white border-gray-300 text-gray-900
                                   dark:bg-[#1e293b] dark:text-white dark:border-white/10 dark:placeholder-slate-600">
                    </div>
                    <div>
                        <label for="date_from" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Dari Tanggal</label>
                        <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] }}"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm
                                   bg-white border-gray-300 text-gray-900
                                   dark:bg-[#1e293b] dark:text-white dark:border-white/10">
                    </div>
                    <div>
                        <label for="date_to" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Sampai Tanggal</label>
                        <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] }}"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm
                                   bg-white border-gray-300 text-gray-900
                                   dark:bg-[#1e293b] dark:text-white dark:border-white/10">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-md shadow-indigo-600/20 active:scale-95 text-sm uppercase tracking-tighter">
                            Terapkan Filter
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('practice-history.index') }}"
                            class="block w-full text-center px-4 py-2 rounded-lg font-semibold border transition active:scale-95 text-sm uppercase tracking-tighter
                                   bg-gray-200 text-gray-700 hover:bg-gray-300 border-transparent
                                   dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 dark:border-white/10">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table Container --}}
            <div class="overflow-hidden sm:rounded-2xl border-t-8 border-blue-600 relative
                        bg-white shadow-xl
                        dark:bg-[#111827] dark:shadow-none">

                <div class="p-6 overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="uppercase text-[10px] font-black tracking-widest border-b
                                       bg-gray-50 text-gray-600
                                       dark:bg-white/5 dark:text-slate-400 dark:border-white/5">
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

                        <tbody class="text-gray-700 dark:text-slate-300">
                            @forelse($results as $row)
                                <tr class="border-b transition-colors group
                                           border-gray-100 hover:bg-gray-50
                                           dark:border-white/5 dark:hover:bg-white/[0.02]">
                                    <td class="py-3 px-4 text-gray-500 dark:text-slate-500">{{ optional($row->submitted_at)->format('d M Y H:i') }}</td>
                                    <td class="py-3 px-4 font-semibold transition-colors group-hover:text-indigo-500 text-slate-900 dark:text-white">{{ $row->user->name ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ $row->user->npm ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ $row->user->class ?? '-' }}</td>
                                    <td class="py-3 px-4 text-center">{{ $row->correct_listening }}</td>
                                    <td class="py-3 px-4 text-center">{{ $row->correct_structure }}</td>
                                    <td class="py-3 px-4 text-center">{{ $row->correct_reading }}</td>
                                    <td class="py-3 px-4 text-center">{{ $row->total_questions }}</td>
                                    <td class="py-3 px-4 text-center font-black text-blue-700 dark:text-blue-400">{{ $row->score_total }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="py-12 text-center text-gray-400 italic">
                                        Belum ada data riwayat latihan ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-6">
                        {{ $results->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>