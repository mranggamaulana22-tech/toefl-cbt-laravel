{{-- ===== FOOTER NAVIGATION ===== --}}
<div class="flex items-center justify-between gap-3 border-t border-white/[0.06] bg-[#111827] p-4">
    <button @click="goToQuestion(activeQuestion - 1)" :disabled="activeQuestion === 0 || isAutoSubmitting" 
        class="rounded-xl border border-white/10 bg-white/[0.05] px-4 py-1.5 text-sm font-bold text-white/60 transition hover:border-white/20 hover:text-white disabled:opacity-50 sm:px-6 sm:py-2">Kembali</button>
    
    <button x-show="activeQuestion < totalQuestions - 1" @click="goToQuestion(activeQuestion + 1)" :disabled="isAutoSubmitting"
        class="rounded-xl bg-indigo-600 px-4 py-1.5 text-sm font-bold text-white transition hover:bg-indigo-700 disabled:opacity-60 sm:px-6 sm:py-2">Selanjutnya</button>
    
    <button x-show="activeQuestion === totalQuestions - 1" @click="showSubmitConfirm = true" :disabled="isAutoSubmitting"
        class="rounded-xl bg-emerald-600 px-5 py-1.5 text-sm font-bold text-white shadow-sm transition hover:bg-emerald-700 disabled:opacity-60 sm:px-8 sm:py-2">Selesai / Kirim</button>
</div>

{{-- ===== MODALS ===== --}}
<div x-show="showTimeUpNotice" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
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
        <p class="mt-2 text-sm leading-relaxed text-slate-600">Anda sedang dalam ujian. Tetap di halaman ini dan selesaikan semua soal terlebih dahulu.</p>

        <div class="mt-6 flex justify-end">
            <button type="button" @click="showBackGestureWarning = false" class="inline-flex items-center rounded-xl border border-slate-300 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Mengerti</button>
        </div>
    </div>
</div>

<div x-cloak x-show="showViolationWarning" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl border border-red-200 bg-white p-6 shadow-2xl">
        <p class="text-xs uppercase tracking-[0.2em] text-red-600 font-bold" x-text="violationWarningTitle"></p>
        <h3 class="mt-2 text-lg font-black text-slate-900">Peringatan Pelanggaran Tab</h3>
        <p class="mt-2 text-sm leading-relaxed text-slate-600" x-text="violationWarningMessage"></p>

        <div class="mt-6 flex justify-end">
            <button type="button" @click="showViolationWarning = false" class="inline-flex items-center rounded-xl border border-slate-300 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Mengerti</button>
        </div>
    </div>
</div>

<div x-cloak x-show="showSubmitConfirm" x-transition.opacity class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
        <p class="text-xs uppercase tracking-[0.2em] text-indigo-600 font-bold">Konfirmasi Pengumpulan</p>
        <h3 class="mt-2 text-xl font-black text-slate-900">Kirim jawaban?</h3>
        <p class="mt-2 text-sm leading-relaxed text-slate-600">Pastikan semua soal sudah dikerjakan sebelum mengirim. Jawaban tidak dapat diubah setelah pengiriman.</p>

        <div class="mt-6 flex justify-end gap-3">
            <button type="button" @click="showSubmitConfirm = false" class="inline-flex items-center rounded-xl border border-slate-300 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">Batal</button>
            <button type="button" @click="triggerAutoSubmit()" class="inline-flex items-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Kirim Jawaban</button>
        </div>
    </div>
</div>

{{-- ===== HIDDEN FORM ===== --}}
<form id="exam-form" action="{{ route('exam.submit') }}" method="POST" class="hidden">
    @csrf
    <template x-for="(ans, idx) in answers">
        <input type="hidden" :name="'answers[' + idx + ']'" :value="ans">
    </template>
</form>
