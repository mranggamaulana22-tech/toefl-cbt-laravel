<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1 animate-fade-in-down">
            <p class="text-[11px] font-bold uppercase tracking-[0.22em] text-indigo-500">Manajemen Pengguna</p>
            
            {{-- Skeleton Header --}}
            <template x-if="!loaded">
                <div class="space-y-2 mt-1">
                    <x-skeleton variant="title" class="h-8 w-72" />
                    <x-skeleton variant="title" class="h-4 w-96 opacity-50" />
                </div>
            </template>

            {{-- Real Header --}}
            <div x-show="loaded" x-cloak>
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    {{ __('Data Mahasiswa Peserta TOEFL') }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Kelola, filter, dan hapus data mahasiswa peserta ujian TOEFL CBT.
                </p>
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
            
            @if(session('success'))
                <div class="bg-green-100 dark:bg-green-500/10 border-l-4 border-green-500 text-green-700 dark:text-green-400 p-4 mb-6 shadow-sm animate-bounce-short transition-all">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            {{-- 1. SKELETON FILTER CARD --}}
            <template x-if="!loaded">
                <div :class="$store.theme?.isDark ? 'bg-[#111827] border-white/10' : 'bg-white border-gray-100'" 
                     class="p-4 mb-6 sm:rounded-xl border transition-all shadow-sm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                        <div class="md:col-span-2 space-y-2">
                            <x-skeleton variant="title" class="w-32" />
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
                        <div class="space-y-2">
                            <x-skeleton variant="title" class="w-24" />
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
                        <div>
                            <x-skeleton class="h-10 w-full rounded-lg" />
                        </div>
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
                <form id="student-filter-form" method="GET" action="{{ route('students.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                    <div class="md:col-span-2">
                        <label for="search" class="block text-xs font-semibold uppercase mb-1" :class="$store.theme && $store.theme.isDark ? 'text-slate-500' : 'text-gray-500'">Live Search Nama / NPM</label>
                        <input id="search" name="search" type="text" value="{{ $filters['search'] }}" placeholder="Ketik nama atau NPM..." 
                            :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-white/10 placeholder-slate-600' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg focus:ring-indigo-500 transition">
                    </div>
                    <div>
                        <label for="class" class="block text-xs font-semibold uppercase mb-1" :class="$store.theme && $store.theme.isDark ? 'text-slate-500' : 'text-gray-500'">Filter Kelas</label>
                        <select id="class" name="class" 
                            :class="$store.theme && $store.theme.isDark ? '!bg-[#1e293b] !text-white border-white/10' : 'bg-white border-gray-300 text-gray-900'"
                            class="w-full rounded-lg focus:ring-indigo-500 transition">
                            <option value="">Semua Kelas</option>
                            @foreach($classes as $className)
                                <option value="{{ $className }}" @selected($filters['class'] === $className)>{{ $className }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <a href="{{ route('students.index') }}" 
                            :class="$store.theme && $store.theme.isDark ? 'bg-white/5 text-slate-300 hover:bg-white/10 border-white/10' : 'bg-gray-200 text-gray-700 hover:bg-gray-300 border-transparent'"
                            class="block w-full text-center px-4 py-2 rounded-lg font-bold border transition active:scale-95 text-xs uppercase tracking-tighter">
                            Reset Filter
                        </a>
                    </div>
                </form>
            </div>

            {{-- 2. RESULTS CONTAINER (Tabel) --}}
            <div id="students-results" 
                 x-show="loaded"
                 x-cloak
                 x-transition:enter="transition ease-out duration-500 delay-500"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 :class="$store.theme && $store.theme.isDark ? 'bg-[#111827] border-white/10 shadow-none' : 'bg-white border-red-600 shadow-xl'"
                 class="overflow-hidden sm:rounded-2xl border-t-8 transition-all relative">
                <div class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr :class="$store.theme?.isDark ? 'bg-white/5 text-slate-400 border-white/5' : 'bg-gray-50 text-gray-600'" 
                                    class="uppercase text-[10px] font-black tracking-widest border-b">
                                    <th class="py-3 px-6">NPM / Nama</th>
                                    <th class="py-3 px-6">Kelas</th>
                                    <th class="py-3 px-6">Email</th>
                                    <th class="py-3 px-6 text-center">Total Ujian</th>
                                    <th class="py-3 px-6 text-center">Aksi</th>
                                </tr>
                            </thead>

                            {{-- SKELETON BODY: Muncul saat isLoading --}}
                            <tbody x-show="isLoading" x-cloak>
                                @for($i=0; $i<6; $i++)
                                    <x-skeleton variant="table-row" :lines="5" />
                                @endfor
                            </tbody>

                            {{-- REAL DATA BODY: Muncul saat !isLoading --}}
                            <tbody x-show="!isLoading" :class="$store.theme && $store.theme.isDark ? 'text-slate-300' : 'text-gray-700'" class="text-sm font-light">
                                @forelse($students as $s)
                                <tr class="border-b transition-colors group" 
                                    :class="$store.theme && $store.theme.isDark ? 'border-white/5 hover:bg-white/[0.02]' : 'border-gray-100 hover:bg-gray-50'">
                                    <td class="py-4 px-6">
                                        <div class="flex flex-col transition-transform group-hover:translate-x-1">
                                            <span class="font-bold" :class="$store.theme && $store.theme.isDark ? 'text-white' : 'text-gray-900'">{{ $s->npm }}</span>
                                            <span :class="$store.theme && $store.theme.isDark ? 'text-slate-400' : ''">{{ $s->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase transition-all group-hover:scale-110 inline-block
                                            {{ $filters['class'] === $s->class ? 'bg-indigo-600 text-white shadow-md shadow-indigo-900/20' : 'bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400' }}">
                                            {{ $s->class ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 italic transition-colors group-hover:text-indigo-500" :class="$store.theme && $store.theme.isDark ? 'text-slate-500' : ''">{{ $s->email }}</td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="text-lg font-black transition-colors" :class="$store.theme && $store.theme.isDark ? 'text-blue-400' : 'text-blue-600'">{{ $s->results_count }}</span>
                                        <span class="text-[10px] uppercase font-bold tracking-widest block" :class="$store.theme && $store.theme.isDark ? 'text-slate-600' : 'text-gray-400'">Sesi</span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <form action="{{ route('students.destroy', $s->id) }}" method="POST" onsubmit="return confirm('Hapus mahasiswa ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 transition transform hover:scale-125 active:scale-90 p-2">
                                                <i class="fas fa-trash-alt text-lg"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-400 italic">
                                        <div class="flex flex-col items-center gap-2 animate-pulse">
                                            <svg class="w-10 h-10 text-slate-300/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                            <span>Data mahasiswa tidak ditemukan.</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6" x-show="!isLoading">
                        {{ $students->links() }}
                    </div>
                    <div class="mt-6" x-show="isLoading" x-cloak>
                        <x-skeleton variant="chip" class="w-full h-10 rounded-xl" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const getAlpine = () => Alpine.$data(document.querySelector('[x-data]'));
            const form = document.getElementById('student-filter-form');
            const searchInput = document.getElementById('search');
            const classSelect = document.getElementById('class');
            const resultsContainer = document.getElementById('students-results');

            if (!form || !searchInput || !classSelect || !resultsContainer) return;

            let activeRequest = null;

            const fetchAndRender = (url) => {
                const alpine = getAlpine();
                if (activeRequest) activeRequest.abort();
                activeRequest = new AbortController();

                // AKTIFKAN SKELETON
                alpine.isLoading = true;

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: activeRequest.signal,
                })
                .then((response) => response.text())
                .then((html) => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newResults = doc.getElementById('students-results');

                    if (newResults) {
                        resultsContainer.innerHTML = newResults.innerHTML;
                    }
                    
                    // MATIKAN SKELETON
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

            resultsContainer.addEventListener('click', function (event) {
                const link = event.target.closest('a');
                if (!link || !link.href || !link.href.includes('page=')) return;
                event.preventDefault();
                fetchAndRender(link.href);
            });
        })();
    </script>
</x-app-layout>