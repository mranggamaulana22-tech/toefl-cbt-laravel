<script>
    function practiceReviewPage() {
        return {
            loaded: false,
            selectedItemId: {{ $items->first()?->id ?? 0 }},
            loadingId: null,
            reviewCache: {},
            quotaRemaining: {{ (int) $reviewQuotaRemaining }},
            activeQuestion: 0,

            // --- VARIABEL TAMBAHAN UNTUK MENCEGAH ERROR ALPINE ---
            answers: {},
            showTimeUpNotice: false,
            showBackGestureWarning: false,
            showViolationWarning: false,
            violationWarningTitle: '',
            violationWarningMessage: '',
            showSubmitConfirm: false,
            clearProgress() {},
            // -----------------------------------------------------

            questionCategories: @js($items->pluck('category')->values()->all()),
            items: @js($items->map(fn ($item) => [
                'id' => $item->id,
                'order' => $item->question_order,
                'is_correct' => (bool) $item->is_correct,
                'category' => $item->category,
            ])->values()),
            reviewUrlTemplate: @js(route('api.v1.review.item', ['practiceResult' => $practiceResult->id, 'item' => '__ITEM__'])),
            showNavigator: true,
            questionAnimating: false,

            get totalQuestions() {
                return this.items.length;
            },
            currentItem() {
                return this.items.find((item) => item.id === this.selectedItemId) || this.items[0] || null;
            },
            currentItemId() {
                return this.currentItem() ? this.currentItem().id : 0;
            },
            navigatorLabel() {
                const item = this.currentItem();
                if (!item) return '0 / 0';
                return `${item.order} / ${this.totalQuestions}`;
            },
            selectItem(itemId) {
                if (this.selectedItemId === itemId) return;

                this.questionAnimating = true;
                this.selectedItemId = itemId;
                this.activeQuestion = this.items.findIndex(item => item.id === itemId);

                if (this.$refs.questionTop) {
                    this.$refs.questionTop.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }

                setTimeout(() => {
                    this.questionAnimating = false;
                }, 180);
            },
            prevQuestion() {
                const index = this.items.findIndex((item) => item.id === this.selectedItemId);
                if (index > 0) this.selectItem(this.items[index - 1].id);
            },
            nextQuestion() {
                const index = this.items.findIndex((item) => item.id === this.selectedItemId);
                if (index >= 0 && index < this.items.length - 1) this.selectItem(this.items[index + 1].id);
            },
            async loadReview(itemId) {
                if (this.loadingId) return;

                this.loadingId = itemId;

                try {
                    const url = this.reviewUrlTemplate.replace('__ITEM__', itemId);
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'include'
                    });

                    const payload = await res.json().catch(() => ({}));

                    if (!res.ok) {
                        throw new Error(payload.message || 'Gagal memuat review AI.');
                    }

                    this.reviewCache[itemId] = payload.review || null;
                    if (typeof payload.quota_remaining === 'number') {
                        this.quotaRemaining = payload.quota_remaining;
                    }
                } catch (error) {
                    alert(error.message || 'Gagal memuat review AI.');
                } finally {
                    this.loadingId = null;
                }
            },
            init() {
                setTimeout(() => {
                    this.loaded = true;
                    if (this.selectedItemId) {
                        this.activeQuestion = this.items.findIndex(item => item.id === this.selectedItemId);
                        this.selectItem(this.selectedItemId);
                    } else {
                        this.activeQuestion = 0;
                    }
                }, 350);
            },
        };
    }
</script>
