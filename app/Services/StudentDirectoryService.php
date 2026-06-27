<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class StudentDirectoryService
{
    public function indexData(array $filters): array
    {
        $classFilter = $filters['class'] ?? null;
        $searchFilter = trim((string) ($filters['search'] ?? ''));

        $students = User::query()
            ->where('role', 'student')
            ->when($classFilter, function ($query) use ($classFilter) {
                $query->where('class', $classFilter);
            })
            ->when($searchFilter, function ($query) use ($searchFilter) {
                $query->where(function ($subQuery) use ($searchFilter) {
                    $subQuery->where('name', 'like', '%' . $searchFilter . '%')
                        ->orWhere('npm', 'like', '%' . $searchFilter . '%');
                });
            })
            ->withCount('results')
            ->latest()
            ->paginate(10);

        $students->appends([
            'class' => $classFilter,
            'search' => $searchFilter,
        ]);

        return [
            'students' => $students,
            'classes' => $this->availableClasses(),
            'filters' => [
                'class' => $classFilter,
                'search' => $searchFilter,
            ],
        ];
    }

    public function availableClasses(): Collection
    {
        return User::where('role', 'student')
            ->whereNotNull('class')
            ->where('class', '!=', '')
            ->distinct()
            ->orderBy('class')
            ->pluck('class');
    }

    public function deleteStudent(User $student): void
    {
        $student->delete();
    }
}