<?php

namespace App\Services;

use Exception;
use App\Models\PracticeResultItem;
use App\Models\PracticeReviewUsage;
use Carbon\Carbon;

/**
 * Service for generating AI reviews of practice questions
 * Extends BaseOpenRouterService to eliminate duplication with OpenRouterService
 */
class PracticeReviewService extends BaseOpenRouterService
{
    protected function getSystemMessage(): string
    {
        return 'Anda adalah tutor TOEFL yang menjelaskan jawaban dengan bahasa Indonesia yang sederhana, singkat, dan akurat. Keluarkan JSON valid sesuai skema yang diminta.';
    }

    protected function getTemperature(): float
    {
        return 0.3;
    }

    /**
     * Generate review for a practice question
     */
    public function generateReview(array $reviewData): ?array
    {
        try {
            $this->lastError = null;
            $this->lastUsedModel = null;

            $prompt = $this->buildPrompt($reviewData);

            return $this->executeWithFallback(
                $prompt,
                fn($index) => $index === 0 ? 700 : 500,  // maxTokens: 700 for primary, 500 for fallback
                fn($content) => $this->normalizeResponse($content)  // Normalize and parse JSON response
            );
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return null;
        }
    }

    protected function buildPrompt(array $data): string
    {
        $templatePath = resource_path('prompts/openrouter/practice_review_v1.txt');
        $template = is_file($templatePath) ? file_get_contents($templatePath) : '';

        if ($template === false || trim($template) === '') {
            throw new Exception('Practice review prompt template tidak ditemukan.');
        }

        return strtr($template, [
            '__QUESTION_TEXT__' => (string) ($data['question_text'] ?? ''),
            '__OPTION_A__' => (string) ($data['option_a'] ?? ''),
            '__OPTION_B__' => (string) ($data['option_b'] ?? ''),
            '__OPTION_C__' => (string) ($data['option_c'] ?? ''),
            '__OPTION_D__' => (string) ($data['option_d'] ?? ''),
            '__CORRECT_ANSWER__' => (string) ($data['correct_answer'] ?? ''),
            '__USER_ANSWER__' => (string) ($data['user_answer'] ?? ''),
            '__CATEGORY__' => (string) ($data['category'] ?? ''),
            '__IS_CORRECT__' => !empty($data['is_correct']) ? 'true' : 'false',
        ]);
    }

    protected function normalizeResponse(?string $content): array
    {
        $text = trim((string) $content);

        if ($text === '') {
            return [
                'summary' => '',
                'mistake_reason' => '',
                'correct_concept' => '',
                'option_analysis' => '',
                'example' => '',
                'next_step' => '',
                'raw' => '',
            ];
        }

        $clean = trim((string) preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $text));
        $decoded = json_decode($clean, true);

        if (!is_array($decoded)) {
            $start = strpos($clean, '{');
            $end = strrpos($clean, '}');

            if ($start !== false && $end !== false && $end > $start) {
                $decoded = json_decode(substr($clean, $start, $end - $start + 1), true);
            }
        }

        if (!is_array($decoded)) {
            return [
                'summary' => $clean,
                'mistake_reason' => '',
                'correct_concept' => '',
                'option_analysis' => '',
                'example' => '',
                'next_step' => '',
                'raw' => $clean,
            ];
        }

        return [
            'summary' => trim((string) ($decoded['summary'] ?? '')),
            'mistake_reason' => trim((string) ($decoded['mistake_reason'] ?? '')),
            'correct_concept' => trim((string) ($decoded['correct_concept'] ?? '')),
            'option_analysis' => trim((string) ($decoded['option_analysis'] ?? '')),
            'example' => trim((string) ($decoded['example'] ?? '')),
            'next_step' => trim((string) ($decoded['next_step'] ?? '')),
            'raw' => $clean,
        ];
    }

    public function generatedToday(int $userId): int
    {
        return PracticeReviewUsage::where('user_id', $userId)
            ->where('generated', true)
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    public function remainingQuota(int $userId): int
    {
        $dailyQuota = (int) config('exam.review.daily_quota', 10);
        return max(0, $dailyQuota - $this->generatedToday($userId));
    }

    public function buildPromptData(PracticeResultItem $item): array
    {
        $audioTranscript = data_get($item->question_snapshot, 'audio_transcript')
            ?: $item->practiceQuestion?->audio_transcript;

        return [
            'question_text' => data_get($item->question_snapshot, 'question_text') ?: $item->question_text,
            'option_a' => data_get($item->question_snapshot, 'option_a') ?: $item->practiceQuestion?->option_a,
            'option_b' => data_get($item->question_snapshot, 'option_b') ?: $item->practiceQuestion?->option_b,
            'option_c' => data_get($item->question_snapshot, 'option_c') ?: $item->practiceQuestion?->option_c,
            'option_d' => data_get($item->question_snapshot, 'option_d') ?: $item->practiceQuestion?->option_d,
            'correct_answer' => data_get($item->question_snapshot, 'correct_answer') ?: $item->practiceQuestion?->correct_answer,
            'user_answer' => $item->user_answer,
            'category' => data_get($item->question_snapshot, 'category') ?: $item->practiceQuestion?->category ?: $item->category,
            'is_correct' => (bool) $item->is_correct,
            'audio_transcript' => $audioTranscript,
        ];
    }

    public function buildSnapshot(PracticeResultItem $item): array
    {
        $passage = data_get($item->question_snapshot, 'passage') ?: $item->practiceQuestion?->passage;
        $audioPath = data_get($item->question_snapshot, 'audio_path') ?: $item->practiceQuestion?->audio_path;
        $audioTranscript = data_get($item->question_snapshot, 'audio_transcript') ?: $item->practiceQuestion?->audio_transcript;

        return [
            'question_text' => data_get($item->question_snapshot, 'question_text') ?: $item->question_text,
            'passage' => $passage,
            'option_a' => data_get($item->question_snapshot, 'option_a') ?: $item->practiceQuestion?->option_a,
            'option_b' => data_get($item->question_snapshot, 'option_b') ?: $item->practiceQuestion?->option_b,
            'option_c' => data_get($item->question_snapshot, 'option_c') ?: $item->practiceQuestion?->option_c,
            'option_d' => data_get($item->question_snapshot, 'option_d') ?: $item->practiceQuestion?->option_d,
            'correct_answer' => data_get($item->question_snapshot, 'correct_answer') ?: $item->practiceQuestion?->correct_answer,
            'category' => data_get($item->question_snapshot, 'category') ?: $item->practiceQuestion?->category ?: $item->category,
            'audio_path' => $audioPath,
            'audio_transcript' => $audioTranscript,
        ];
    }

    public function buildResponsePayload(PracticeResultItem $item, array $review, bool $cached): array
    {
        return [
            'summary' => $review['summary'] ?? '',
            'mistake_reason' => $review['mistake_reason'] ?? '',
            'correct_concept' => $review['correct_concept'] ?? '',
            'option_analysis' => $review['option_analysis'] ?? '',
            'example' => $review['example'] ?? '',
            'next_step' => $review['next_step'] ?? '',
            'personal_note' => $this->personalNote($item),
            'cached' => $cached,
        ];
    }

    public function personalNote(PracticeResultItem $item): string
    {
        $userAnswer = $item->user_answer ?: '-';
        $correctAnswer = data_get($item->question_snapshot, 'correct_answer') ?: $item->practiceQuestion?->correct_answer ?: $item->correct_answer;

        if ($item->is_correct) {
            return 'Jawabanmu sudah benar. Review ini membantu menguatkan konsep supaya pola yang sama tetap konsisten di soal berikutnya.';
        }

        return 'Kamu memilih ' . $userAnswer . ', sedangkan kunci jawaban adalah ' . $correctAnswer . '. Fokuskan review pada perbedaan konsep dan jebakan opsi yang terlihat mirip.';
    }

    public function flattenReview(array $review): string
    {
        return trim(implode("\n\n", array_filter([
            $review['summary'] ?? '',
            $review['mistake_reason'] ?? '',
            $review['correct_concept'] ?? '',
            $review['option_analysis'] ?? '',
            $review['example'] ?? '',
            $review['next_step'] ?? '',
        ])));
    }
}
