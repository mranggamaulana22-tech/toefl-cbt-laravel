/**
 * Practice session: extends baseTestSession with server-synced progress
 * and devtools-open detection, on top of the shared anti-cheat/navigation logic.
 *
 * Requires test-session-base.js to be loaded first.
 */
import { baseTestSession } from './test-session-base';

export function practiceSession(config) {
    return {
        ...baseTestSession({
            ...config,
            lockKey: 'practiceLocked',
            warningLabel: 'latihan',
            formId: 'practice-form',
        }),

        progressLoadUrl: config.progressLoadUrl,
        progressSaveUrl: config.progressSaveUrl,
        progressClearUrl: config.progressClearUrl,
        csrfToken: config.csrfToken,
        syncDebounceId: null,
        syncInFlight: false,
        devtoolsIntervalId: null,
        devtoolsTriggered: false,

        async initExam() {
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
            this.bindLifecycleGuards();
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

            this.finalizeAutoSubmit();

            if (this.devtoolsIntervalId) {
                clearInterval(this.devtoolsIntervalId);
                this.devtoolsIntervalId = null;
            }

            this.saveProgress();
            this.syncProgressToServer(true);

            setTimeout(() => {
                this.clearProgress();
                this.submitForm();
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

        // override: also sync to server after navigating between questions
        onAfterNavigate() {
            this.syncProgressToServer();
        },
    };
}