{{--
    Komponen: <x-confirm-modal>
    Modal konfirmasi generik berbasis Alpine, dikendalikan dari luar lewat
    variabel showConfirm / confirmTitle / confirmMessage / confirmRoute
    yang didefinisikan di x-data induk (lihat dashboard.blade.php).

    Cara pakai (taruh di dalam elemen yang punya x-data showConfirm dkk):
        <x-confirm-modal />

    Cara pakai dengan nama variabel state berbeda (opsional):
        <x-confirm-modal show-var="showDeleteConfirm" />

    Props:
    - showVar (string)    : nama variabel Alpine untuk show/hide (default 'showConfirm')
    - titleVar (string)   : nama variabel Alpine untuk judul (default 'confirmTitle')
    - messageVar (string) : nama variabel Alpine untuk pesan (default 'confirmMessage')
    - routeVar (string)   : nama variabel Alpine untuk action route (default 'confirmRoute')
    - confirmLabel (string) : teks tombol konfirmasi (default 'Ya, Lanjutkan')
    - cancelLabel (string)  : teks tombol batal (default 'Batal')
--}}
@props([
    'showVar' => 'showConfirm',
    'titleVar' => 'confirmTitle',
    'messageVar' => 'confirmMessage',
    'routeVar' => 'confirmRoute',
    'confirmLabel' => 'Ya, Lanjutkan',
    'cancelLabel' => 'Batal',
])

<div x-cloak x-show="{{ $showVar }}"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-90"
     x-transition:enter-end="opacity-100 scale-100"
     class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm px-4">
    <div @click.away="{{ $showVar }} = false" class="w-full max-w-sm bg-white dark:bg-[#111827] rounded-2xl p-6 border border-slate-200 dark:border-white/10 shadow-2xl">
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2" x-text="{{ $titleVar }}"></h3>
        <p class="text-sm text-slate-500 mb-6" x-text="{{ $messageVar }}"></p>
        <div class="flex gap-3">
            <form method="POST" class="flex-1" :action="{{ $routeVar }}">
                @csrf
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl py-3 transition active:scale-95">{{ $confirmLabel }}</button>
            </form>
            <button type="button" @click="{{ $showVar }} = false" class="flex-1 bg-slate-100 dark:bg-white/5 dark:text-white text-slate-700 text-sm font-bold rounded-xl py-3 transition">{{ $cancelLabel }}</button>
        </div>
    </div>
</div>