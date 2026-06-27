<script>
    function practiceSession() {
        return {
            activeQuestion: 0, 
            totalQuestions: {{ $questions->count() }},
            answers: {},
            questionAnimating: false,
            showNavigator: true,
            questionCategories: @js($questions->pluck('category')->values()->all()),
            questionIds: @js($questions->pluck('id')->values()->all()),
            progressKey: 'practice_progress_user_{{ auth()->id() }}',
            timeLeft: 120 * 60,
            timerId: null,
            isAutoSubmitting: false,
            showTimeUpNotice: false,
            showSubmitConfirm: false,
            tabViolationCount: 0,
            lastWarningShownForViolation: 0,
            lastViolationCapturedAt: 0,
            navigationLocked: false,
            showViolationWarning: false,
            violationWarningTitle: '',
            violationWarningMessage: '',
            showBackGestureWarning: false,
            progressLoadUrl: '{{ route('api.v1.practice.progress.show') }}',
            progressSaveUrl: '{{ route('api.v1.practice.progress.save') }}',
            progressClearUrl: '{{ route('api.v1.practice.progress.clear') }}',
            csrfToken: '{{ csrf_token() }}',
            syncDebounceId: null,
            syncInFlight: false,
            devtoolsIntervalId: null,
            devtoolsTriggered: false,

            initExam: async function() {
                // MEMATIKAN BACKGROUND SECARA OTOMATIS
                this.$store.bg.enabled = false;

                this.loadLocalProgress();
                await this.loadServerProgress();

                if (this.timeLeft <= 0) {
                    this.timeLeft = 0;
                    this.triggerAutoSubmit();
                    return;
                }

                this.startTimer();
                this.startDevtoolsWatcher();
                this.lockBrowserNavigation();

                window.addEventListener('beforeunload', () => {
                    if (!this.isAutoSubmitting) {
                        this.saveProgress();
                    }
                });
                window.addEventListener('blur', () => {
                    if (!document.hidden && !this.isAutoSubmitting) {
                        this.captureTabViolation();
                    }
                });
                document.addEventListener('visibilitychange', () => {
                    if (document.hidden && !this.isAutoSubmitting) {
                        this.captureTabViolation();
                        return;
                    }

                    if (!document.hidden && this.tabViolationCount > 0 && this.tabViolationCount < 3 && this.lastWarningShownForViolation < this.tabViolationCount) {
                        this.showTabViolationWarning();
                    }
                });
                window.addEventListener('pageshow', () => {
                    if (!this.isAutoSubmitting) {
                        this.lockBrowserNavigation();
                    }
                });
                window.addEventListener('popstate', () => {
                    if (!this.isAutoSubmitting) {
                        this.showBackGestureWarning = true;
                        history.go(1);
                        this.lockBrowserNavigation();
                        setTimeout(() => {
                            if (this.showBackGestureWarning) {
                                this.showBackGestureWarning = false;
                            }
                        }, 3000);
                    }
                });
            },
            loadLocalProgress() {
                const saved = localStorage.getItem(this.progressKey);

                if (!saved) {
                    return;
                }

                try {
                    const parsed = JSON.parse(saved);
                    const sameQuestionSet = JSON.stringify(parsed.questionIds || []) === JSON.stringify(this.questionIds);

                    if (!sameQuestionSet) {
                        this.clearLocalProgress();
                        return;
                    }

                    this.answers = parsed.answers || {};
                    this.activeQuestion = Number.isInteger(parsed.activeQuestion) ? parsed.activeQuestion : 0;
                    this.timeLeft = Number.isInteger(parsed.timeLeft) ? parsed.timeLeft : this.timeLeft;
                    this.tabViolationCount = Number.isInteger(parsed.tabViolationCount) ? parsed.tabViolationCount : 0;
                    this.lastWarningShownForViolation = this.tabViolationCount;
                } catch (e) {
                    this.clearLocalProgress();
                }
            },
            async loadServerProgress() {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || this.csrfToken;
                    const response = await fetch(this.progressLoadUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'include'
                    });

                    if (!response.ok) {
                        return;
                    }

                    const data = await response.json();
                    const progress = data.progress;

                    if (!progress) {
                        return;
                    }

                    const sameQuestionSet = JSON.stringify(progress.question_ids || []) === JSON.stringify(this.questionIds);

                    if (!sameQuestionSet) {
                        return;
                    }

                    this.answers = progress.answers || {};
                    this.activeQuestion = Number.isInteger(progress.active_question) ? progress.active_question : this.activeQuestion;
                    this.timeLeft = Number.isInteger(progress.time_left) ? progress.time_left : this.timeLeft;
                    this.tabViolationCount = Number.isInteger(progress.tab_violation_count) ? progress.tab_violation_count : this.tabViolationCount;
                    this.lastWarningShownForViolation = this.tabViolationCount;
                    this.saveLocalProgress();
                } catch (e) {
                    // Keep local fallback if server sync fails.
                }
            },
            lockBrowserNavigation() {
                history.replaceState({ practiceLocked: true }, '', window.location.href);
                history.pushState({ practiceLocked: true }, '', window.location.href);
                this.navigationLocked = true;
            },
            startDevtoolsWatcher() {
                if (this.devtoolsIntervalId) {
                    clearInterval(this.devtoolsIntervalId);
                    this.devtoolsIntervalId = null;
                }

                this.devtoolsIntervalId = setInterval(() => {
                    if (this.isAutoSubmitting || this.devtoolsTriggered) {
                        return;
                    }

                    if (this.isDevtoolsOpened()) {
                        this.handleDevtoolsViolation();
                    }
                }, 1000);
            },
            isDevtoolsOpened() {
                const widthGap = window.outerWidth - window.innerWidth;
                const heightGap = window.outerHeight - window.innerHeight;
                const panelLikelyOpen = widthGap > 160 || heightGap > 160;

                const started = performance.now();
                debugger;
                const ended = performance.now();
                const debuggerPaused = (ended - started) > 120;

                return panelLikelyOpen || debuggerPaused;
            },
            handleDevtoolsViolation() {
                if (this.devtoolsTriggered || this.isAutoSubmitting) {
                    return;
                }

                this.devtoolsTriggered = true;
                this.tabViolationCount = 3;
                this.lastWarningShownForViolation = this.tabViolationCount;
                this.violationWarningTitle = 'Inspect Terdeteksi';
                this.violationWarningMessage = 'Sistem mendeteksi DevTools/inspect terbuka. Jawaban akan otomatis dikumpulkan.';
                this.showViolationWarning = true;

                this.saveProgress();
                this.syncProgressToServer(true);
                this.triggerAutoSubmit();
            },
            captureTabViolation() {
                const now = Date.now();

                if (now - this.lastViolationCapturedAt < 1000) {
                    return;
                }

                this.lastViolationCapturedAt = now;
                this.registerTabViolation();
                this.saveProgress();
            },
            registerTabViolation() {
                if (this.isAutoSubmitting) {
                    return;
                }

                this.tabViolationCount++;
                this.saveProgress();

                if (this.tabViolationCount >= 3) {
                    this.triggerAutoSubmit();
                    return;
                }
            },
            showTabViolationWarning() {
                const warningMap = {
                    1: {
                        title: 'Peringatan 1 dari 3',
                        message: 'Kamu terdeteksi keluar dari tab atau halaman latihan. Hindari berpindah agar latihan tidak otomatis dikumpulkan.'
                    },
                    2: {
                        title: 'Peringatan 2 dari 3',
                        message: 'Ini peringatan terakhir sebelum jawaban dikirim otomatis. Tetap di halaman latihan sampai selesai.'
                    }
                };

                const warning = warningMap[this.tabViolationCount];

                if (!warning || this.isAutoSubmitting) {
                    return;
                }

                this.violationWarningTitle = warning.title;
                this.violationWarningMessage = warning.message;
                this.showViolationWarning = true;
                this.lastWarningShownForViolation = this.tabViolationCount;

                setTimeout(() => {
                    if (this.showViolationWarning && this.lastWarningShownForViolation === this.tabViolationCount) {
                        this.showViolationWarning = false;
                    }
                }, 3500);
            },
            startTimer() {
                this.timerId = setInterval(() => {
                    if (this.timeLeft <= 0) {
                        return;
                    }

                    this.timeLeft--;

                    if (this.timeLeft % 5 === 0) {
                        this.saveProgress();
                    }

                    if (this.timeLeft % 15 === 0) {
                        this.syncProgressToServer();
                    }

                    if (this.timeLeft === 0) {
                        this.triggerAutoSubmit();
                    }
                }, 1000);
            },
            triggerAutoSubmit() {
                if (this.isAutoSubmitting) {
                    return;
                }

                this.isAutoSubmitting = true;
                this.showTimeUpNotice = true;

                if (this.timerId) {
                    clearInterval(this.timerId);
                    this.timerId = null;
                }

                if (this.devtoolsIntervalId) {
                    clearInterval(this.devtoolsIntervalId);
                    this.devtoolsIntervalId = null;
                }

                this.saveProgress();
                this.syncProgressToServer(true);

                setTimeout(() => {
                    this.clearProgress();
                    document.getElementById('practice-form').submit();
                }, 1200);
            },
            saveProgress() {
                this.saveLocalProgress();
                this.syncProgressToServer();
            },
            saveLocalProgress() {
                localStorage.setItem(this.progressKey, JSON.stringify({
                    answers: this.answers,
                    activeQuestion: this.activeQuestion,
                    timeLeft: this.timeLeft,
                    questionIds: this.questionIds,
                    tabViolationCount: this.tabViolationCount
                }));
            },
            syncProgressToServer(force = false) {
                if (this.syncDebounceId) {
                    clearTimeout(this.syncDebounceId);
                    this.syncDebounceId = null;
                }

                if (force) {
                    this.persistProgressToServer(true);
                    return;
                }

                this.syncDebounceId = setTimeout(() => {
                    this.persistProgressToServer(false);
                }, 700);
            },
            async persistProgressToServer(force = false) {
                if (this.syncInFlight || (this.isAutoSubmitting && !force)) {
                    return;
                }

                this.syncInFlight = true;

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || this.csrfToken;
                    await fetch(this.progressSaveUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'include',
                        body: JSON.stringify({
                            answers: this.answers,
                            active_question: this.activeQuestion,
                            time_left: this.timeLeft,
                            question_ids: this.questionIds,
                            tab_violation_count: this.tabViolationCount
                        })
                    });
                } catch (e) {
                    // Local copy remains available when sync fails.
                } finally {
                    this.syncInFlight = false;
                }
            },
            clearProgress() {
                this.clearLocalProgress();
                this.clearServerProgress();
            },
            clearLocalProgress() {
                localStorage.removeItem(this.progressKey);
            },
            async clearServerProgress() {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || this.csrfToken;
                    await fetch(this.progressClearUrl, {
                        method: 'DELETE',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'include'
                    });
                } catch (e) {
                    // Ignore clear errors because submit flow also clears on backend.
                }
            },
            goToQuestion(index) {
                if (index < 0 || index >= this.totalQuestions || index === this.activeQuestion) {
                    return;
                }

                this.questionAnimating = true;

                setTimeout(() => {
                    this.activeQuestion = index;
                    this.saveProgress();
                    this.syncProgressToServer();
                    this.scrollToQuestionTop();

                    requestAnimationFrame(() => {
                        requestAnimationFrame(() => {
                            this.questionAnimating = false;
                        });
                    });
                }, 90);
            },
            scrollToQuestionTop() {
                if (this.$refs.questionTop) {
                    this.$refs.questionTop.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            },
            formatTime(seconds) {
                const h = Math.floor(seconds / 3600);
                const m = Math.floor((seconds % 3600) / 60);
                const s = seconds % 60;
                return `${h}:${m < 10 ? '0' : ''}${m}:${s < 10 ? '0' : ''}${s}`;
            },
            get answeredCount() {
                return Object.keys(this.answers).length;
            },
            get progressPercent() {
                return this.totalQuestions > 0 ? Math.round((this.answeredCount / this.totalQuestions) * 100) : 0;
            },
            get timerPercent() {
                return Math.round((this.timeLeft / (120 * 60)) * 100);
            },
            get timerWarning() {
                return this.timeLeft <= 600;
            }
        };
    }
</script>