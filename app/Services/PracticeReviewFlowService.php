<?php

namespace App\Services;

use App\Models\PracticeQuestionReview;
use App\Models\PracticeReviewUsage;
use App\Models\PracticeResult;
use App\Models\PracticeResultItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PracticeReviewFlowService
{
    public function __construct(private PracticeReviewService $practiceReviewService)
    {
    }

    public function sessionsForUser(int $userId): LengthAwarePaginator
    {
        return PracticeResult::where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->paginate(10);
    }

    public function showPayload(int $userId, PracticeResult $practiceResult): array
    {
        $practiceResult->load(['items' => function ($query) {
            $query->with('practiceQuestion:id,category,passage,audio_path,audio_transcript,question_text,option_a,option_b,option_c,option_d,correct_answer')
                ->orderBy('question_order');
        }]);

        $items = $practiceResult->items;
        $wrongCount = $items->where('is_correct', false)->count();
        $correctCount = $items->where('is_correct', true)->count();

        return [
            'practiceResult' => $practiceResult,
            'items' => $items,
            'summary' => [
                'total' => $items->count(),
                'wrong' => $wrongCount,
                'correct' => $correctCount,
            ],
            'reviewQuotaRemaining' => $this->practiceReviewService->remainingQuota($userId),
        ];
    }

    public function reviewItemForUser(int $userId, PracticeResultItem $item): array
    {
        $cached = PracticeQuestionReview::where('question_hash', $item->question_hash)
            ->where(function ($query) {
                $query->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->first();

        if ($cached) {
            return DB::transaction(function () use ($userId, $item, $cached) {
                $generatedToday = PracticeReviewUsage::where('user_id', $userId)
                    ->where('generated', true)
                    ->whereDate('created_at', Carbon::today())
                    ->lockForUpdate()
                    ->count();

                PracticeReviewUsage::create([
                    'user_id' => $userId,
                    'practice_result_item_id' => $item->id,
                    'question_hash' => $item->question_hash,
                    'from_cache' => true,
                    'generated' => false,
                ]);

                return [
                    'status' => 200,
                    'body' => [
                        'cached' => true,
                        'review' => $this->practiceReviewService->buildResponsePayload($item, $cached->ai_review_json ?: [], true),
                        'model' => $cached->ai_model_used,
                        'quota_remaining' => max(0, 10 - $generatedToday),
                    ],
                ];
            });
        }

        return DB::transaction(function () use ($userId, $item) {
            $lockedUser = User::whereKey($userId)->lockForUpdate()->first();

            if (!$lockedUser) {
                return [
                    'status' => 404,
                    'body' => [
                        'message' => 'User tidak ditemukan.',
                    ],
                ];
            }

            $generatedToday = PracticeReviewUsage::where('user_id', $userId)
                ->where('generated', true)
                ->whereDate('created_at', Carbon::today())
                ->lockForUpdate()
                ->count();

            if ($generatedToday >= 10) {
                return [
                    'status' => 429,
                    'body' => [
                        'message' => 'Batas review AI harian sudah tercapai. Coba lagi besok.',
                    ],
                ];
            }

            $review = $this->practiceReviewService->generateReview($this->practiceReviewService->buildPromptData($item));

            if (!$review) {
                return [
                    'status' => 500,
                    'body' => [
                        'message' => $this->practiceReviewService->getLastError() ?: 'Gagal membuat review AI.',
                    ],
                ];
            }

            PracticeQuestionReview::updateOrCreate(
                ['question_hash' => $item->question_hash],
                [
                    'question_snapshot' => $item->question_snapshot ?: $this->practiceReviewService->buildSnapshot($item),
                    'ai_review_json' => $review,
                    'ai_review_text' => $this->practiceReviewService->flattenReview($review),
                    'ai_model_used' => $this->practiceReviewService->getLastUsedModel(),
                    'ai_generated_at' => now(),
                    'expires_at' => now()->addDays(14),
                ]
            );

            PracticeReviewUsage::create([
                'user_id' => $userId,
                'practice_result_item_id' => $item->id,
                'question_hash' => $item->question_hash,
                'from_cache' => false,
                'generated' => true,
            ]);

            return [
                'status' => 200,
                'body' => [
                    'cached' => false,
                    'review' => $this->practiceReviewService->buildResponsePayload($item, $review, false),
                    'model' => $this->practiceReviewService->getLastUsedModel(),
                    'quota_remaining' => max(0, 10 - ($generatedToday + 1)),
                ],
            ];
        });
    }
}
