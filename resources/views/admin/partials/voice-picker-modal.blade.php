{{-- Voice Picker Modal Template --}}
<div x-data='{
    show: false,
    loading: false,
    saving: false,
    previewLoading: null,
    voices: { woman: [], man: [] },
    selectedWoman: @json($initialWoman),
    selectedMan: @json($initialMan),
    openModal() { this.show = true; },
    playPreview() {},
    save() {}
}'
    x-init='Object.assign($data, window.voicePickerModal({
        getUrl: @json($getUrl),
        saveUrl: @json($saveUrl),
        previewUrl: @json(route('admin.tts.preview-voice')),
        initialWoman: @json($initialWoman),
        initialMan: @json($initialMan)
    }))'>
    {{-- Trigger Button --}}
    <button type="button"
        @click="openModal()"
        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-lg transition active:scale-95 bg-blue-600 hover:bg-blue-700 text-white shadow-sm">
        <i class="fas fa-microphone"></i> Pengaturan Suara
    </button>

    {{-- Modal --}}
    <div x-show="show"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm px-4"
         @keydown.escape.window="show = false">
        <div @click.away="show = false"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="w-full max-w-md rounded-2xl border shadow-2xl bg-white border-slate-200 dark:bg-[#111827] dark:border-white/10">

            {{-- Modal Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-white/10">
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Pengaturan Suara TTS</h3>
                <button type="button" @click="show = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-white transition text-xl leading-none">&times;</button>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-4">
                {{-- Loading State --}}
                <template x-if="loading">
                    <div class="text-center py-6">
                        <i class="fas fa-spinner fa-spin text-2xl text-slate-400"></i>
                        <p class="mt-2 text-sm text-slate-500">Memuat daftar suara...</p>
                    </div>
                </template>

                {{-- Woman Voice Select --}}
                <template x-if="!loading">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            👩 Suara Wanita
                        </label>
                        <select x-model="selectedWoman" class="w-full px-3 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-900 dark:bg-[#1e293b] dark:border-white/10 dark:text-white">
                            <option value="">-- Pilih Suara --</option>
                            <template x-for="voice in voices.woman" :key="voice.id">
                                <option :value="voice.id" x-text="`${voice.label} - ${voice.desc}`"></option>
                            </template>
                        </select>
                    </div>
                </template>

                {{-- Man Voice Select --}}
                <template x-if="!loading">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            👨 Suara Pria
                        </label>
                        <select x-model="selectedMan" class="w-full px-3 py-2 rounded-lg text-sm font-medium bg-white border border-slate-200 text-slate-900 dark:bg-[#1e293b] dark:border-white/10 dark:text-white">
                            <option value="">-- Pilih Suara --</option>
                            <template x-for="voice in voices.man" :key="voice.id">
                                <option :value="voice.id" x-text="`${voice.label} - ${voice.desc}`"></option>
                            </template>
                        </select>
                    </div>
                </template>

                {{-- Preview Button --}}
                <template x-if="!loading">
                    <div class="space-y-3">
                        <button type="button"
                            :disabled="!selectedWoman && !selectedMan"
                            @click="playPreview(selectedWoman || selectedMan)"
                            class="w-full px-4 py-2 rounded-lg text-sm font-semibold transition active:scale-95 bg-slate-100 text-slate-700 hover:bg-slate-200 disabled:opacity-50 disabled:cursor-not-allowed dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                            <span x-show="!previewLoading"><i class="fas fa-play"></i> Preview Suara</span>
                            <span x-show="previewLoading"><i class="fas fa-spinner fa-spin"></i> Memutar...</span>
                        </button>
                        <audio x-ref="previewAudio" class="w-full"></audio>
                    </div>
                </template>
            </div>

            {{-- Modal Footer --}}
            <div class="flex gap-2 px-6 py-4 border-t border-slate-200 dark:border-white/10">
                <button type="button"
                    :disabled="saving"
                    @click="save()"
                    class="flex-1 px-4 py-2.5 rounded-lg font-semibold text-sm transition active:scale-95 bg-blue-600 hover:bg-blue-700 text-white disabled:opacity-50">
                    <span x-show="!saving">Simpan</span>
                    <span x-show="saving"><i class="fas fa-spinner fa-spin"></i> Menyimpan...</span>
                </button>
                <button type="button"
                    @click="show = false"
                    class="flex-1 px-4 py-2.5 rounded-lg font-semibold text-sm transition active:scale-95 bg-slate-100 text-slate-700 hover:bg-slate-200 dark:bg-white/5 dark:text-slate-300 dark:hover:bg-white/10">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>