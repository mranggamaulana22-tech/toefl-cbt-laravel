<?php

namespace App\Services;

use App\Jobs\GenerateAiSuggestionJob;
use App\Models\PracticeResult;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AiSuggestionService
{
    public function __construct(
        private AiSuggestionParser $aiSuggestionParser,
        private TrendDataBuilder $trendDataBuilder,
        private AnalysisMetaBuilder $analysisMetaBuilder,
        private ResponseFormatter $responseFormatter,
    ) {
    }

    public function generateForExam(Request $request, Result $result)
    {
        return $this->generateSuggestion(
            request: $request,
            record: $result,
            type: 'exam',
            trendBuilder: fn (Result $record) => $this->trendDataBuilder->buildExamTrendData($record),
            currentBuilder: fn (Result $record, int $durationMinutes) => [
                'correct_listening' => $record->correct_listening,
                'correct_structure' => $record->correct_structure,
                'correct_reading' => $record->correct_reading,
                'score_total' => $record->score_total,
                'duration_minutes' => $durationMinutes,
            ],
        );
    }

    public function generateForPractice(Request $request, PracticeResult $practiceResult)
    {
        return $this->generateSuggestion(
            request: $request,
            record: $practiceResult,
            type: 'practice',
            trendBuilder: fn (PracticeResult $record) => $this->trendDataBuilder->buildPracticeTrendData($record),
            currentBuilder: fn (PracticeResult $record, int $durationMinutes) => [
                'correct_listening' => $record->correct_listening,
                'correct_structure' => $record->correct_structure,
                'correct_reading' => $record->correct_reading,
                'score_total' => $record->score_total,
                'duration_minutes' => $durationMinutes,
            ],
        );
    }

    public function buildExamStatusPayload(Result $result): array
    {
        return $this->buildStatusPayload($result);
    }

    public function buildPracticeStatusPayload(PracticeResult $practiceResult): array
    {
        return $this->buildStatusPayload($practiceResult);
    }

    public function dashboardSuggestionsForUser(int $userId): array
    {
        $latestExam = Result::where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->first();

        $latestPractice = PracticeResult::where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->first();

        $recentExams = Result::where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->limit(5)
            ->get(['id', 'score_total', 'correct_listening', 'correct_structure', 'correct_reading', 'submitted_at', 'started_at', 'ai_suggestion', 'ai_generated_at', 'ai_model_used', 'ai_parsed_json', 'ai_parser_version', 'ai_status', 'ai_error'])
            ->map(fn (Result $item) => $this->responseFormatter->buildDashboardItem($item, $this->parsedSuggestion($item, false)));

        $recentPractices = PracticeResult::where('user_id', $userId)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->limit(5)
            ->get(['id', 'score_total', 'correct_listening', 'correct_structure', 'correct_reading', 'submitted_at', 'started_at', 'ai_suggestion', 'ai_generated_at', 'ai_model_used', 'ai_parsed_json', 'ai_parser_version', 'ai_status', 'ai_error'])
            ->map(fn (PracticeResult $item) => $this->responseFormatter->buildDashboardItem($item, $this->parsedSuggestion($item, false)));

        return [
            'latest_exam_suggestion' => $latestExam?->ai_suggestion,
            'latest_exam_id' => $latestExam?->id,
            'latest_practice_suggestion' => $latestPractice?->ai_suggestion,
            'latest_practice_id' => $latestPractice?->id,
            'recent_exams' => $recentExams,
            'recent_practices' => $recentPractices,
        ];
    }

    protected function buildStatusPayload(Result|PracticeResult $record): array
    {
        return $this->responseFormatter->buildStatusPayload($record, $this->parsedSuggestion($record, false));
    }

    protected function generateSuggestion(Request $request, Result|PracticeResult $record, string $type, callable $trendBuilder, callable $currentBuilder)
    {
        $durationMinutes = $this->responseFormatter->resolveDurationMinutes($record->started_at ?? null, $record->submitted_at ?? null);
        $analysisMeta = $record instanceof Result
            ? $this->analysisMetaBuilder->buildForResult($record, $durationMinutes)
            : $this->analysisMetaBuilder->buildForPractice($record, $durationMinutes);

        if ($record->ai_suggestion) {
            $usedModel = $record->ai_model_used ?: null;

            return response()->json([
                'suggestion' => $record->ai_suggestion,
                'parsed' => $this->parsedSuggestion($record),
                'model' => $usedModel,
                'status' => $record->ai_status ?: 'done',
                'meta' => array_merge($analysisMeta, ['model' => $usedModel]),
                'cached' => true,
            ]);
        }

        if (in_array($record->ai_status, ['pending', 'processing'], true)) {
            return response()->json([
                'queued' => true,
                'status' => $record->ai_status,
                'meta' => array_merge($analysisMeta, ['model' => $record->ai_model_used]),
            ]);
        }

        $suggestionData = array_merge(
            $currentBuilder($record, $durationMinutes),
            $trendBuilder($record),
        );

        $record->update([
            'ai_status' => 'pending',
            'ai_error' => null,
            'ai_requested_at' => now(),
        ]);

        $queued = Cache::add($this->aiQueueLockKey($type, (int) $record->id), true, now()->addMinutes(15));

        if (!$queued) {
            return response()->json([
                'queued' => true,
                'status' => 'pending',
                'meta' => array_merge($analysisMeta, ['model' => $record->ai_model_used]),
            ]);
        }

        GenerateAiSuggestionJob::dispatch($type, (int) $record->id, (int) $request->user()->id, $suggestionData);

        $record->refresh();

        if ($record->ai_suggestion) {
            $usedModel = $record->ai_model_used ?: null;

            return response()->json([
                'suggestion' => $record->ai_suggestion,
                'parsed' => $this->parsedSuggestion($record),
                'model' => $usedModel,
                'status' => $record->ai_status ?: 'done',
                'meta' => array_merge($analysisMeta, ['model' => $usedModel]),
                'cached' => false,
            ]);
        }

        return response()->json([
            'queued' => true,
            'status' => $record->ai_status ?: 'pending',
            'meta' => array_merge($analysisMeta, ['model' => $record->ai_model_used]),
        ]);
    }

    protected function aiQueueLockKey(string $type, int $recordId): string
    {
        return "ai-suggestion:{$type}:{$recordId}";
    }

    protected function parsedSuggestion(Result|PracticeResult $record, bool $persist = true): ?array
    {
        if (is_array($record->ai_parsed_json) && !empty($record->ai_parsed_json)) {
            return $record->ai_parsed_json;
        }

        if (blank($record->ai_suggestion)) {
            return null;
        }

        $parsed = $this->aiSuggestionParser->parse($record->ai_suggestion);

        if (!$persist) {
            return $parsed;
        }

        $record->forceFill([
            'ai_parsed_json' => $parsed,
            'ai_parser_version' => $this->aiSuggestionParser->version(),
        ])->save();

        return $parsed;
    }
}
