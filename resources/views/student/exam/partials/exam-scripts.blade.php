{{-- ===== JAVASCRIPT LOGIC ===== --}}
<script>
    function examSession() {
        return {
            activeQuestion: 0,
            totalQuestions: {{ $questions->count() }},
            answers: {},
            questionAnimating: false,
            showNavigator: true,
            questionCategories: @js($questions->pluck('category')->values()->all()),
            questionIds: @js($questions->pluck('id')->values()->all()),
            progressKey: 'exam_progress_user_{{ auth()->id() }}',
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
            
            initExam() {
                // MATIKAN BACKGROUND OTOMATIS SAAT UJIAN DIMULAI
                this.$store.bg.enabled = false;

                const saved = localStorage.getItem(this.progressKey);

                if (saved) {
                    try {
                        const parsed = JSON.parse(saved);
                        const sameQuestionSet = JSON.stringify(parsed.questionIds || []) === JSON.stringify(this.questionIds);

                        if (sameQuestionSet) {
                            this.answers = parsed.answers || {};
                            this.activeQuestion = Number.isInteger(parsed.activeQuestion) ? parsed.activeQuestion : 0;
                            this.timeLeft = Number.isInteger(parsed.timeLeft) ? parsed.timeLeft : this.timeLeft;
                            this.tabViolationCount = Number.isInteger(parsed.tabViolationCount) ? parsed.tabViolationCount : 0;
                            this.lastWarningShownForViolation = this.tabViolationCount;
                        }
                    } catch (e) {
                        localStorage.removeItem(this.progressKey);
                    }
                }

                if (this.timeLeft <= 0) {
                    this.timeLeft = 0;
                    this.triggerAutoSubmit();
                    return;
                }

                this.startTimer();
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
            lockBrowserNavigation() {
                history.replaceState({ examLocked: true }, '', window.location.href);
                history.pushState({ examLocked: true }, '', window.location.href);
                this.navigationLocked = true;
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
                        message: 'Kamu terdeteksi keluar dari tab atau halaman ujian. Hindari berpindah agar ujian tidak otomatis dikumpulkan.'
                    },
                    2: {
                        title: 'Peringatan 2 dari 3',
                        message: 'Ini peringatan terakhir sebelum jawaban dikirim otomatis. Tetap di halaman ujian sampai selesai.'
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

                this.saveProgress();

                setTimeout(() => {
                    this.clearProgress();
                    document.getElementById('exam-form').submit();
                }, 1200);
            },
            saveProgress() {
                localStorage.setItem(this.progressKey, JSON.stringify({
                    answers: this.answers,
                    activeQuestion: this.activeQuestion,
                    timeLeft: this.timeLeft,
                    questionIds: this.questionIds,
                    tabViolationCount: this.tabViolationCount
                }));
            },
            clearProgress() {
                localStorage.removeItem(this.progressKey);
            },
            goToQuestion(index) {
                if (index < 0 || index >= this.totalQuestions || index === this.activeQuestion) {
                    return;
                }

                this.questionAnimating = true;

                setTimeout(() => {
                    this.activeQuestion = index;
                    this.saveProgress();
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
