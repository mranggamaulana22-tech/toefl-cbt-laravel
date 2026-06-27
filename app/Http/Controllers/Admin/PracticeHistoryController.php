<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PracticeResult;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PracticeHistoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->validatedFilters($request);

        $results = $this->applyFilters(PracticeResult::with('user')->whereNotNull('submitted_at'), $filters)
            ->latest('submitted_at')
            ->paginate(15);

        $results->appends($filters);

        return view('admin.practice-history.index', [
            'results' => $results,
            'filters' => $filters,
        ]);
    }

    private function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        return [
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'search' => trim($validated['search'] ?? ''),
        ];
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['date_from'])) {
            $query->where('submitted_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if (!empty($filters['date_to'])) {
            $query->where('submitted_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];

            $query->whereHas('user', function (Builder $userQuery) use ($search): void {
                $userQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('npm', 'like', '%' . $search . '%');
            });
        }

        return $query;
    }
}
