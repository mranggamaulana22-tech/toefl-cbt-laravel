<div x-show="showTimeUpNotice" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
    <div class="w-full max-w-md rounded-2xl bg-white p-6 text-center shadow-2xl border border-slate-200">
        <p class="text-xs uppercase tracking-[0.2em] text-amber-600 font-bold">Waktu Habis</p>
        <h3 class="mt-2 text-xl font-black text-slate-900">Jawaban Sedang Dikumpulkan</h3>
        <p class="mt-2 text-sm text-slate-600">Sistem akan mengirim jawaban kamu secara otomatis.</p>
    </div>
</div>

<div x-cloak x-show="showBackGestureWarning" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl border border-red-200 bg-white p-6 shadow-2xl">
        <p class="text-xs uppercase tracking-[0.2em] text-red-600 font-bold">Navigasi Dilarang</p>
        <h3 class="mt-2 text-xl font-black text-slate-900">Navigasi back tidak diperbolehkan</h3>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">Anda sedang dalam latihan. Tetap di halaman ini dan selesaikan semua soal terlebih dahulu.</p>
        <div class="mt-6 flex justify-end">
            <button type="button" @click="showBackGestureWarning = false" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">Mengerti</button>
        </div>
    </div>
</div>

<div x-cloak x-show="showViolationWarning" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4" @click.self="showViolationWarning = false">
    <div class="w-full max-w-md rounded-2xl border border-amber-200 bg-white p-6 shadow-2xl">
        <p class="text-xs uppercase tracking-[0.2em] text-amber-600 font-bold" x-text="violationWarningTitle"></p>
        <h3 class="mt-2 text-xl font-black text-slate-900">Tetap di halaman latihan</h3>
        <p class="mt-2 text-sm leading-relaxed text-slate-600" x-text="violationWarningMessage"></p>
        <div class="mt-6 flex justify-end">
            <button type="button" @click="showViolationWarning = false" class="rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">Mengerti</button>
        </div>
    </div>
</div>

<div x-cloak x-show="showSubmitConfirm" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4" @click.self="showSubmitConfirm = false">
    <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl border border-slate-200">
        <p class="text-xs uppercase tracking-[0.2em] text-emerald-600 font-bold">Konfirmasi Submit</p>
        <h3 class="mt-2 text-xl font-black text-slate-900">Kumpulkan jawaban latihan sekarang?</h3>
        <p class="mt-2 text-sm text-slate-600">Setelah dikirim, latihan ini akan dinilai otomatis.</p>
        <div class="mt-6 flex gap-2">
            <button type="button" @click="showSubmitConfirm = false" class="flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Batal</button>
            <button type="button" @click="showSubmitConfirm = false; clearProgress(); document.getElementById('practice-form').submit();" class="flex-1 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">Ya, Kirim</button>
        </div>
    </div>
</div>