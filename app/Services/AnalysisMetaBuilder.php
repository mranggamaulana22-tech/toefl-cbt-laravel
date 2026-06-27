<?php

namespace App\Services;

use App\Models\PracticeResult;
use App\Models\Result;

/**
 * Builds analysis metadata for AI suggestions
 * Combines current performance with trend data to create analysis context
 */
class AnalysisMetaBuilder
{
    public function __construct(private TrendDataBuilder $trendDataBuilder)
    {
    }

    /**
     * Build analysis metadata for exam result
     */
    public function buildForResult(Result $result, int $durationMinutes): array
    {
        $trend = $this->trendDataBuilder->buildExamTrendData($result);

        return $this->build([
            'score_total' => (float) $result->score_total,
            'correct_listening' => (float) $result->correct_listening,
            'correct_structure' => (float) $result->correct_structure,
            'correct_reading' => (float) $result->correct_reading,
            'duration_minutes' => $durationMinutes,
        ], $trend);
    }

    /**
     * Build analysis metadata for practice result
     */
    public function buildForPractice(PracticeResult $practiceResult, int $durationMinutes): array
    {
        $trend = $this->trendDataBuilder->buildPracticeTrendData($practiceResult);

        return $this->build([
            'score_total' => (float) $practiceResult->score_total,
            'correct_listening' => (float) $practiceResult->correct_listening,
            'correct_structure' => (float) $practiceResult->correct_structure,
            'correct_reading' => (float) $practiceResult->correct_reading,
            'duration_minutes' => $durationMinutes,
        ], $trend);
    }

    /**
     * Build combined analysis metadata
     * Merges current performance scores with historical trend data
     */
    protected function build(array $current, array $trend): array
    {
        $previousTestsCount = (int) ($trend['previous_tests_count'] ?? 0);
        $previousAvgScoreTotal = $previousTestsCount > 0 ? (float) ($trend['previous_avg_score_total'] ?? 0) : null;
        $previousAvgListening = $previousTestsCount > 0 ? (float) ($trend['previous_avg_listening'] ?? 0) : null;
        $previousAvgStructure = $previousTestsCount > 0 ? (float) ($trend['previous_avg_structure'] ?? 0) : null;
        $previousAvgReading = $previousTestsCount > 0 ? (float) ($trend['previous_avg_reading'] ?? 0) : null;

        $currentTotal = (float) ($current['score_total'] ?? 0);
        $currentListening = (float) ($current['correct_listening'] ?? 0);
        $currentStructure = (float) ($current['correct_structure'] ?? 0);
        $currentReading = (float) ($current['correct_reading'] ?? 0);

        return [
            'current' => $current,
            'trend' => [
                'previous_tests_count' => $previousTestsCount,
                'previous_avg_score_total' => $previousAvgScoreTotal,
                'previous_avg_listening' => $previousAvgListening,
                'previous_avg_structure' => $previousAvgStructure,
                'previous_avg_reading' => $previousAvgReading,
                'delta_total' => $previousTestsCount > 0 ? round($currentTotal - $previousAvgScoreTotal, 2) : 0,
                'delta_listening' => $previousTestsCount > 0 ? round($currentListening - $previousAvgListening, 2) : 0,
                'delta_structure' => $previousTestsCount > 0 ? round($currentStructure - $previousAvgStructure, 2) : 0,
                'delta_reading' => $previousTestsCount > 0 ? round($currentReading - $previousAvgReading, 2) : 0,
            ],
        ];
    }
}
