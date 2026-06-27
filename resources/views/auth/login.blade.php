<x-guest-layout>
    <div class="mb-6">
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-violet-600">Masuk Akun</p>
        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-900">Selamat datang kembali</h1>
        <p class="mt-1 text-sm text-slate-500">Masuk untuk melanjutkan ujian dan latihan TOEFL.</p>
    </div>

    <x-auth-session-status class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-slate-700" />
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                class="mt-2 block w-full rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-violet-500 focus:ring-violet-500"
                placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-slate-700" />
            <input id="password" type="password" name="password" required autocomplete="current-password"
                class="mt-2 block w-full rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-violet-500 focus:ring-violet-500"
                placeholder="Masukkan password">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <div class="mt-4 flex items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-violet-300 text-violet-600 shadow-sm focus:ring-violet-500" name="remember">
                <span class="ms-2 text-sm text-slate-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-violet-600 transition hover:text-violet-700" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <div class="mt-6 space-y-3">
            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700">
                Masuk
            </button>

            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-violet-300 hover:text-violet-700">
                    Belum punya akun? Daftar
                </a>
            @endif
        </div>
    </form>
</x-guest-layout>
