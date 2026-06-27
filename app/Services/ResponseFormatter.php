<?php

namespace App\Services;

use App\Models\PracticeResult;
use App\Models\Result;

/**
 * Formats response payloads for AI suggestions
 * Builds consistent response structures for status, dashboard items, etc.
 */
class ResponseFormatter
{
    public function __construct(private AnalysisMetaBuilder $analysisMetaBuilder)
    {
    }

    /**
     * Resolve duration in minutes from started and submitted times
     */
    public function resolveDurationMinutes($startedAt, $submittedAt): int
    {
        return $startedAt && $submittedAt
            ? $submittedAt->diffInMinutes($startedAt)
            : 120;
    }

    /**
     * Build status payload for exam result
     */
    public function buildStatusPayload(Result|PracticeResult $record, ?array $parsed = null): array
    {
        $durationMinutes = $this->resolveDurationMinutes($record->started_at ?? null, $record->submitted_at ?? null);
        $analysisMeta = $record instanceof Result
            ? $this->analysisMetaBuilder->buildForResult($record, $durationMinutes)
            : $this->analysisMetaBuilder->buildForPractice($record, $durationMinutes);

        return [
            'status' => $record->ai_status ?: ($record->ai_suggestion ? 'done' : 'idle'),
            'suggestion' => $record->ai_suggestion,
            'parsed' => $parsed ?? [],
            'model' => $record->ai_model_used,
            'error' => $record->ai_error,
            'meta' => array_merge($analysisMeta, ['model' => $record->ai_model_used]),
        ];
    }

    /**
     * Build dashboard suggestion item
     */
    public function buildDashboardItem(Result|PracticeResult $item, ?array $parsed = null): array
    {
        $durationMinutes = $this->resolveDurationMinutes($item->started_at ?? null, $item->submitted_at ?? null);
        $analysisMeta = $item instanceof Result
            ? $this->analysisMetaBuilder->buildForResult($item, $durationMinutes)
            : $this->analysisMetaBuilder->buildForPractice($item, $durationMinutes);

        return [
            'id' => $item->id,
            'score_total' => $item->score_total,
            'correct_listening' => $item->correct_listening,
            'correct_structure' => $item->correct_structure,
            'correct_reading' => $item->correct_reading,
            'submitted_at' => $item->submitted_at,
            'ai_suggestion' => $item->ai_suggestion,
            'ai_generated_at' => $item->ai_generated_at,
            'ai_model_used' => $item->ai_model_used,
            'ai_status' => $item->ai_status,
            'ai_error' => $item->ai_error,
            'ai_parsed' => $parsed ?? [],
            'analysis_meta' => array_merge($analysisMeta, ['model' => $item->ai_model_used]),
        ];
    }
}
