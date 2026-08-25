<x-app-layout>
    <div class="py-12 transition-colors duration-500 min-h-screen bg-gray-50 dark:bg-[#0b0d13]"
         x-data="{ isLoading: false }">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-500/10 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 mb-6 shadow-sm animate-bounce-short transition-all">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            {{-- Filter Card --}}
            <div class="p-4 mb-6 sm:rounded-xl border transition-all bg-white border-gray-100 shadow-sm dark:bg-[#111827] dark:border-white/10 dark:shadow-none">
                <form id="student-filter-form" method="GET" action="{{ route('students.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Live Search Nama / NPM</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Ketik nama atau NPM..."
                            class="w-full rounded-lg focus:ring-indigo-500 transition bg-white border-gray-300 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10 dark:placeholder-slate-600">
                    </div>
                    <div>
                        <label for="class" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Filter Jurusan</label>
                        <select id="class" name="class"
                            class="w-full rounded-lg focus:ring-indigo-500 transition bg-white border-gray-300 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                            <option value="">Semua Jurusan</option>
                            @foreach($classes as $className)
                                <option value="{{ $className }}" @selected($filters['class'] === $className)>{{ $className }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="angkatan" class="block text-xs font-semibold uppercase mb-1 text-gray-500 dark:text-slate-500">Filter Angkatan</label>
                        <select id="angkatan" name="angkatan"
                            class="w-full rounded-lg focus:ring-indigo-500 transition bg-white border-gray-300 text-gray-900 dark:!bg-[#1e293b] dark:!text-white dark:border-white/10">
                            <option value="">Semua Angkatan</option>
                            @foreach($angkatans as $year)
                                <option value="{{ $year }}" @selected((string) $filters['angkatan'] === (string) $year)>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <a href="{{ route('students.index') }}"
                            class="block w-full text-center px-4 py-2 rounded-lg font-bold border transition active:scale-95 text-xs uppercase tracking-tighter bg-gray-200 text-gray-700 hover:bg-gray-300 border-transparent dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10 dark:border-white/10">
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>

            {{-- Results Container --}}
            @include('admin.students.partials.results')
        </div>
    </div>

    <script>
        (function () {
            const getAlpine = () => Alpine.$data(document.querySelector('[x-data]'));
            const form = document.getElementById('student-filter-form');
            const searchInput = document.getElementById('search');
            const classSelect = document.getElementById('class');
            const angkatanSelect = document.getElementById('angkatan');
            const resultsContainer = document.getElementById('students-results');

            if (!form || !searchInput || !classSelect || !angkatanSelect || !resultsContainer) return;

            let activeRequest = null;

            const fetchAndRender = (url) => {
                const alpine = getAlpine();
                if (activeRequest) activeRequest.abort();
                activeRequest = new AbortController();

                alpine.isLoading = true;

                const partialUrl = url + (url.includes('?') ? '&' : '?') + 'partial=1';

                fetch(partialUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: activeRequest.signal,
                })
                .then((response) => response.json())
                .then((data) => {
                    resultsContainer.outerHTML = data.html;
                    alpine.isLoading = false;
                    window.history.replaceState({}, '', url);
                })
                .catch((error) => {
                    if (error.name !== 'AbortError') {
                        console.error(error);
                        alpine.isLoading = false;
                    }
                });
            };

            const submitFilters = () => {
                const params = new URLSearchParams(new FormData(form));
                const url = form.action + '?' + params.toString();
                fetchAndRender(url);
            };

            let timer;
            searchInput.addEventListener('input', function () {
                clearTimeout(timer);
                timer = setTimeout(submitFilters, 450);
            });

            classSelect.addEventListener('change', submitFilters);
            angkatanSelect.addEventListener('change', submitFilters);

            document.addEventListener('click', function (event) {
                const link = event.target.closest('#students-results a');
                if (!link || !link.href || !link.href.includes('page=')) return;
                event.preventDefault();
                fetchAndRender(link.href);
            });
        })();
    </script>
</x-app-layout>