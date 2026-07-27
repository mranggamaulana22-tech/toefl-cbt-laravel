<x-app-layout>
    <div class="py-12 transition-colors duration-500 min-h-screen bg-gray-50 dark:bg-[#0b0d13]"
         x-data="{ isLoading: false }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Filter Card --}}
            <div class="p-4 mb-6 sm:rounded-xl border transition-all bg-white border-gray-100 shadow-sm dark:bg-[#111827] dark:border-white/10 dark:shadow-none">
                <form id="gradebook-filter-form" method="GET" action="{{ route('gradebook.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Live Search Nama / NPM</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Ketik nama atau NPM..."
                            class="w-full rounded-lg focus:ring-indigo-500 transition border bg-white border-gray-300 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10 dark:placeholder-slate-600">
                    </div>
                    <div>
                        <label for="date_from" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Dari Tanggal</label>
                        <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] }}"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm bg-white border-gray-300 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                    </div>
                    <div>
                        <label for="date_to" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Sampai Tanggal</label>
                        <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] }}"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm bg-white border-gray-300 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-md shadow-indigo-600/20 active:scale-95 text-sm uppercase">
                            Filter
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('gradebook.index') }}"
                            class="block w-full text-center px-4 py-2 rounded-lg font-bold border transition active:scale-95 text-sm uppercase bg-gray-200 text-gray-700 hover:bg-gray-300 border-transparent dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 dark:border-white/10">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- Results Container --}}
            <div id="gradebook-results"
                 class="overflow-hidden sm:rounded-2xl border-t-8 transition-all relative bg-white border-indigo-600 shadow-xl dark:bg-[#111827] dark:border-white/10 dark:shadow-none">

                @include('admin.gradebook.partials.results', ['results' => $results])
            </div>
        </div>
    </div>

    <script>
        (function () {
            const getAlpine = () => Alpine.$data(document.querySelector('[x-data]'));
            const form = document.getElementById('gradebook-filter-form');
            const searchInput = document.getElementById('search');
            const resultsContainer = document.getElementById('gradebook-results');
            const exportLink = document.getElementById('gradebook-export-link');

            let activeRequest = null;

            const updateExportLink = () => {
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