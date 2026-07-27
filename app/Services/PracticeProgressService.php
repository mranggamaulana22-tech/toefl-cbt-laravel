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

        // If question set changed, invalidate saved progress.
        // Bandingkan sebagai SET (isi), bukan urutan — karena urutan soal bisa
        // berbeda antara session dan payload (mis. akibat shuffle di client),
        // padahal soalnya tetap sama.
        if (!$this->sameQuestionSet($savedQuestionIds, $normalizedCurrentIds)) {
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

        // Verify question IDs match current session (sebagai SET, bukan urutan persis).
        if (!$this->sameQuestionSet($savedQuestionIds, $normalizedCurrentIds)) {
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

    /**
     * Bandingkan dua daftar ID soal sebagai SET (isi sama), tanpa peduli urutan.
     *
     * PENTING: jangan pakai `$a !== $b` untuk membandingkan array di sini —
     * operator itu di PHP juga membandingkan URUTAN elemen, sehingga
     * [1,2,3] dianggap BEDA dari [3,2,1] walau isinya identik. Ini bug lama
     * yang bikin progress selalu invalid tiap kali urutan soal berbeda
     * antara session dan payload.
     */
    private function sameQuestionSet(array $a, array $b): bool
    {
        sort($a);
        sort($b);

        return $a === $b;
    }
}