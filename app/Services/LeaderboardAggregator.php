<?php

namespace App\Services;

use App\Models\ExamSetting;
use App\Models\PracticeResult;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Aggregates leaderboard data for student rankings
 * Handles caching and pagination of practice result rankings
 */
class LeaderboardAggregator
{
    private const CACHE_TTL_MINUTES = 5;

    public function buildForDisplay(int $perPage = 20, string $sortBy = 'best_score'): array
    {
        $examSetting = ExamSetting::current();
        $cacheKey = $this->buildCacheKey($sortBy);

        $leaderboardData = Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($sortBy) {
            return $this->queryLeaderboard($sortBy);
        });

        return [
            'exam_setting' => $examSetting,
            'leaderboard' => $leaderboardData,
            'stats' => $this->buildStats(),
            'sort_by' => $sortBy,
        ];
    }

    public function buildPaginatedForDisplay(int $perPage = 20, string $sortBy = 'best_score'): array
    {
        $examSetting = ExamSetting::current();
        $cacheKey = $this->buildCacheKey($sortBy);

        $pagination = Cache::remember($cacheKey, now()->addMinutes(self::CACHE_TTL_MINUTES), function () use ($perPage, $sortBy) {
            return $this->queryLeaderboardPaginated($perPage, $sortBy);
        });

        return [
            'exam_setting' => $examSetting,
            'leaderboard' => $pagination,
            'stats' => $this->buildStats(),
            'sort_by' => $sortBy,
        ];
    }

    protected function queryLeaderboard(string $sortBy = 'best_score'): array
    {
        $query = PracticeResult::query()
            ->whereNotNull('submitted_at')
            ->select('user_id', DB::raw('MAX(score_total) as best_score'), DB::raw('COUNT(*) as attempt_count'))
            ->groupBy('user_id')
            ->with('user');

        if ($sortBy === 'attempts') {
            $query->orderByDesc('attempt_count')->orderByDesc('best_score');
        } else {
            $query->orderByDesc('best_score')->orderByDesc('attempt_count');
        }

        $items = $query->get();

        return $items->map(fn ($item) => [
            'rank' => null, // Will be set with index
            'user_id' => $item->user_id,
            'user_name' => $item->user?->name,
            'user_npm' => $item->user?->npm,
            'best_score' => $item->best_score,
            'attempt_count' => $item->attempt_count,
        ])->map(function ($item, $index) {
            $item['rank'] = $index + 1;
            return $item;
        })->toArray();
    }

    protected function queryLeaderboardPaginated(int $perPage, string $sortBy = 'best_score'): LengthAwarePaginator
    {
        $query = PracticeResult::query()
            ->whereNotNull('submitted_at')
            ->select('user_id', DB::raw('MAX(score_total) as best_score'), DB::raw('COUNT(*) as attempt_count'))
            ->groupBy('user_id')
            ->with('user');

        if ($sortBy === 'attempts') {
            $query->orderByDesc('attempt_count')->orderByDesc('best_score');
        } else {
            $query->orderByDesc('best_score')->orderByDesc('attempt_count');
        }

        return $query->paginate($perPage);
    }

    protected function buildStats(): array
    {
        $totalStudents = PracticeResult::distinct('user_id')->count('user_id');
        $totalAttempts = PracticeResult::whereNotNull('submitted_at')->count();
        $avgScore = (int) PracticeResult::whereNotNull('submitted_at')->avg('score_total');

        return [
            'total_students' => $totalStudents,
            'total_attempts' => $totalAttempts,
            'average_score' => $avgScore,
        ];
    }

    protected function buildCacheKey(string $sortBy = 'best_score'): string
    {
        return "leaderboard:{$sortBy}";
    }

    /**
     * Clear leaderboard cache (call when results are submitted)
     */
    public function clearCache(): void
    {
        Cache::forget($this->buildCacheKey('best_score'));
        Cache::forget($this->buildCacheKey('attempts'));
    }
}
