<nav x-data="studentNav()" class="sticky top-0 z-50 border-b border-slate-200/80 bg-white/95 backdrop-blur-xl shadow-[0_8px_30px_rgba(15,23,42,0.08)]">
    @php
        $user = Auth::user();
        $isAdmin = $user?->role === 'admin';

        $desktopLinks = $isAdmin
            ? [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
                ['label' => 'Soal Ujian', 'route' => 'questions.index', 'active' => 'questions.*'],
                ['label' => 'Soal Latihan', 'route' => 'admin.practice-questions.index', 'active' => 'admin.practice-questions.*'],
                ['label' => 'Riwayat Latihan', 'route' => 'practice-history.index', 'active' => 'practice-history.*'],
                ['label' => 'Gradebook', 'route' => 'gradebook.index', 'active' => 'gradebook.*'],
                ['label' => 'Mahasiswa', 'route' => 'students.index', 'active' => 'students.*'],
            ]
            : [
                ['label' => 'Dashboard', 'route' => 'dashboard', 'active' => 'dashboard'],
                ['label' => 'Mulai Tes', 'route' => 'exam.start', 'active' => 'exam.*'],
                ['label' => 'Mode Latihan', 'route' => 'practice.start', 'active' => 'practice.*'],
                ['label' => 'Review AI', 'route' => 'student.review.index', 'active' => 'student.review.*'],
                ['label' => 'AI Analisis', 'route' => 'student.ai.index', 'active' => 'student.ai.*'],
                ['label' => 'Riwayat', 'route' => 'student.results.index', 'active' => 'student.results.*'],
            ];

        $mobileLinks = $desktopLinks;
    @endphp

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex h-14 items-center justify-between gap-4 overflow-visible">
            <div class="flex items-center gap-4 min-w-0">
                <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-sm transition hover:border-indigo-200 hover:shadow-md">
                    <span class="inline-flex h-10 w-10 items-center justify-center">
                        <x-application-logo class="h-10 w-10" />
                    </span>
                    <div class="hidden min-w-0 sm:block">
                        <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-indigo-500">TOEFL PIKSI</p>
                        <p class="truncate text-sm font-semibold text-slate-900">{{ config('app.name') }}</p>
                    </div>
                </a>

                <div class="hidden max-w-full flex-nowrap overflow-hidden lg:flex items-center gap-1 rounded-full border border-slate-200 bg-slate-50 p-1.5 shadow-inner">
                    @foreach ($desktopLinks as $link)
                        <x-nav-link :href="route($link['route'])" :active="request()->routeIs($link['active'])" class="rounded-full whitespace-nowrap px-3 py-2 text-sm font-semibold transition">
                            {{ $link['label'] }}
                        </x-nav-link>
                    @endforeach
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:gap-3">
                {{-- Tombol Tema: Muncul untuk Admin & Student --}}
                <button type="button"
                    @click="($store.theme?.toggle && $store.theme.toggle()) || (typeof toggleTheme === 'function' && toggleTheme())"
                    class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-indigo-200 hover:text-indigo-700"
                    title="Ganti tema">
                    <span x-cloak x-show="!$store.theme?.isDark">Gelap</span>
                    <span x-cloak x-show="$store.theme?.isDark">Terang</span>
                </button>

                {{-- Tombol Background: Hanya muncul jika BUKAN Admin --}}
                @unless($isAdmin)
                    <button type="button" @click="$store.bg.toggle()" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:border-indigo-200 hover:text-indigo-700" title="Background">
                        <span x-cloak x-show="$store.bg.enabled">Matikan</span>
                        <span x-cloak x-show="!$store.bg.enabled">Nyalakan</span>
                    </button>
                @endunless

                <x-dropdown align="right" width="56">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 text-left text-sm font-medium text-slate-700 shadow-sm transition hover:border-indigo-200 hover:shadow-md focus:outline-none">
                            @if ($user->profile_photo_url)
                                <img src="{{ $user->profile_photo_url.'?v='.(optional($user->updated_at)->timestamp ?? time()) }}" alt="Foto profil {{ $user->name }}" class="h-9 w-9 rounded-xl object-cover" />
                            @else
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-slate-800 to-slate-600 text-white font-bold">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                            @endif
                            <span class="min-w-0">
                                <span class="block truncate font-semibold text-slate-900">{{ $user->name }}</span>
                                <span class="block text-xs text-slate-500">{{ $isAdmin ? 'Admin' : 'Student' }}</span>
                            </span>
                            <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Profil
                        </x-dropdown-link>

                        <button type="button" x-on:click="$dispatch('open-modal', 'confirm-logout')" class="block w-full appearance-none border-0 border-l-2 border-transparent bg-transparent px-4 py-2 text-start text-sm leading-5 text-slate-700 transition duration-150 ease-in-out hover:border-indigo-300 hover:bg-slate-50 focus:outline-none focus:border-indigo-300 focus:bg-slate-50 dark:text-slate-200 dark:hover:border-indigo-400 dark:hover:bg-slate-800 dark:focus:border-indigo-400 dark:focus:bg-slate-800">
                            Logout
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" aria-label="Toggle mobile navigation" :aria-expanded="open.toString()" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white p-2 text-slate-500 shadow-sm transition hover:border-indigo-200 hover:text-indigo-700 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-slate-200 bg-white/98 backdrop-blur-xl">
        <div class="space-y-2 px-4 py-4">
            @foreach ($mobileLinks as $link)
                <x-responsive-nav-link :href="route($link['route'])" :active="request()->routeIs($link['active'])">
                    {{ $link['label'] }}
                </x-responsive-nav-link>
            @endforeach
        </div>

        <div class="border-t border-slate-200 px-4 pb-4 pt-4">
            {{-- Mobile Tema: Muncul untuk Admin & Student --}}
            <button type="button" @click="($store.theme?.toggle && $store.theme.toggle()) || (typeof toggleTheme === 'function' && toggleTheme())" class="mb-3 inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700">
                <span x-cloak x-show="!$store.theme?.isDark">Gunakan Tema Gelap</span>
                <span x-cloak x-show="$store.theme?.isDark">Gunakan Tema Terang</span>
            </button>

            {{-- Mobile Background: Hanya muncul jika BUKAN Admin --}}
            @unless($isAdmin)
                <button type="button" @click="$store.bg.toggle()" class="mb-3 inline-flex w-full items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-indigo-200 hover:text-indigo-700">
                    <span x-cloak x-show="$store.bg.enabled">Matikan Background</span>
                    <span x-cloak x-show="!$store.bg.enabled">Nyalakan Background</span>
                </button>
            @endunless

            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="flex items-center gap-3">
                    @if ($user->profile_photo_url)
                        <img src="{{ $user->profile_photo_url.'?v='.(optional($user->updated_at)->timestamp ?? time()) }}" alt="Foto profil {{ $user->name }}" class="h-11 w-11 rounded-xl object-cover" />
                    @else
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-slate-800 to-slate-600 text-sm font-bold text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    @endif
                    <div>
                        <div class="font-medium text-base text-slate-900">{{ $user->name }}</div>
                        <div class="font-medium text-sm text-slate-500">{{ $user->email }}</div>
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <button type="button" x-on:click="open = false; $dispatch('open-modal', 'confirm-logout')" class="block w-full rounded-lg appearance-none border-0 border-l-2 border-transparent bg-transparent px-4 py-2 text-start text-sm leading-5 text-slate-700 transition duration-150 ease-in-out hover:border-indigo-300 hover:bg-slate-50 focus:outline-none focus:border-indigo-300 focus:bg-slate-50 dark:text-slate-200 dark:hover:border-indigo-400 dark:hover:bg-slate-800">
                    {{ __('Log Out') }}
                </button>
            </div>
        </div>
    </div>
</nav>