/**
 * Exam session: extends baseTestSession with the simpler, localStorage-only
 * progress flow (no server sync, no devtools detection).
 *
 * Requires test-session-base.js to be loaded first.
 */
import { baseTestSession } from './test-session-base';

export function examSession(config) {
    return {
        ...baseTestSession({
            ...config,
            lockKey: 'examLocked',
            warningLabel: 'ujian',
            formId: 'exam-form',
        }),

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
            this.bindLifecycleGuards();
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

            this.finalizeAutoSubmit();
            this.saveProgress();

            setTimeout(() => {
                this.clearProgress();
                this.submitForm();
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
    };
}