<?php

namespace App\Services;

use App\Models\ExamSetting;
use App\Models\PracticeResult;
use App\Models\Result;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Aggregates student statistics for dashboard display
 * Consolidates exam and practice performance data
 */
class StudentStatsAggregator
{
    public function buildForUser(User $user): array
    {
        $examSetting = ExamSetting::current();

        $latestResult = Result::where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->first();

        $latestPracticeResult = PracticeResult::where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->latest('submitted_at')
            ->first();

        $bestScore = (int) Result::where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->max('score_total');

        $totalAttempts = Result::where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->count();

        $practiceAttempts = PracticeResult::where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->count();

        $hasAttemptedCurrentCycle = $examSetting->current_cycle > 0
            && Result::where('user_id', $user->id)
                ->where('exam_cycle', $examSetting->current_cycle)
                ->whereNotNull('submitted_at')
                ->exists();

        $canStart = $examSetting->is_open && !$hasAttemptedCurrentCycle;

        $completionPercent = 0;
        if ($latestResult) {
            $answered = (int) $latestResult->correct_listening + (int) $latestResult->correct_structure + (int) $latestResult->correct_reading;
            $completionPercent = (int) min(100, round(($answered / 50) * 100));
        }

        $practiceProgressPercent = 0;
        if ($latestPracticeResult) {
            $practiceProgressPercent = (int) min(100, round(((int) $latestPracticeResult->score_total / 120) * 100));
        }

        $practiceBestScore = (int) PracticeResult::where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->max('score_total');

        $rankInCycle = null;
        if ($examSetting->current_cycle > 0) {
            $cycle = (int) $examSetting->current_cycle;
            $cacheKey = $this->rankCacheKey($cycle, (int) $user->id);

            $rankInCycle = Cache::remember($cacheKey, now()->addMinutes(3), function () use ($cycle, $user) {
                return $this->calculateRankInCycle($cycle, (int) $user->id);
            });
        }

        $practiceRankings = PracticeResult::whereNotNull('submitted_at')
            ->select('user_id', \DB::raw('MAX(score_total) as top_score'))
            ->groupBy('user_id')
            ->orderBy('top_score', 'desc')
            ->get();

        $rankIndex = $practiceRankings->pluck('user_id')->search($user->id);
        $practiceRank = ($rankIndex !== false) ? $rankIndex + 1 : '-';

        $motivation = $this->getDailyMotivation();

        return [
            'examSetting' => $examSetting,
            'studentStats' => [
                'latest_score' => $latestResult?->score_total,
                'best_score' => $bestScore,
                'total_attempts' => $totalAttempts,
                'completion_percent' => $completionPercent,
                'exam_cycle' => (int) $examSetting->current_cycle,
                'is_exam_open' => (bool) $examSetting->is_open,
                'can_start' => $canStart,
                'has_attempted_current_cycle' => $hasAttemptedCurrentCycle,
                'rank_in_cycle' => $rankInCycle,
                'practice_rank' => $practiceRank,
                'motivation' => $motivation,
                'last_taken_at' => $latestResult?->submitted_at,
                'practice_latest_score' => $latestPracticeResult?->score_total,
                'practice_best_score' => $practiceBestScore,
                'practice_attempts' => $practiceAttempts,
                'practice_progress_percent' => $practiceProgressPercent,
                'practice_last_taken_at' => $latestPracticeResult?->submitted_at,
            ],
        ];
    }

    protected function rankCacheKey(int $cycle, int $userId): string
    {
        return "exam-rank:cycle:{$cycle}:user:{$userId}";
    }

    protected function calculateRankInCycle(int $cycle, int $userId): ?int
    {
        $rank = Result::whereNotNull('submitted_at')
            ->where('exam_cycle', $cycle)
            ->select('user_id', \DB::raw('MAX(score_total) as best_score'))
            ->groupBy('user_id')
            ->orderByDesc('best_score')
            ->pluck('user_id')
            ->search($userId);

        return ($rank !== false) ? $rank + 1 : null;
    }

    protected function getDailyMotivation(): string
    {
        $dailyMotivations = [
            'Konsistensi 1 jam latihan hari ini lebih kuat dari 5 jam dadakan besok.',
            'Fokus pada satu section dulu, lalu menangkan ritme ujianmu.',
            'Kecepatan membaca meningkat saat kamu berani latihan dengan timer.',
            'Skor besar datang dari disiplin kecil yang diulang setiap hari.',
            'Listening bagus dimulai dari konsentrasi, bukan sekadar hafalan.',
            'Tiap latihan adalah simulasi kemenangan di hari ujian.',
            'Progress kecil hari ini adalah skor tinggi di percobaan berikutnya.',
        ];

        return $dailyMotivations[Carbon::now()->dayOfYear % count($dailyMotivations)];
    }
}
