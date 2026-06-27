{{-- resources/views/student/ai-analysis/partials/scripts.blade.php --}}
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
window.dispatchAiGenerateRequest = function () {
    document.dispatchEvent(new CustomEvent('ai-generate-request'));
};

window.aiAnalysisPage = function () {
    return {
        loading: true,
        generating: false,
        loaded: false,
        error: null,
        activeTab: 'practice',
        selectedId: null,
        selectedItem: null,
        activeModel: @js(config('services.openrouter.model', 'default-model')),
        activeModelUsed: null,
        canScrollSessionLeft: false,
        canScrollSessionRight: false,
        activePlanDay: 1,
        dashboardUrl: @js(route('api.v1.suggestion.dashboard')),
        examGenerateUrlTemplate: @js(route('api.v1.suggestion.exam.generate', ['result' => '__ID__'])),
        practiceGenerateUrlTemplate: @js(route('api.v1.suggestion.practice.generate', ['practiceResult' => '__ID__'])),
        examStatusUrlTemplate: @js(route('api.v1.suggestion.exam.status', ['result' => '__ID__'])),
        practiceStatusUrlTemplate: @js(route('api.v1.suggestion.practice.status', ['practiceResult' => '__ID__'])),
        statusPollId: null,
        examHistory: [],
        practiceHistory: [],
        analysis: { mode: 'empty', raw: '', data: null, html: '' },
        analysisMeta: null,

        init() {
            this.fetchHistory();
        },

        updateSessionScrollButtons() { 
            // Opsional
        },

        scrollSession(direction, shift = 200) {
            const row = this.$refs.sessionScroll;
            if(row) {
                row.scrollBy({ left: direction * shift, behavior: 'smooth' });
                setTimeout(() => this.updateSessionScrollButtons(), 220);
            }
        },

        async fetchHistory() {
            this.loading = true;
            this.error = null;
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch(this.dashboardUrl, {
                    method: 'GET',
                    headers: { 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
                    },
                    credentials: 'include'
                });
                if (!res.ok) {
                    const errorData = await res.json().catch(() => ({}));
                    throw new Error(errorData.message || `Gagal memuat riwayat AI (${res.status}).`);
                }
                
                const data = await res.json();
                this.examHistory = data.recent_exams || [];
                this.practiceHistory = data.recent_practices || [];
                
                if (this.practiceHistory.length === 0 && this.examHistory.length > 0) {
                    this.activeTab = 'exam';
                }
                
                const firstItem = this.currentHistory[0] || null;
                if (firstItem) this.selectItem(firstItem, true);
                
                this.$nextTick(() => this.updateSessionScrollButtons());
            } catch (err) {
                this.error = err.message || 'Gagal memuat riwayat AI.';
            } finally {
                setTimeout(() => {
                    this.loading = false;
                    this.loaded = true;
                }, 300);
            }
        },

        get sessionTypeLabel() {
            return this.activeTab === 'exam' ? 'ujian' : 'latihan';
        },

        get currentHistory() {
            return this.activeTab === 'exam' ? this.examHistory : this.practiceHistory;
        },

        switchTab(tab) {
            this.stopStatusPolling();
            this.activeTab = tab;
            this.$nextTick(() => {
                this.updateSessionScrollButtons();
                if(this.$refs.sessionScroll) this.$refs.sessionScroll.scrollLeft = 0;
            });

            const firstItem = this.currentHistory[0] || null;
            if (firstItem) {
                this.selectItem(firstItem, true);
                return;
            }
            
            this.selectedId = null;
            this.selectedItem = null;
            this.analysisMeta = null;
            this.analysis = { mode: 'empty', raw: '', data: null, html: '' };
        },

        selectItem(item, autoRender = true) {
            this.selectedItem = item;
            this.selectedId = item.id;
            this.analysisMeta = item.analysis_meta || null;
            this.activeModelUsed = item.ai_model_used || this.analysisMeta?.model || null;
            this.activePlanDay = 1;

            if (this.shouldPoll(item)) {
                this.startStatusPolling(item);
            } else {
                this.stopStatusPolling();
            }

            if (autoRender) {
                if (item.ai_parsed) {
                    this.renderParsedSuggestion(item.ai_parsed, item.ai_suggestion || '', item.analysis_meta || null);
                } else {
                    this.renderSuggestion(item.ai_suggestion || '', item.analysis_meta || null);
                }
            }
        },

        planRows() {
            return this.analysis.data?.plan || [];
        },

        selectedPlan() {
            const rows = this.planRows();
            return rows.find((row) => Number(row.day) === Number(this.activePlanDay)) || rows[0] || null;
        },

        setPlanDay(day) {
            this.activePlanDay = day;
        },

        shouldPoll(item) {
            if (!item) return false;
            return item.ai_status === 'pending' || item.ai_status === 'processing';
        },

        stopStatusPolling() {
            if (this.statusPollId) {
                clearInterval(this.statusPollId);
                this.statusPollId = null;
            }
        },

        startStatusPolling(item) {
            this.stopStatusPolling();
            this.statusPollId = setInterval(async () => {
                if (!this.selectedItem || this.selectedItem.id !== item.id) {
                    this.stopStatusPolling();
                    return;
                }
                await this.fetchSuggestionStatus(item);
            }, 2500);
        },

        async fetchSuggestionStatus(item) {
            try {
                let url = this.activeTab === 'exam'
                    ? this.examStatusUrlTemplate
                    : this.practiceStatusUrlTemplate;

                url = url.replace('__ID__', item.id).replace('%5F%5FID%5F%5F', item.id);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch(url, {
                    method: 'GET',
                    headers: { 
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        ...(csrfToken && { 'X-CSRF-TOKEN': csrfToken })
                    },
                    credentials: 'include'
                });

                if (!res.ok) return;

                const payload = await res.json();
                item.ai_status = payload.status || 'idle';
                item.ai_error = payload.error || null;

                if (payload.suggestion) {
                    item.ai_suggestion = payload.suggestion;
                    item.ai_parsed = payload.parsed || item.ai_parsed || null;
                    item.analysis_meta = payload.meta || item.analysis_meta || null;
                    item.ai_model_used = payload.model || item.ai_model_used || null;
                    this.activeModelUsed = item.ai_model_used || this.activeModel;
                    
                    if (item.ai_parsed) {
                        this.renderParsedSuggestion(item.ai_parsed, item.ai_suggestion, item.analysis_meta);
                    } else {
                        this.renderSuggestion(item.ai_suggestion, item.analysis_meta);
                    }
                }

                if (item.ai_status === 'done') {
                    this.stopStatusPolling();
                    return;
                }
                
                if (item.ai_status === 'failed') {
                    this.error = payload.error || 'Analisa AI gagal diproses. Silakan coba lagi.';
                    this.stopStatusPolling();
                }
            } catch (err) {
                // Ignore transient network errors
            }
        },

        syncGlobalAnalyzeButton() { },

        async generateForSelected() {
            const selected = this.currentHistory.find((item) => item.id === this.selectedId);
            
            if (!selected || this.generating) {
                if (!selected) this.error = 'Pilih sesi terlebih dahulu sebelum menjalankan Analisa AI.';
                return;
            }

            this.generating = true;
            this.error = null;
            this.syncGlobalAnalyzeButton();

            try {
                let url = this.activeTab === 'exam'
                    ? this.examGenerateUrlTemplate
                    : this.practiceGenerateUrlTemplate;

                url = url.replace('__ID__', selected.id).replace('%5F%5FID%5F%5F', selected.id);
                
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                const csrfToken = tokenMeta ? tokenMeta.content : '{{ csrf_token() }}';

                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'include'
                });

                const payload = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(payload.error || 'Gagal membuat analisis AI. Cek konfigurasi server/API Key.');

                selected.ai_status = payload.status || (payload.suggestion ? 'done' : 'pending');
                selected.ai_error = null;

                if (payload.queued || selected.ai_status === 'pending' || selected.ai_status === 'processing') {
                    this.analysis = { mode: 'empty', raw: '', data: null, html: '' };
                    this.error = 'Permintaan analisa masuk antrean. Hasil akan muncul otomatis saat selesai.';
                    this.startStatusPolling(selected);
                    return;
                }

                selected.ai_suggestion = payload.suggestion || '';
                selected.ai_parsed = payload.parsed || null;
                selected.analysis_meta = payload.meta || selected.analysis_meta || null;
                this.activeModelUsed = payload.model || this.activeModel;
                
                if (selected.ai_parsed) {
                    this.renderParsedSuggestion(selected.ai_parsed, selected.ai_suggestion, selected.analysis_meta);
                } else {
                    this.renderSuggestion(selected.ai_suggestion, selected.analysis_meta);
                }
                
                this.stopStatusPolling();
            } catch (err) {
                this.error = err.message || 'Terjadi kesalahan sistem saat menghubungi AI.';
            } finally {
                this.generating = false;
                this.syncGlobalAnalyzeButton();
            }
        },

        fallbackAnalysisFromRaw(rawText = '') {
            return { strengths: '', weaknesses: '', error_pattern: '', motivation: '', priority_tip: '', warning: '', scores: {}, time_management: [], plan: [], __raw: rawText };
        },

        normalizeTimeManagement(data) {
            const rows = Array.isArray(data.time_management) ? data.time_management : [];
            if (rows.length > 0) {
                return rows.slice(0, 4).map((row) => ({ section: row.section || 'Section', minutes: Number(row.minutes || 0) }));
            }
            return [{ section: 'Listening', minutes: 20 }, { section: 'Reading', minutes: 15 }, { section: 'Structure', minutes: 10 }, { section: 'Review', minutes: 10 }];
        },

        resolveMotivation(data) {
            if (typeof data.motivation === 'string' && data.motivation.trim() !== '') return data.motivation.trim();
            if (typeof data.quick_win === 'string' && data.quick_win.trim() !== '') return data.quick_win.trim();
            return '';
        },

        normalizePlan(data) {
            const modern = Array.isArray(data.plan) ? data.plan : [];
            const legacy = Array.isArray(data.day_plan) ? data.day_plan : [];
            const source = modern.length > 0 ? modern : legacy;
            return source.slice(0, 3).map((item, idx) => ({
                day: Number(item.day ?? idx + 1),
                title: item.title || item.task || `Hari ${idx + 1}`,
                desc: item.desc || `${item.task || 'Latihan ringkas'} (${item.duration || '30 mnt'})`,
                tags: Array.isArray(item.tags) ? item.tags : [item.focus || 'Mock Test'],
            }));
        },

        normalizeAnalysis(data) {
            return {
                strengths: data.strengths || '',
                weaknesses: data.weaknesses || '',
                error_pattern: data.error_pattern || '',
                motivation: this.resolveMotivation(data),
                priority_tip: data.priority_tip || '',
                warning: data.warning || '',
                scores: data.scores || {},
                time_management: this.normalizeTimeManagement(data),
                plan: this.normalizePlan(data),
            };
        },

        renderSuggestion(text, meta = null) {
            this.analysisMeta = meta || this.analysisMeta;
            this.activeModelUsed = (meta && meta.model) ? meta.model : this.activeModelUsed;
            this.analysis = { mode: 'json', raw: text, data: this.normalizeAnalysis(this.fallbackAnalysisFromRaw(text)), html: '' };
        },

        renderParsedSuggestion(parsed, raw = '', meta = null) {
            this.analysisMeta = meta || this.analysisMeta;
            this.activeModelUsed = (meta && meta.model) ? meta.model : this.activeModelUsed;
            this.analysis = { mode: 'json', raw, data: this.normalizeAnalysis(parsed || {}), html: '' };
        },

        aiStatusLabel() {
            const status = String(this.selectedItem?.ai_status || '').toLowerCase();
            if (status === 'pending') return 'Antri';
            if (status === 'processing') return 'Diproses';
            if (status === 'failed') return 'Gagal';
            if (status === 'done' || this.selectedItem?.ai_suggestion) return 'Selesai';
            return 'Belum';
        },

        aiStatusClass() {
            const status = String(this.selectedItem?.ai_status || '').toLowerCase();
            if (status === 'pending' || status === 'processing') return 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-900/50 dark:bg-amber-900/20 dark:text-amber-400';
            if (status === 'failed') return 'border-red-200 bg-red-50 text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-400';
            if (status === 'done' || this.selectedItem?.ai_suggestion) return 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-900/50 dark:bg-emerald-900/20 dark:text-emerald-400';
            return 'border-slate-200 bg-slate-50 text-slate-600 dark:border-slate-700 dark:bg-slate-800 dark:text-slate-300';
        },

        modelFamily() {
            const model = String(this.resolvedModel()).toLowerCase();
            if (model.includes('llama')) return 'llama';
            if (model.includes('gpt')) return 'gpt';
            if (model.includes('gemini')) return 'gemini';
            return 'other';
        },

        resolvedModel() {
            return this.activeModelUsed || this.analysisMeta?.model || this.selectedItem?.ai_model_used || 'unknown';
        }
    };
};
</script>