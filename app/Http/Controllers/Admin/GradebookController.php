<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Result;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class GradebookController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $filters = $this->validatedFilters($request);

        $results = $this->applyDateFilters(Result::with('user'), $filters)
            ->latest()
            ->paginate(15);

        $results->appends($filters);

        if ($request->boolean('partial')) {
            return response()->json([
                'html' => view('admin.gradebook.partials.results', [
                    'results' => $results,
                ])->render(),
            ]);
        }

        return view('admin.gradebook.index', [
            'results' => $results,
            'filters' => $filters,
        ]);
    }

    public function exportCsv(Request $request): Response
    {
        $filters = $this->validatedFilters($request);

        $fileName = 'gradebook_toefl_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename=' . $fileName,
        ];

        $callback = function () use ($filters): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Tanggal',
                'Nama',
                'NPM',
                'Listening',
                'Structure',
                'Reading',
                'Total Skor',
            ]);

            $this->applyDateFilters(Result::query()->with('user'), $filters)
                ->orderBy('id')
                ->chunkById(500, function ($rows) use ($handle): void {
                    foreach ($rows as $result) {
                        fputcsv($handle, [
                            $result->created_at?->format('d-m-Y H:i:s'),
                            $result->user?->name,
                            $result->user?->npm,
                            $result->correct_listening,
                            $result->correct_structure,
                            $result->correct_reading,
                            $result->score_total,
                        ]);
                    }
                }, 'id');

            fclose($handle);
        };

        return response()->streamDownload($callback, $fileName, $headers);
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

    private function applyDateFilters(Builder $query, array $filters): Builder
    {
        if (!empty($filters['date_from'])) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }

        if (!empty($filters['date_to'])) {
            $query->where('created_at', '<=', Carbon::parse($filters['date_to'])->endOfDay());
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
