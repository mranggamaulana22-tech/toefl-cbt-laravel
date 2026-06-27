<x-guest-layout>
    <div class="mb-6">
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-violet-600">Daftar Akun</p>
        <h1 class="mt-2 text-2xl font-black tracking-tight text-slate-900">Buat akun mahasiswa</h1>
        <p class="mt-1 text-sm text-slate-500">Lengkapi data berikut untuk mulai ujian dan latihan TOEFL.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Nama Lengkap')" class="text-sm font-semibold text-slate-700" />
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                class="mt-2 block w-full rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-violet-500 focus:ring-violet-500"
                placeholder="Masukkan nama lengkap">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-600" />
        </div>

        <div class="mt-4">
            <x-input-label for="npm" :value="__('NPM')" class="text-sm font-semibold text-slate-700" />
            <input id="npm" type="text" name="npm" value="{{ old('npm') }}" autocomplete="off"
                class="mt-2 block w-full rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-violet-500 focus:ring-violet-500"
                placeholder="Contoh: 23010001">
            <x-input-error :messages="$errors->get('npm')" class="mt-2 text-sm text-red-600" />
        </div>

        <div class="mt-4">
            <x-input-label for="class" :value="__('Kelas')" class="text-sm font-semibold text-slate-700" />
            <input id="class" type="text" name="class" value="{{ old('class') }}" autocomplete="off"
                class="mt-2 block w-full rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-violet-500 focus:ring-violet-500"
                placeholder="Contoh: TI-2A">
            <x-input-error :messages="$errors->get('class')" class="mt-2 text-sm text-red-600" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-sm font-semibold text-slate-700" />
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                class="mt-2 block w-full rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-violet-500 focus:ring-violet-500"
                placeholder="nama@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-slate-700" />
            <input id="password" type="password" name="password" required autocomplete="new-password"
                class="mt-2 block w-full rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-violet-500 focus:ring-violet-500"
                placeholder="Minimal 8 karakter">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" class="text-sm font-semibold text-slate-700" />
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                class="mt-2 block w-full rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm transition focus:border-violet-500 focus:ring-violet-500"
                placeholder="Ulangi password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
        </div>

        <div class="mt-6 space-y-3">
            <button type="submit" class="inline-flex w-full items-center justify-center rounded-xl bg-violet-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-violet-700">
                Daftar
            </button>

            <a href="{{ route('login') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-violet-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-violet-300 hover:text-violet-700">
                Sudah punya akun? Masuk
            </a>
        </div>
    </form>
</x-guest-layout>
