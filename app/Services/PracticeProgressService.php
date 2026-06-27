<?php

namespace App\Services;

use App\Models\PracticeProgress;
use Illuminate\Http\Request;

/**
 * Service for managing practice progress persistence
 * Handles saving and retrieving practice session progress
 */
class PracticeProgressService
{
    /**
     * Get saved practice progress for user
     * Validates that question set hasn't changed
     */
    public function getProgress(Request $request, array $currentQuestionIds): ?array
    {
        $progress = PracticeProgress::where('user_id', $request->user()->id)->first();

        if (!$progress) {
            return null;
        }

        $savedQuestionIds = array_map('intval', $progress->question_ids ?? []);
        $normalizedCurrentIds = array_map('intval', $currentQuestionIds);

        // If question set changed, invalidate saved progress
        if ($savedQuestionIds !== $normalizedCurrentIds) {
            $progress->delete();
            return null;
        }

        return [
            'answers' => $progress->answers ?? [],
            'active_question' => (int) $progress->active_question,
            'time_left' => (int) $progress->time_left,
            'tab_violation_count' => (int) $progress->tab_violation_count,
            'question_ids' => $savedQuestionIds,
            'updated_at' => $progress->updated_at,
        ];
    }

    /**
     * Save or update practice progress for user
     */
    public function saveProgress(Request $request, array $validated, array $currentQuestionIds): bool
    {
        $savedQuestionIds = array_map('intval', $validated['question_ids']);
        $normalizedCurrentIds = array_map('intval', $currentQuestionIds);

        // Verify question IDs match current session
        if ($savedQuestionIds !== $normalizedCurrentIds) {
            return false;
        }

        PracticeProgress::updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'question_ids' => $savedQuestionIds,
                'answers' => $validated['answers'],
                'active_question' => $validated['active_question'],
                'time_left' => $validated['time_left'],
                'tab_violation_count' => $validated['tab_violation_count'],
            ]
        );

        return true;
    }

    /**
     * Clear progress for user
     */
    public function clearProgress(Request $request): void
    {
        PracticeProgress::where('user_id', $request->user()->id)->delete();
    }
}
