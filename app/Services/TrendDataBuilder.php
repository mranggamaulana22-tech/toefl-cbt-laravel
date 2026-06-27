<?php

namespace App\Services;

use App\Models\PracticeResult;
use App\Models\Result;

/**
 * Builds trend data for AI suggestions
 * Analyzes previous test performance to determine trends and deltas
 */
class TrendDataBuilder
{
    /**
     * Build trend data from exam result history
     */
    public function buildExamTrendData(Result $result): array
    {
        $anchorTime = $result->submitted_at ?? $result->created_at;

        $previousResults = Result::query()
            ->where('user_id', $result->user_id)
            ->whereNotNull('submitted_at')
            ->where('id', '!=', $result->id)
            ->when($anchorTime, function ($query) use ($anchorTime) {
                $query->where('submitted_at', '<', $anchorTime);
            })
            ->latest('submitted_at')
            ->limit(5)
            ->get([
                'score_total',
                'correct_listening',
                'correct_structure',
                'correct_reading',
            ]);

        return $this->buildTrendPayload($previousResults);
    }

    /**
     * Build trend data from practice result history
     */
    public function buildPracticeTrendData(PracticeResult $practiceResult): array
    {
        $anchorTime = $practiceResult->submitted_at ?? $practiceResult->created_at;

        $previousResults = PracticeResult::query()
            ->where('user_id', $practiceResult->user_id)
            ->whereNotNull('submitted_at')
            ->where('id', '!=', $practiceResult->id)
            ->when($anchorTime, function ($query) use ($anchorTime) {
                $query->where('submitted_at', '<', $anchorTime);
            })
            ->latest('submitted_at')
            ->limit(5)
            ->get([
                'score_total',
                'correct_listening',
                'correct_structure',
                'correct_reading',
            ]);

        return $this->buildTrendPayload($previousResults);
    }

    /**
     * Build trend payload from collection of previous results
     */
    protected function buildTrendPayload($previousResults): array
    {
        $previousTestsCount = $previousResults->count();

        if ($previousTestsCount === 0) {
            return [
                'previous_tests_count' => 0,
            ];
        }

        $previousSession = $previousResults->first();

        return [
            'previous_tests_count' => $previousTestsCount,
            'previous_avg_score_total' => round((float) $previousResults->avg('score_total'), 2),
            'previous_avg_listening' => round((float) $previousResults->avg('correct_listening'), 2),
            'previous_avg_structure' => round((float) $previousResults->avg('correct_structure'), 2),
            'previous_avg_reading' => round((float) $previousResults->avg('correct_reading'), 2),
            'previous_session_score_total' => (float) ($previousSession->score_total ?? 0),
            'previous_session_listening' => (float) ($previousSession->correct_listening ?? 0),
            'previous_session_structure' => (float) ($previousSession->correct_structure ?? 0),
            'previous_session_reading' => (float) ($previousSession->correct_reading ?? 0),
        ];
    }
}
