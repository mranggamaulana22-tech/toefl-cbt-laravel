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
     * Convert raw correct answers count to TOEFL scaled score.
     * Uses ETS-style lookup tables instead of a linear multiplier.
     */
    public function convertScore(int $correct, string $category): float
    {
        $category = strtolower(trim($category));

        $conversionTables = [
            'listening' => [
                0 => 24, 1 => 25, 2 => 26, 3 => 27, 4 => 28, 5 => 29, 6 => 30, 7 => 31, 8 => 32, 9 => 32,
                10 => 33, 11 => 35, 12 => 37, 13 => 37, 14 => 38, 15 => 41, 16 => 41, 17 => 42, 18 => 43, 19 => 44,
                20 => 45, 21 => 45, 22 => 46, 23 => 47, 24 => 47, 25 => 48, 26 => 48, 27 => 49, 28 => 49, 29 => 50,
                30 => 51, 31 => 51, 32 => 52, 33 => 52, 34 => 53, 35 => 54, 36 => 54, 37 => 55, 38 => 56, 39 => 57,
                40 => 57, 41 => 58, 42 => 59, 43 => 60, 44 => 61, 45 => 62, 46 => 63, 47 => 65, 48 => 66, 49 => 67,
                50 => 68,
            ],
            'structure' => [
                0 => 20, 1 => 20, 2 => 21, 3 => 22, 4 => 23, 5 => 25, 6 => 26, 7 => 27, 8 => 29, 9 => 31,
                10 => 33, 11 => 35, 12 => 36, 13 => 37, 14 => 38, 15 => 40, 16 => 40, 17 => 41, 18 => 42, 19 => 43,
                20 => 44, 21 => 45, 22 => 46, 23 => 47, 24 => 48, 25 => 49, 26 => 50, 27 => 51, 28 => 52, 29 => 53,
                30 => 54, 31 => 55, 32 => 56, 33 => 57, 34 => 58, 35 => 60, 36 => 61, 37 => 63, 38 => 65, 39 => 67,
                40 => 68,
            ],
            'reading' => [
                0 => 21, 1 => 22, 2 => 23, 3 => 23, 4 => 24, 5 => 25, 6 => 26, 7 => 27, 8 => 28, 9 => 28,
                10 => 29, 11 => 30, 12 => 31, 13 => 32, 14 => 34, 15 => 35, 16 => 36, 17 => 37, 18 => 38, 19 => 39,
                20 => 40, 21 => 41, 22 => 42, 23 => 43, 24 => 43, 25 => 44, 26 => 45, 27 => 46, 28 => 46, 29 => 47,
                30 => 48, 31 => 48, 32 => 49, 33 => 50, 34 => 51, 35 => 52, 36 => 52, 37 => 53, 38 => 54, 39 => 54,
                40 => 55, 41 => 56, 42 => 57, 43 => 58, 44 => 59, 45 => 60, 46 => 61, 47 => 63, 48 => 65, 49 => 66,
                50 => 67,
            ],
        ];

        if (!array_key_exists($category, $conversionTables)) {
            return 20.0;
        }

        $correct = max(0, min($correct, array_key_last($conversionTables[$category])));

        return (float) $conversionTables[$category][$correct];
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

            $userAnswer = strtoupper(trim((string) $userAnswers[$index]));
            $correctAnswer = strtoupper(trim((string) $question->correct_answer));

            if ($userAnswer !== $correctAnswer) {
                continue;
            }

            $category = strtolower(trim((string) ($question->category ?? '')));

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
        return (int) round(($listeningScore + $structureScore + $readingScore) * 10 / 3);
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
