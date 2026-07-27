<?php

namespace App\Services;

use App\Models\ExamSetting;
use App\Models\PracticeResult;
use App\Models\Result;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Aggregates student statistics for dashboard display
 * Consolidates exam and practice performance data
 */
class StudentStatsAggregator
{
    private const RANK_CACHE_TTL_MINUTES = 5;

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

        // Combined into single query: best_score + total_attempts in one round-trip
        $examAgg = Result::where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->selectRaw('MAX(score_total) as best_score, COUNT(*) as total_attempts')
            ->first();

        $bestScore = (int) ($examAgg->best_score ?? 0);
        $totalAttempts = (int) ($examAgg->total_attempts ?? 0);

        // Combined into single query: best_score + attempt_count for practice
        $practiceAgg = PracticeResult::where('user_id', $user->id)
            ->whereNotNull('submitted_at')
            ->selectRaw('MAX(score_total) as best_score, COUNT(*) as total_attempts')
            ->first();

        $practiceBestScore = (int) ($practiceAgg->best_score ?? 0);
        $practiceAttempts = (int) ($practiceAgg->total_attempts ?? 0);

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

        $rankInCycle = null;
        if ($examSetting->current_cycle > 0) {
            $cycle = (int) $examSetting->current_cycle;
            $rankInCycle = Cache::remember(
                $this->examRankTableCacheKey($cycle),
                now()->addMinutes(self::RANK_CACHE_TTL_MINUTES),
                fn () => $this->buildExamRankTable($cycle)
            )->get((int) $user->id);
        }

        $practiceRankTable = Cache::remember(
            $this->practiceRankTableCacheKey(),
            now()->addMinutes(self::RANK_CACHE_TTL_MINUTES),
            fn () => $this->buildPracticeRankTable()
        );

        $practiceRank = $practiceRankTable->get((int) $user->id, '-');

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

    /**
     * Build a full user_id => rank map for the given exam cycle, cached as a whole.
     * This is computed ONCE per cache window regardless of how many students load
     * the dashboard, instead of running a full GROUP BY scan per user per request.
     *
     * @return \Illuminate\Support\Collection<int, int> user_id => rank
     */
    protected function buildExamRankTable(int $cycle): \Illuminate\Support\Collection
    {
        $userIds = Result::whereNotNull('submitted_at')
            ->where('exam_cycle', $cycle)
            ->select('user_id', DB::raw('MAX(score_total) as best_score'))
            ->groupBy('user_id')
            ->orderByDesc('best_score')
            ->pluck('user_id');

        return $userIds->mapWithKeys(fn ($userId, $index) => [$userId => $index + 1]);
    }

    /**
     * Build a full user_id => rank map for practice results, cached as a whole.
     * Replaces the old per-request full-table scan (previously run uncached
     * on every single dashboard load).
     *
     * @return \Illuminate\Support\Collection<int, int> user_id => rank
     */
    protected function buildPracticeRankTable(): \Illuminate\Support\Collection
    {
        $userIds = PracticeResult::whereNotNull('submitted_at')
            ->select('user_id', DB::raw('MAX(score_total) as top_score'))
            ->groupBy('user_id')
            ->orderByDesc('top_score')
            ->pluck('user_id');

        return $userIds->mapWithKeys(fn ($userId, $index) => [$userId => $index + 1]);
    }

    protected function examRankTableCacheKey(int $cycle): string
    {
        return "exam-rank-table:cycle:{$cycle}";
    }

    protected function practiceRankTableCacheKey(): string
    {
        return 'practice-rank-table';
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