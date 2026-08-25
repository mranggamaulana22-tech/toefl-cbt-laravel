<x-app-layout>
    <div class="py-12 min-h-screen bg-gray-50 dark:bg-[#0b0d13]"
         x-data="{ isLoading: false }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 animate-fade-in-down">

            {{-- Filter Card --}}
            <div class="p-4 mb-6 sm:rounded-xl border
                        bg-white border-gray-100 shadow-sm
                        dark:bg-[#111827] dark:border-white/10 dark:shadow-none">
                <form id="gradebook-filter-form" method="GET" action="{{ route('gradebook.index') }}" class="grid grid-cols-1 md:grid-cols-7 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Cari Nama / NPM</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Ketik nama atau NPM..."
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm
                                   bg-white border-gray-300 text-gray-900
                                   dark:bg-[#1e293b] dark:text-white dark:border-white/10 dark:placeholder-slate-600">
                    </div>
                    <div>
                        <label for="class" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Jurusan</label>
                        <select id="class" name="class"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm
                                   bg-white border-gray-300 text-gray-900
                                   dark:bg-[#1e293b] dark:text-white dark:border-white/10">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusanOptions as $value => $label)
                                <option value="{{ $value }}" @selected($filters['class'] === $value)>{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="angkatan" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Angkatan</label>
                        <select id="angkatan" name="angkatan"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm
                                   bg-white border-gray-300 text-gray-900
                                   dark:bg-[#1e293b] dark:text-white dark:border-white/10">
                            <option value="">Semua Angkatan</option>
                            @foreach($angkatanOptions as $year)
                                <option value="{{ $year }}" @selected((string) $filters['angkatan'] === (string) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="sort" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Urutkan</label>
                        <select id="sort" name="sort"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm
                                   bg-white border-gray-300 text-gray-900
                                   dark:bg-[#1e293b] dark:text-white dark:border-white/10">
                            <option value="newest" @selected($filters['sort'] === 'newest')>Terbaru</option>
                            <option value="score_desc" @selected($filters['sort'] === 'score_desc')>Skor Tertinggi</option>
                            <option value="score_asc" @selected($filters['sort'] === 'score_asc')>Skor Terendah</option>
                        </select>
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
                    <div class="flex gap-2">
                        <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-md shadow-blue-600/20 active:scale-95 text-sm uppercase tracking-tighter">
                            Filter
                        </button>
                        <a href="{{ route('gradebook.index') }}"
                            class="w-full text-center px-4 py-2 rounded-lg font-semibold border transition active:scale-95 text-sm uppercase tracking-tighter
                                   bg-gray-200 text-gray-700 hover:bg-gray-300 border-transparent
                                   dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 dark:border-white/10">
                            Reset
                        </a>
                    </div>
                </form>

                <div class="mt-3 flex justify-end">
                    <a id="gradebook-export-link" href="{{ route('gradebook.export.csv') }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 text-white rounded-lg font-semibold hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20 active:scale-95 text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Export Excel
                    </a>
                </div>
            </div>

            {{-- Table Container --}}
            <div id="gradebook-results"
                 class="overflow-hidden sm:rounded-2xl border-t-8 border-blue-600 relative
                        bg-white shadow-xl
                        dark:bg-[#111827] dark:shadow-none">

                @include('admin.gradebook.partials.results', ['results' => $results])
            </div>
        </div>
    </div>

    <script>
        (function () {
            const getAlpine = () => Alpine.$data(document.querySelector('[x-data]'));
            const form = document.getElementById('gradebook-filter-form');
            const searchInput = document.getElementById('search');
            const classSelect = document.getElementById('class');
            const angkatanSelect = document.getElementById('angkatan');
            const sortSelect = document.getElementById('sort');
            const resultsContainer = document.getElementById('gradebook-results');
            const exportLink = document.getElementById('gradebook-export-link');

            if (!form || !searchInput || !classSelect || !angkatanSelect || !sortSelect || !resultsContainer) return;

            let activeRequest = null;

            const updateExportLink = () => {
                if (!exportLink) return;
                const params = new URLSearchParams(new FormData(form));
                exportLink.href = "{{ route('gradebook.export.csv') }}?" + params.toString();
            };

            const fetchAndRender = (url) => {
                const alpine = getAlpine();
                if (activeRequest) activeRequest.abort();
                activeRequest = new AbortController();

                alpine.isLoading = true;

                const requestUrl = new URL(url, window.location.origin);
                requestUrl.searchParams.set('partial', '1');

                fetch(requestUrl.toString(), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    signal: activeRequest.signal,
                })
                .then(r => r.json())
                .then(payload => {
                    if (payload && payload.html) {
                        resultsContainer.innerHTML = payload.html;
                    }
                    alpine.isLoading = false;
                    updateExportLink();
                    window.history.replaceState({}, '', url);
                })
                .catch(e => {
                    if (e.name !== 'AbortError') {
                        console.error(e);
                        alpine.isLoading = false;
                    }
                });
            };

            const submitFilters = () => {
                const params = new URLSearchParams(new FormData(form));
                fetchAndRender(form.action + '?' + params.toString());
            };

            let timer;
            searchInput.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(submitFilters, 450);
            });

            classSelect.addEventListener('change', submitFilters);
            angkatanSelect.addEventListener('change', submitFilters);
            sortSelect.addEventListener('change', submitFilters);

            form.addEventListener('submit', e => {
                e.preventDefault();
                submitFilters();
            });

            resultsContainer.addEventListener('click', e => {
                const link = e.target.closest('a');
                if (link && link.href && link.href.includes('page=')) {
                    e.preventDefault();
                    fetchAndRender(link.href);
                }
            });

            updateExportLink();
        })();
    </script>
</x-app-layout>