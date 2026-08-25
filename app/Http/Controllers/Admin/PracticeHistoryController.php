<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Jurusan;
use App\Http\Controllers\Controller;
use App\Models\PracticeResult;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PracticeHistoryController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $filters = $this->validatedFilters($request);

        $results = $this->applySorting(
            $this->applyFilters(PracticeResult::with('user')->whereNotNull('submitted_at'), $filters),
            $filters['sort']
        )->paginate(15);

        $results->appends($filters);

        if ($request->boolean('partial')) {
            return response()->json([
                'html' => view('admin.practice-history.partials.results', [
                    'results' => $results,
                ])->render(),
            ]);
        }

        return view('admin.practice-history.index', [
            'results' => $results,
            'filters' => $filters,
            'jurusanOptions' => Jurusan::labels(),
            'angkatanOptions' => $this->availableAngkatanOptions(),
        ]);
    }

    public function exportXlsx(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);

        $rows = $this->applySorting(
            $this->applyFilters(PracticeResult::query()->with('user')->whereNotNull('submitted_at'), $filters),
            $filters['sort']
        )->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Riwayat Latihan');

        $headers = ['Tanggal', 'Nama', 'NPM', 'Jurusan', 'Angkatan', 'Listening', 'Structure', 'Reading', 'Total Skor'];
        $sheet->fromArray($headers, null, 'A1');

        $headerRange = 'A1:' . chr(64 + count($headers)) . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNumber = 2;
        foreach ($rows as $result) {
            $sheet->fromArray([
                $result->submitted_at?->format('d-m-Y H:i:s'),
                $result->user?->name,
                $result->user?->npm,
                $result->user?->class,
                $result->user?->angkatan,
                $result->correct_listening,
                $result->correct_structure,
                $result->correct_reading,
                $result->score_total,
            ], null, 'A' . $rowNumber);
            $rowNumber++;
        }

        foreach (range('A', chr(64 + count($headers))) as $columnId) {
            $sheet->getColumnDimension($columnId)->setAutoSize(true);
        }

        $fileName = 'riwayat_latihan_toefl_' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
            'search' => ['nullable', 'string', 'max:100'],
            'class' => ['nullable', 'string', 'max:50'],
            'angkatan' => ['nullable', 'integer', 'digits:4'],
            'sort' => ['nullable', 'in:newest,score_desc,score_asc'],
        ]);

        return [
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'search' => trim($validated['search'] ?? ''),
            'class' => $validated['class'] ?? null,
            'angkatan' => $validated['angkatan'] ?? null,
            'sort' => $validated['sort'] ?? 'newest',
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

        if (!empty($filters['class'])) {
            $class = $filters['class'];

            $query->whereHas('user', function (Builder $userQuery) use ($class): void {
                $userQuery->where('class', $class);
            });
        }

        if (!empty($filters['angkatan'])) {
            $angkatan = $filters['angkatan'];

            $query->whereHas('user', function (Builder $userQuery) use ($angkatan): void {
                $userQuery->where('angkatan', $angkatan);
            });
        }

        return $query;
    }

    private function applySorting(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'score_desc' => $query->orderByDesc('score_total'),
            'score_asc' => $query->orderBy('score_total'),
            default => $query->latest('submitted_at'),
        };
    }

    private function availableAngkatanOptions(): array
    {
        $currentYear = (int) now()->year;

        return range($currentYear + 1, $currentYear - 5);
    }
}