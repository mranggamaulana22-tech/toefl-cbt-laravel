<?php

namespace App\Services;

use Exception;

/**
 * Service for calculating scores in TOEFL exams and practices
 * Consolidates all scoring logic from ExamController
 * Configuration: config/exam.php
 *
 * CATATAN SKORING:
 * ETS tidak mempublikasikan tabel konversi raw-to-scaled TOEFL ITP resmi
 * ke publik (proses aslinya menggunakan "equating" statistik yang berbeda
 * tiap paket soal dan bersifat proprietary). Oleh karena itu, service ini
 * menggunakan pendekatan LINEAR SCALING sebagai simplifikasi yang wajar
 * dan transparan, dengan floor/ceiling mengikuti skala resmi TOEFL ITP:
 *   - Listening        : skor 31 - 68 (raw max 50)
 *   - Structure & WE    : skor 31 - 68 (raw max 40)
 *   - Reading           : skor 31 - 67 (raw max 50)
 * Pendekatan ini didokumentasikan sebagai simplifikasi di BAB III/IV TA.
 */
class ScoringService
{
    /**
     * Konfigurasi skala tiap kategori: [max_raw, min_scaled, max_scaled]
     */
    private const SCALE_CONFIG = [
        'listening' => ['max_raw' => 50, 'min_scaled' => 31, 'max_scaled' => 68],
        'structure' => ['max_raw' => 40, 'min_scaled' => 31, 'max_scaled' => 68],
        'reading'   => ['max_raw' => 50, 'min_scaled' => 31, 'max_scaled' => 67],
    ];

    /**
     * Convert raw correct answers count to TOEFL scaled score
     * using linear scaling: min_scaled + (correct/max_raw) * (max_scaled - min_scaled)
     *
     * Catatan: correct = 0 tetap menghasilkan skor floor (31), BUKAN 0,
     * sesuai standar skala resmi TOEFL ITP.
     */
    public function convertScore(int $correct, string $category): float
    {
        $category = strtolower(trim($category));

        if (!array_key_exists($category, self::SCALE_CONFIG)) {
            return self::SCALE_CONFIG['reading']['min_scaled']; // fallback floor aman
        }

        $config = self::SCALE_CONFIG[$category];
        $maxRaw = $config['max_raw'];
        $minScaled = $config['min_scaled'];
        $maxScaled = $config['max_scaled'];

        // Clamp correct agar tidak negatif atau melebihi max raw
        $correct = max(0, min($correct, $maxRaw));

        $scaled = $minScaled + ($correct / $maxRaw) * ($maxScaled - $minScaled);

        return round($scaled, 0);
    }

    /**
     * Calculate correct answers by category from user responses
     * Returns array like: ['listening' => 10, 'structure' => 9, 'reading' => 8]
     */
    public function calculateCorrectAnswers(iterable $questions, array $userAnswers): array
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