<?php

namespace App\Services;

use App\Models\PracticeQuestion;
use App\Models\PracticeResult;
use App\Models\PracticeResultItem;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PracticeFlowService
{
    public function __construct(
        private QuestionSelectionService $questionSelectionService,
        private ScoringService $scoringService,
    ) {
    }

    public function generateOrGetQuestionIds(): array
    {
        return $this->questionSelectionService->generateOrderedPracticeQuestionIds();
    }

    public function loadQuestionsByIds(array $questionIds): Collection
    {
        return $this->questionSelectionService->orderByIds(
            PracticeQuestion::whereIn('id', $questionIds)->get(),
            $questionIds,
        );
    }

    public function submitPractice(int $userId, Collection $questions, array $userAnswers, ?string $startedAt = null): array
    {
        return DB::transaction(function () use ($userId, $questions, $userAnswers, $startedAt) {
            $correct = $this->scoringService->calculateCorrectAnswers($questions, $userAnswers);
            $scoreSummary = $this->scoringService->calculateScoreSummary($correct);

            $practiceResult = PracticeResult::create([
                'user_id' => $userId,
                'total_questions' => $questions->count(),
                'correct_listening' => $correct['listening'],
                'correct_structure' => $correct['structure'],
                'correct_reading' => $correct['reading'],
                'score_total' => $scoreSummary['total_score'],
                'started_at' => $startedAt ? Carbon::parse($startedAt) : null,
                'submitted_at' => now(),
            ]);

            foreach ($questions as $index => $question) {
                $userAnswer = $userAnswers[$index] ?? null;
                $snapshot = [
                    'question_text' => $question->question_text,
                    'passage' => $question->passage,
                    'option_a' => $question->option_a,
                    'option_b' => $question->option_b,
                    'option_c' => $question->option_c,
                    'option_d' => $question->option_d,
                    'correct_answer' => $question->correct_answer,
                    'category' => $question->category,
                    'audio_path' => $question->audio_path,
                    'audio_transcript' => $question->audio_transcript,
                ];

                PracticeResultItem::create([
                    'practice_result_id' => $practiceResult->id,
                    'practice_question_id' => $question->id,
                    'question_order' => $index + 1,
                    'user_answer' => $userAnswer,
                    'is_correct' => $userAnswer === $question->correct_answer,
                    'question_hash' => hash('sha256', Str::of(json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))->toString()),
                    'question_snapshot' => $snapshot,
                ]);
            }

            return [
                'practiceResult' => $practiceResult,
                'summary' => [
                    'score_total' => $scoreSummary['total_score'],
                    'correct_listening' => $correct['listening'],
                    'correct_structure' => $correct['structure'],
                    'correct_reading' => $correct['reading'],
                ],
            ];
        });
    }
}
