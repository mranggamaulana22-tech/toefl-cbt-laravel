<section>
    <header>
        <h2 class="student-profile-section-title text-xl font-bold">Informasi Profil</h2>

        <p class="student-profile-section-lead mt-1 text-sm leading-relaxed">
            Perbarui data utama akun Anda seperti nama, NPM, kelas, email, dan foto profil agar informasi mahasiswa selalu akurat.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6" x-data="{ photoPreview: null }">
        @csrf
        @method('patch')

        <div class="student-profile-photo-panel rounded-2xl border border-slate-200/80 p-4 sm:p-5">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                <div class="flex items-center gap-4">
                    @if ($user->profile_photo_url)
                        <img x-show="photoPreview" :src="photoPreview" alt="Preview foto profil baru" class="student-profile-photo-preview h-16 w-16 rounded-2xl object-cover" />
                        <img x-show="!photoPreview" src="{{ $user->profile_photo_url.'?v='.(optional($user->updated_at)->timestamp ?? time()) }}" alt="Foto profil {{ $user->name }}" class="student-profile-photo-preview h-16 w-16 rounded-2xl object-cover" />
                    @else
                        <img x-show="photoPreview" :src="photoPreview" alt="Preview foto profil baru" class="student-profile-photo-preview h-16 w-16 rounded-2xl object-cover" />
                        <span x-show="!photoPreview" class="student-profile-photo-fallback inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-slate-800 to-slate-600 text-lg font-bold text-white">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    @endif

                    <div>
                        <p class="text-sm font-semibold text-slate-800">Foto Profil</p>
                        <p class="mt-1 text-xs text-slate-500">Unggah JPG, PNG, atau WEBP (maksimal 2 MB).</p>
                    </div>
                </div>

                @if ($user->profile_photo_path)
                    <label for="remove_profile_photo" class="inline-flex cursor-pointer items-center gap-2 text-xs font-semibold text-slate-600">
                        <input id="remove_profile_photo" name="remove_profile_photo" value="1" type="checkbox" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" @checked(old('remove_profile_photo'))>
                        Hapus foto saat disimpan
                    </label>
                @endif
            </div>

            <div class="mt-4">
                <x-input-label for="profile_photo" :value="__('Upload Foto Profil')" />
                <input
                    id="profile_photo"
                    x-ref="profilePhoto"
                    name="profile_photo"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    x-on:change="
                        const selectedFile = $event.target.files[0];
                        if (!selectedFile) {
                            photoPreview = null;
                            return;
                        }

                        const reader = new FileReader();
                        reader.onload = (event) => { photoPreview = event.target?.result ?? null; };
                        reader.readAsDataURL(selectedFile);
                    "
                    class="mt-2 block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-xs file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                >
                <p class="mt-2 text-xs text-slate-500">Preview akan muncul otomatis setelah Anda memilih file. Jika gagal tersimpan, coba gunakan gambar JPG/PNG/WEBP berukuran lebih kecil.</p>
                <button
                    x-show="photoPreview"
                    type="button"
                    x-on:click="photoPreview = null; $refs.profilePhoto.value = null;"
                    class="mt-2 inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-600 transition hover:bg-slate-50"
                >
                    Batalkan file terpilih
                </button>
                <x-input-error class="mt-2" :messages="$errors->get('profile_photo')" />
                <x-input-error class="mt-2" :messages="$errors->get('remove_profile_photo')" />
                @if (session('photo-updated'))
                    <p class="mt-2 text-xs font-semibold text-emerald-600">Foto profil berhasil diperbarui.</p>
                @endif
            </div>
        </div>

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="npm" :value="__('NPM')" />
                <x-text-input id="npm" name="npm" type="text" class="mt-1 block w-full" :value="old('npm', $user->npm)" autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('npm')" />
            </div>

            <div>
                <x-input-label for="class" :value="__('Class')" />
                <x-text-input id="class" name="class" type="text" class="mt-1 block w-full" :value="old('class', $user->class)" autocomplete="off" />
                <x-input-error class="mt-2" :messages="$errors->get('class')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-slate-500"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
