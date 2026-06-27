<?php

namespace App\Services;

use App\Models\ExamSetting;
use App\Models\Question;
use App\Models\Result;
use App\Models\User;

class DashboardService
{
    public function __construct(
        private StudentStatsAggregator $statsAggregator,
        private LeaderboardAggregator $leaderboardAggregator,
    ) {
    }

    public function adminDashboardData(): array
    {
        $examSetting = ExamSetting::current();

        return [
            'examSetting' => $examSetting,
            'stats' => [
                'total_mahasiswa' => User::where('role', 'student')->count(),
                'total_soal' => Question::count(),
                'total_ujian' => Result::count(),
            ],
        ];
    }

    public function studentDashboardData(User $user): array
    {
        return $this->statsAggregator->buildForUser($user);
    }

    public function leaderboardData(int $perPage = 20, string $sortBy = 'best_score'): array
    {
        return $this->leaderboardAggregator->buildPaginatedForDisplay($perPage, $sortBy);
    }
}