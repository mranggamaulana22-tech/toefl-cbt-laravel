<?php

namespace App\Services;

use App\Models\ExamSetting;
use App\Models\PaketSoal;
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
        $examReadiness = app(QuestionSelectionService::class)->getExamReadinessReport();

        // Daftar paket soal untuk dropdown "Paket Soal untuk Sesi Ini" di
        // dashboard. Sengaja dihitung ringan (bukan withCount seluruh
        // relasi) karena cuma untuk pengisian <select>, bukan halaman
        // detail paket.
        $pakets = PaketSoal::oldest()
            ->get()
            ->map(function (PaketSoal $paket) {
                return [
                    'model' => $paket,
                    'is_complete' => $paket->isComplete(),
                ];
            });

        return [
            'examSetting' => $examSetting,
            'examReadiness' => $examReadiness,
            'pakets' => $pakets,
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