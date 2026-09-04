/**
 * voice-picker.js
 *
 * Alpine.data() untuk modal Voice Picker (pilih suara Woman/Man + preview).
 * Dipakai di 2 konteks:
 * 1. Global (Soal Latihan) — getUrl diisi, ambil setting via AJAX saat modal dibuka.
 * 2. Per-paket (Soal Ujian) — getUrl null, initialWoman/initialMan dikirim langsung dari server.
 *
 * Cara pakai di Blade:
 *   x-data="voicePicker({ getUrl: '...', saveUrl: '...', previewUrl: '...', initialWoman: '...', initialMan: '...' })"
 */
export default function voicePicker(options = {}) {
    return {
        show: false,
        loading: false,
        saving: false,
        previewLoading: null,
        voices: { woman: [], man: [] },
        selectedWoman: options.initialWoman || null,
        selectedMan: options.initialMan || null,

        openModal() {
            this.show = true;

            if (this.voices.woman.length > 0) {
                return;
            }

            if (options.getUrl) {
                this.loading = true;
                fetch(options.getUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                })
                    .then((r) => r.json())
                    .then((data) => {
                        this.voices = data.voices;
                        this.selectedWoman = this.selectedWoman || data.voice_woman;
                        this.selectedMan = this.selectedMan || data.voice_man;
                        this.loading = false;
                    })
                    .catch(() => {
                        this.loading = false;
                        alert('Gagal memuat pilihan suara.');
                    });
            } else {
                this.voices = window.ttsVoices || { woman: [], man: [] };
            }
        },

        playPreview(voiceId) {
            this.previewLoading = voiceId;

            fetch(options.previewUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ voice_id: voiceId }),
            })
                .then((r) => r.json())
                .then((data) => {
                    this.previewLoading = null;
                    if (data.success) {
                        this.$refs.previewAudio.src = data.audio_url;
                        this.$refs.previewAudio.play().catch(() => {
                            alert('Audio preview tidak dapat diputar oleh browser.');
                        });
                    } else {
                        alert(data.message || 'Gagal memuat preview.');
                    }
                })
                .catch(() => {
                    this.previewLoading = null;
                    alert('Terjadi kesalahan saat memuat preview.');
                });
        },

        save() {
            if (!this.selectedWoman || !this.selectedMan) {
                alert('Pilih dulu suara Wanita dan Pria.');
                return;
            }

            this.saving = true;

            fetch(options.saveUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    voice_woman: this.selectedWoman,
                    voice_man: this.selectedMan,
                }),
            })
                .then((r) => r.json())
                .then((data) => {
                    this.saving = false;
                    if (data.success) {
                        this.show = false;
                        alert('Pengaturan suara berhasil disimpan.');
                    } else {
                        alert('Gagal menyimpan pengaturan.');
                    }
                })
                .catch(() => {
                    this.saving = false;
                    alert('Terjadi kesalahan saat menyimpan.');
                });
        },
    };
}