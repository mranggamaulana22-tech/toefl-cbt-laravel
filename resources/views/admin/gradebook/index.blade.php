<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3 animate-fade-in-down">
            <div class="flex flex-col gap-0.5">
                {{-- Skeleton Header Title --}}
                <template x-if="!loaded">
                    <x-skeleton variant="title" class="h-7 w-64 mt-1" />
                </template>
                <h2 x-show="loaded" x-cloak class="font-semibold text-xl leading-tight" :class="$store.theme && $store.theme.isDark ? 'text-white' : 'text-gray-800'">
                    Laporan & Hasil Ujian (Gradebook)
                </h2>
            </div>
            <div class="flex items-center gap-2">
                <a id="gradebook-export-link" href="{{ route('gradebook.export.csv', ['date_from' => $filters['date_from'], 'date_to' => $filters['date_to'], 'search' => $filters['search']]) }}" 
                   class="inline-block px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 transition shadow-md shadow-emerald-600/20 active:scale-95">
                    <i class="fas fa-file-csv mr-1"></i> Export CSV
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12 transition-colors duration-500 min-h-screen" 
         :class="$store.theme && $store.theme.isDark ? 'bg-[#0b0d13]' : 'bg-gray-50'"
         x-data="{ 
            loaded: false, 
            isLoading: false,
            init() { 
                setTimeout(() => this.loaded = true, 600);
            } 
         }">
        
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- 1. SKELETON FILTER CARD --}}
            <template x-if="!loaded">
                <div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-gray-100'" 
                     class="p-4 mb-6 sm:rounded-xl border transition-all shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                        <div class="md:col-span-2 space-y-2">
                            <x-skeleton variant="title" class="w-32" />
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
                        <div class="space-y-2">
                            <x-skeleton variant="title" class="w-20" />
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
                        <div class="space-y-2">
                            <x-skeleton variant="title" class="w-20" />
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
                        <div><x-skeleton class="h-10 w-full rounded-lg" /></div>
                        <div><x-skeleton class="h-10 w-full rounded-lg" /></div>
                    </div>
                </div>
            </template>

            {{-- REAL FILTER CARD --}}
            <div x-show="loaded"
                 x-cloak
                 x-transition:enter="transition ease-out duration-500 delay-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-gray-100 shadow-sm'" 
                 class="p-4 mb-6 sm:rounded-xl border transition-all">
                <form id="gradebook-filter-form" method="GET" action="{{ route('gradebook.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold uppercase mb-1" :class="$store.theme && $store.theme.isDark ? 'text-slate-500' : 'text-gray-500'">Live Search Nama / NPM</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Ketik nama atau NPM..." 
                            :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-600' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border">
                    </div>
                    <div>
                        <label for="date_from" class="block text-xs font-semibold uppercase mb-1" :class="$store.theme && $store.theme.isDark ? 'text-slate-500' : 'text-gray-500'">Dari Tanggal</label>
                        <input id="date_from" name="date_from" type="date" value="{{ $filters['date_from'] }}" 
                            :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-white/10' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm">
                    </div>
                    <div>
                        <label for="date_to" class="block text-xs font-semibold uppercase mb-1" :class="$store.theme && $store.theme.isDark ? 'text-slate-500' : 'text-gray-500'">Sampai Tanggal</label>
                        <input id="date_to" name="date_to" type="date" value="{{ $filters['date_to'] }}" 
                            :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-white/10' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg focus:ring-indigo-500 transition border text-sm">
                    </div>
                    <div>
                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold hover:bg-indigo-700 transition shadow-md shadow-indigo-600/20 active:scale-95 text-sm uppercase">
                            Filter
                        </button>
                    </div>
                    <div>
                        <a href="{{ route('gradebook.index') }}" 
                            :class="$store.theme && $store.theme.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10 border-white/10' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 border-transparent'"
                            class="block w-full text-center px-4 py-2 rounded-lg font-bold border transition active:scale-95 text-sm uppercase">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- 2. RESULTS CONTAINER (Tabel) --}}
            <div id="gradebook-results" 
                 x-show="loaded"
                 x-cloak
                 x-transition:enter="transition ease-out duration-500 delay-500"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-indigo-600 shadow-xl'"
                 class="overflow-hidden sm:rounded-2xl border-t-8 transition-all relative">
                
                {{-- Data Di-include lewat Partials --}}
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

                // PICU SKELETON LOADING PADA TABEL
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
                    // MATIKAN SKELETON, DATA ASLI MUNCUL
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

            // Handle Pagination Click
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