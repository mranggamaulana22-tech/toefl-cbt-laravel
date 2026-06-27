<?php

namespace App\Services;

use App\Models\PracticeResult;
use App\Models\Result;
use Illuminate\Database\Eloquent\Builder;

class StudentResultService
{
    public function dashboardData(int $userId): array
    {
        return [
            'results' => Result::forUser($userId)
                ->submitted()
                ->latest('submitted_at')
                ->limit(5)
                ->get(),
            'practiceResults' => PracticeResult::forUser($userId)
                ->submitted()
                ->latest('submitted_at')
                ->limit(5)
                ->get(),
            'summary' => $this->summary(Result::class, $userId),
            'practiceSummary' => $this->summary(PracticeResult::class, $userId),
        ];
    }

    public function examHistoryData(int $userId): array
    {
        return [
            'results' => Result::forUser($userId)
                ->submitted()
                ->latest('submitted_at')
                ->paginate(10),
            'summary' => $this->summary(Result::class, $userId),
        ];
    }

    public function practiceHistoryData(int $userId): array
    {
        return [
            'practiceResults' => PracticeResult::forUser($userId)
                ->submitted()
                ->latest('submitted_at')
                ->paginate(10),
            'practiceSummary' => $this->summary(PracticeResult::class, $userId),
        ];
    }

    public function summary(string $modelClass, int $userId): array
    {
        $baseQuery = $modelClass::forUser($userId)->submitted();

        return [
            'total_attempts' => (clone $baseQuery)->count(),
            'latest_score' => (clone $baseQuery)->latest('submitted_at')->value('score_total'),
            'best_score' => (clone $baseQuery)->max('score_total'),
        ];
    }
}