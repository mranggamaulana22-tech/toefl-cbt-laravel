<?php

namespace App\Services;

use App\Http\Requests\SubmitExamRequest;
use App\Models\ExamSetting;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\Result;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExamFlowService
{
    public function __construct(
        private QuestionSelectionService $questionSelectionService,
        private ScoringService $scoringService,
    ) {
    }

    public function prepareTest(int $userId): array
    {
        $setting = ExamSetting::current();

        return DB::transaction(function () use ($userId, $setting) {
            User::whereKey($userId)->lockForUpdate()->first();

            if (!$setting->is_open) {
                throw new \RuntimeException('Ujian belum dibuka oleh admin.');
            }

            if ($setting->current_cycle > 0 && Result::where('user_id', $userId)->where('exam_cycle', $setting->current_cycle)->whereNotNull('submitted_at')->exists()) {
                throw new \RuntimeException('Anda sudah mengerjakan ujian pada sesi aktif ini.');
            }

            $existingResult = Result::where('user_id', $userId)
                ->where('exam_cycle', $setting->current_cycle)
                ->lockForUpdate()
                ->first();

            if ($existingResult && $existingResult->user_id !== $userId) {
                throw new \RuntimeException('Akses ditolak.');
            }

            $examSession = ExamSession::where('user_id', $userId)
                ->where('exam_cycle', $setting->current_cycle)
                ->where('status', ExamSession::STATUS_IN_PROGRESS)
                ->lockForUpdate()
                ->first();

            if (!$examSession) {
                $questionIds = $this->questionSelectionService->generateOrderedExamQuestionIds();

                $examSession = ExamSession::create([
                    'user_id' => $userId,
                    'exam_settings_id' => $setting->id,
                    'exam_cycle' => $setting->current_cycle,
                    'question_ids' => $questionIds,
                    'current_question_index' => 0,
                    'answers' => [],
                    'status' => ExamSession::STATUS_IN_PROGRESS,
                    'started_at' => now(),
                ]);
            }

            if (!$existingResult) {
                Result::create([
                    'user_id' => $userId,
                    'exam_cycle' => $setting->current_cycle,
                    'started_at' => now(),
                ]);
            }

            return [
                'setting' => $setting,
                'examSession' => $examSession,
                'questionIds' => $examSession->question_ids,
            ];
        });
    }

    public function loadQuestionsByIds(array $questionIds): Collection
    {
        return $this->questionSelectionService->orderByIds(
            Question::whereIn('id', $questionIds)->get(),
            $questionIds,
        );
    }

    public function submitExam(int $userId, array $userAnswers): array
    {
        $setting = ExamSetting::current();

        return DB::transaction(function () use ($userId, $setting, $userAnswers) {
            User::whereKey($userId)->lockForUpdate()->first();

            if (!$setting->is_open) {
                throw new \RuntimeException('Sesi ujian sudah ditutup oleh admin.');
            }

            $examSession = ExamSession::where('user_id', $userId)
                ->where('exam_cycle', $setting->current_cycle)
                ->where('status', ExamSession::STATUS_IN_PROGRESS)
                ->lockForUpdate()
                ->first();

            if (!$examSession) {
                throw new \RuntimeException('Sesi ujian tidak ditemukan. Silakan mulai kembali dari halaman instruksi.');
            }

            $existingResult = Result::where('user_id', $userId)
                ->where('exam_cycle', $setting->current_cycle)
                ->lockForUpdate()
                ->first();

            if (!$existingResult) {
                throw new \RuntimeException('Silakan mulai ujian dari halaman instruksi terlebih dahulu.');
            }

            if ($existingResult->user_id !== $userId) {
                throw new \RuntimeException('Akses ditolak.');
            }

            $questionIds = $examSession->question_ids;

            if (!is_array($questionIds) || empty($questionIds)) {
                throw new \RuntimeException('Sesi ujian berakhir.');
            }

            $lockedResult = Result::lockForUpdate()
                ->where('id', $existingResult->id)
                ->first();

            if (!$lockedResult) {
                throw new \RuntimeException('Hasil ujian tidak ditemukan.');
            }

            if ($lockedResult->submitted_at) {
                throw new \RuntimeException('Ujian sudah disubmit sebelumnya.');
            }

            $questions = $this->questionSelectionService->orderByIds(
                Question::whereIn('id', $questionIds)->get(),
                $questionIds,
            );

            $correct = $this->scoringService->calculateCorrectAnswers($questions, $userAnswers);
            $scoreSummary = $this->scoringService->calculateScoreSummary($correct);

            $lockedResult->update([
                'correct_listening' => $correct['listening'],
                'correct_structure' => $correct['structure'],
                'correct_reading' => $correct['reading'],
                'score_total' => $scoreSummary['total_score'],
                'submitted_at' => now(),
            ]);

            $examSession->markSubmitted();

            return [
                'result' => $lockedResult,
                'cycle' => (int) $setting->current_cycle,
            ];
        });
    }
}
