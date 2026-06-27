<?php

namespace App\Services;

use Exception;

/**
 * Service for generating AI suggestions for exam results
 * Extends BaseOpenRouterService to eliminate duplication with PracticeReviewService
 */
class OpenRouterService extends BaseOpenRouterService
{
    protected function getSystemMessage(): string
    {
        return 'Anda adalah coach TOEFL untuk siswa pemula. Gunakan bahasa Indonesia yang sederhana, ringkas, personal, dan mudah dipahami orang awam. Dilarang mengubah angka skor pengguna.';
    }

    protected function getTemperature(): float
    {
        return 0.35;
    }

    /**
     * Generate AI suggestion based on exam results
     */
    public function generateSuggestion(array $resultData): ?string
    {
        try {
            $this->lastError = null;
            $this->lastUsedModel = null;

            $prompt = $this->buildPrompt($resultData);

            return $this->executeWithFallback(
                $prompt,
                fn($index) => $index === 0 ? 520 : 420,  // maxTokens: 520 for primary, 420 for fallback
                fn($content) => $content  // Return content as-is for AI suggestion
            );
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            \Log::error('OpenRouter API Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build prompt for AI suggestion
     */
    protected function buildPrompt(array $data): string
    {
        $promptVersion = (string) config('services.openrouter.prompt_version', 'v1');
        $templatePath = resource_path('prompts/openrouter/ai_analysis_' . $promptVersion . '.txt');

        if (!is_file($templatePath)) {
            $templatePath = resource_path('prompts/openrouter/ai_analysis_v1.txt');
        }

        $template = is_file($templatePath) ? file_get_contents($templatePath) : '';

        if ($template === false || trim($template) === '') {
            throw new Exception('OpenRouter prompt template tidak ditemukan.');
        }

        return strtr($template, $this->buildPromptReplacements($data));
    }

    protected function buildPromptReplacements(array $data): array
    {
        $listeningPercent = round(((float) ($data['correct_listening'] ?? 0) / 10) * 100);
        $structurePercent = round(((float) ($data['correct_structure'] ?? 0) / 10) * 100);
        $readingPercent = round(((float) ($data['correct_reading'] ?? 0) / 10) * 100);

        $sectionScores = [
            'Listening' => (float) ($data['correct_listening'] ?? 0),
            'Structure' => (float) ($data['correct_structure'] ?? 0),
            'Reading' => (float) ($data['correct_reading'] ?? 0),
        ];

        asort($sectionScores);
        $weakestSection = array_key_first($sectionScores);
        $strongestSection = array_key_last($sectionScores);

        $previousTestsCount = (int) ($data['previous_tests_count'] ?? 0);
        $deltaTotal = 0.0;
        $deltaListening = 0.0;
        $deltaStructure = 0.0;
        $deltaReading = 0.0;
        $previousSessionListening = (float) ($data['previous_session_listening'] ?? 0);
        $previousSessionStructure = (float) ($data['previous_session_structure'] ?? 0);
        $previousSessionReading = (float) ($data['previous_session_reading'] ?? 0);

        $listeningWrong = max(0, 10 - (int) ($data['correct_listening'] ?? 0));
        $structureWrong = max(0, 10 - (int) ($data['correct_structure'] ?? 0));
        $readingWrong = max(0, 10 - (int) ($data['correct_reading'] ?? 0));

        if ($previousTestsCount > 0) {
            $deltaTotal = round((float) $data['score_total'] - (float) $data['previous_avg_score_total'], 2);
            $deltaListening = round((float) $data['correct_listening'] - (float) $data['previous_avg_listening'], 2);
            $deltaStructure = round((float) $data['correct_structure'] - (float) $data['previous_avg_structure'], 2);
            $deltaReading = round((float) $data['correct_reading'] - (float) $data['previous_avg_reading'], 2);

            $trendContext = <<<TREND
Riwayat {$previousTestsCount} tes sebelumnya:
- Avg total: {$data['previous_avg_score_total']} | Delta total saat ini: {$deltaTotal}
- Delta Listening: {$deltaListening}
- Delta Structure: {$deltaStructure}
- Delta Reading: {$deltaReading}
TREND;
        } else {
            $trendContext = 'Riwayat sebelumnya belum cukup untuk hitung delta.';
        }

        $declineSections = [];
        if ($deltaListening < 0) {
            $declineSections[] = 'Listening';
        }
        if ($deltaStructure < 0) {
            $declineSections[] = 'Structure';
        }
        if ($deltaReading < 0) {
            $declineSections[] = 'Reading';
        }

        $declineContext = count($declineSections) > 0
            ? implode(', ', $declineSections)
            : 'Tidak ada section yang turun';

        return [
            '__SCORE_TOTAL__' => (string) $data['score_total'],
            '__LISTENING__' => (string) $data['correct_listening'],
            '__STRUCTURE__' => (string) $data['correct_structure'],
            '__READING__' => (string) $data['correct_reading'],
            '__LISTENING_PERCENT__' => (string) $listeningPercent,
            '__STRUCTURE_PERCENT__' => (string) $structurePercent,
            '__READING_PERCENT__' => (string) $readingPercent,
            '__LISTENING_WRONG__' => (string) $listeningWrong,
            '__STRUCTURE_WRONG__' => (string) $structureWrong,
            '__READING_WRONG__' => (string) $readingWrong,
            '__DURATION_MINUTES__' => (string) $data['duration_minutes'],
            '__WEAKEST_SECTION__' => $weakestSection,
            '__STRONGEST_SECTION__' => $strongestSection,
            '__DECLINE_CONTEXT__' => $declineContext,
            '__PREVIOUS_SESSION_LISTENING__' => (string) $previousSessionListening,
            '__PREVIOUS_SESSION_STRUCTURE__' => (string) $previousSessionStructure,
            '__PREVIOUS_SESSION_READING__' => (string) $previousSessionReading,
            '__TREND_CONTEXT__' => $trendContext,
        ];
    }
}
