<?php

namespace App\Services;

use Exception;

/**
 * Service for calculating scores in TOEFL exams and practices
 * Consolidates all scoring logic from ExamController
 * Configuration: config/exam.php
 */
class ScoringService
{
    /**
     * Convert raw correct answers count to TOEFL score
     * Uses formula from config: base_score + (correct * multiplier)
     * Default: 20 + (correct * 1.2)
     */
    public function convertScore(int $correct, string $category): float
    {
        $baseScore = (float) config('exam.scoring.base_score', 20);
        $multiplier = (float) config('exam.scoring.multiplier', 1.2);

        return $baseScore + ($correct * $multiplier);
    }

    /**
     * Calculate correct answers by category from user responses
     * Returns array like: ['listening' => 10, 'structure' => 9, 'reading' => 8]
     */
    public function calculateCorrectAnswers($questions, array $userAnswers): array
    {
        $validCategories = ['listening', 'structure', 'reading'];
        $correct = array_fill_keys($validCategories, 0);

        foreach ($questions as $index => $question) {
            if (!isset($userAnswers[$index])) {
                continue;
            }

            // Strict comparison - must match exactly
            if ($userAnswers[$index] !== $question->correct_answer) {
                continue;
            }

            $category = strtolower((string) ($question->category ?? ''));

            // Only count if valid category
            if (in_array($category, $validCategories, true)) {
                $correct[$category]++;
            }
        }

        return $correct;
    }

    /**
     * Calculate total TOEFL score from section scores
     * Formula: round((s1 + s2 + s3) * 10 / 3)
     */
    public function calculateTotalScore(float $listeningScore, float $structureScore, float $readingScore): int
    {
        return round(($listeningScore + $structureScore + $readingScore) * 10 / 3);
    }

    /**
     * Build section scores and total score from raw correct-answer counts.
     */
    public function calculateScoreSummary(array $correct): array
    {
        $listeningScore = $this->convertScore((int) ($correct['listening'] ?? 0), 'listening');
        $structureScore = $this->convertScore((int) ($correct['structure'] ?? 0), 'structure');
        $readingScore = $this->convertScore((int) ($correct['reading'] ?? 0), 'reading');

        return [
            'listening_score' => $listeningScore,
            'structure_score' => $structureScore,
            'reading_score' => $readingScore,
            'total_score' => $this->calculateTotalScore($listeningScore, $structureScore, $readingScore),
        ];
    }
}
