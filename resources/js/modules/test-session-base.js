/**
 * Base test-session logic shared between Exam and Practice.
 *
 * Holds everything that was previously duplicated verbatim in
 * exam-scripts.blade.php and practice-scripts.blade.php:
 * - question navigation state
 * - anti-cheat (tab-switch / back-gesture) detection
 * - timer formatting & progress getters
 *
 * Exam/Practice extend this with their own initExam(), startTimer(),
 * and saveProgress() implementations (localStorage-only vs server-synced).
 *
 * @param {Object} config
 * @param {number} config.totalQuestions
 * @param {Array}  config.questionCategories
 * @param {Array}  config.questionIds
 * @param {string} config.progressKey   localStorage key
 * @param {string} config.lockKey       history.state key, e.g. 'examLocked' | 'practiceLocked'
 * @param {string} config.warningLabel  used in violation copy, e.g. 'ujian' | 'latihan'
 * @param {string} config.formId        id of the <form> to submit on auto-submit
 */
export function baseTestSession(config) {
    return {
        // ----- shared state -----
        activeQuestion: 0,
        totalQuestions: config.totalQuestions,
        answers: {},
        questionAnimating: false,
        showNavigator: true,
        questionCategories: config.questionCategories,
        questionIds: config.questionIds,
        progressKey: config.progressKey,
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

        lockKey: config.lockKey,
        warningLabel: config.warningLabel,
        formId: config.formId,

        // ----- shared: browser lock / anti-cheat -----
        lockBrowserNavigation() {
            const state = { [this.lockKey]: true };
            history.replaceState(state, '', window.location.href);
            history.pushState(state, '', window.location.href);
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
            }
        },

        showTabViolationWarning() {
            const warningMap = {
                1: {
                    title: 'Peringatan 1 dari 3',
                    message: `Kamu terdeteksi keluar dari tab atau halaman ${this.warningLabel}. Hindari berpindah agar ${this.warningLabel} tidak otomatis dikumpulkan.`
                },
                2: {
                    title: 'Peringatan 2 dari 3',
                    message: `Ini peringatan terakhir sebelum jawaban dikirim otomatis. Tetap di halaman ${this.warningLabel} sampai selesai.`
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

        bindLifecycleGuards() {
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

        // ----- shared: navigation -----
        goToQuestion(index) {
            if (index < 0 || index >= this.totalQuestions || index === this.activeQuestion) {
                return;
            }

            this.questionAnimating = true;

            setTimeout(() => {
                this.activeQuestion = index;
                this.saveProgress();
                this.onAfterNavigate?.();
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

        // ----- shared: formatting / getters -----
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
        },

        // ----- shared: submit -----
        finalizeAutoSubmit() {
            this.isAutoSubmitting = true;
            this.showTimeUpNotice = true;

            if (this.timerId) {
                clearInterval(this.timerId);
                this.timerId = null;
            }
        },

        submitForm() {
            document.getElementById(this.formId).submit();
        },
    };
}