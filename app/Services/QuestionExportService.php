<?php

namespace App\Services;

use App\Enums\QuestionCategory;
use App\Models\PracticeQuestion;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

class QuestionExportService
{
    public function exportQuestions(Request $request, QuestionRepositoryInterfaceProxy $questionRepo): StreamedResponse
    {
        $category = $this->validatedCategory($request);
        $fileName = 'questions_' . ($category ?? 'all') . '_' . now()->format('Ymd_His') . '.csv';

        return $this->streamDownload($fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ], function ($handle) use ($category, $questionRepo): void {
            fputcsv($handle, [
                'Kategori',
                'Pertanyaan',
                'Jawaban A',
                'Jawaban B',
                'Jawaban C',
                'Jawaban D',
                'Jawaban Benar',
            ]);

            $questionRepo->queryFiltered(['category' => $category])
                ->orderBy('id')
                ->chunkById(500, function ($rows) use ($handle): void {
                    foreach ($rows as $question) {
                        fputcsv($handle, [
                            $question->category,
                            $question->question_text,
                            $question->option_a,
                            $question->option_b,
                            $question->option_c,
                            $question->option_d,
                            $question->correct_answer,
                        ]);
                    }
                }, 'id');
        });
    }

    /**
     * Export soal ke Excel (.xlsx) yang rapi (auto-size kolom, header bold).
     * Kalau ada soal listening dengan file audio, dibundel jadi .zip berisi
     * xlsx + folder audio/, supaya file MP3 ikut terbawa (link localhost
     * tidak portable ke komputer lain).
     */
    public function exportQuestionsXlsx(Request $request, QuestionRepositoryInterfaceProxy $questionRepo): StreamedResponse
    {
        $category = $this->validatedCategory($request);

        $questions = $questionRepo->queryFiltered(['category' => $category])
            ->orderBy('id')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Soal TOEFL');

        $headers = ['No', 'Kategori', 'Passage/Bacaan', 'Nama File Audio', 'Transkrip Audio', 'Pertanyaan', 'Pilihan A', 'Pilihan B', 'Pilihan C', 'Pilihan D', 'Jawaban Benar'];
        $sheet->fromArray($headers, null, 'A1');

        $headerRange = 'A1:' . chr(64 + count($headers)) . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNumber = 2;
        $audioFiles = []; // ['soal_5.mp3' => 'questions/audio/xxxxx.mp3']

        foreach ($questions as $index => $question) {
            $audioFileName = null;

            if ($question->audio_path && Storage::disk('public')->exists($question->audio_path)) {
                $extension = pathinfo($question->audio_path, PATHINFO_EXTENSION) ?: 'mp3';
                $audioFileName = 'soal_' . $question->id . '.' . $extension;
                $audioFiles[$audioFileName] = $question->audio_path;
            }

            $sheet->fromArray([
                $index + 1,
                ucfirst($question->category),
                $question->passage,
                $audioFileName ?? '-',
                $question->audio_transcript,
                $question->question_text,
                $question->option_a,
                $question->option_b,
                $question->option_c,
                $question->option_d,
                $question->correct_answer,
            ], null, 'A' . $rowNumber);
            $rowNumber++;
        }

        foreach (range('A', chr(64 + count($headers))) as $columnId) {
            $sheet->getColumnDimension($columnId)->setAutoSize(true);
        }
        foreach (['C', 'E', 'F'] as $columnId) {
            $sheet->getColumnDimension($columnId)->setAutoSize(false);
            $sheet->getColumnDimension($columnId)->setWidth(50);
        }
        $sheet->getStyle('C:F')->getAlignment()->setWrapText(true);

        $baseFileName = 'soal_toefl_' . ($category ?? 'all') . '_' . now()->format('Ymd_His');

        if (empty($audioFiles)) {
            return response()->streamDownload(function () use ($spreadsheet): void {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $baseFileName . '.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        $tempDir = storage_path('app/temp_export_' . uniqid());
        mkdir($tempDir, 0755, true);

        $xlsxPath = $tempDir . '/' . $baseFileName . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($xlsxPath);

        $zipPath = $tempDir . '/' . $baseFileName . '.zip';
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($xlsxPath, $baseFileName . '.xlsx');

        foreach ($audioFiles as $zipFileName => $storagePath) {
            $fullPath = Storage::disk('public')->path($storagePath);
            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, 'audio/' . $zipFileName);
            }
        }

        $zip->close();

        return response()->streamDownload(function () use ($zipPath, $tempDir, $xlsxPath): void {
            readfile($zipPath);
            @unlink($xlsxPath);
            @unlink($zipPath);
            @rmdir($tempDir);
        }, $baseFileName . '.zip', [
            'Content-Type' => 'application/zip',
        ]);
    }

    public function exportPracticeQuestions(Request $request): StreamedResponse
    {
        $category = $this->validatedCategory($request);
        $fileName = 'practice_questions_' . ($category ?? 'all') . '_' . now()->format('Ymd_His') . '.csv';

        return $this->streamDownload($fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ], function ($handle) use ($category): void {
            fputcsv($handle, [
                'Kategori',
                'Passage',
                'Pertanyaan',
                'Jawaban A',
                'Jawaban B',
                'Jawaban C',
                'Jawaban D',
                'Jawaban Benar',
                'Audio Path',
            ]);

            $query = PracticeQuestion::query();

            if ($category) {
                $query->where('category', $category);
            }

            $query->orderBy('id')->chunkById(500, function ($rows) use ($handle): void {
                foreach ($rows as $question) {
                    fputcsv($handle, [
                        $question->category,
                        $question->passage,
                        $question->question_text,
                        $question->option_a,
                        $question->option_b,
                        $question->option_c,
                        $question->option_d,
                        $question->correct_answer,
                        $question->audio_path,
                    ]);
                }
            }, 'id');
        });
    }

    /**
     * Export soal latihan ke Excel (.xlsx) yang rapi. Kalau ada soal listening
     * dengan file audio, dibundel jadi .zip berisi xlsx + folder audio/.
     */
    public function exportPracticeQuestionsXlsx(Request $request): StreamedResponse
    {
        $category = $this->validatedCategory($request);

        $query = PracticeQuestion::query();

        if ($category) {
            $query->where('category', $category);
        }

        $questions = $query->orderBy('id')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Soal Latihan');

        $headers = ['No', 'Kategori', 'Passage/Bacaan', 'Nama File Audio', 'Transkrip Audio', 'Pertanyaan', 'Pilihan A', 'Pilihan B', 'Pilihan C', 'Pilihan D', 'Jawaban Benar'];
        $sheet->fromArray($headers, null, 'A1');

        $headerRange = 'A1:' . chr(64 + count($headers)) . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $rowNumber = 2;
        $audioFiles = [];

        foreach ($questions as $index => $question) {
            $audioFileName = null;

            if ($question->audio_path && Storage::disk('public')->exists($question->audio_path)) {
                $extension = pathinfo($question->audio_path, PATHINFO_EXTENSION) ?: 'mp3';
                $audioFileName = 'soal_latihan_' . $question->id . '.' . $extension;
                $audioFiles[$audioFileName] = $question->audio_path;
            }

            $sheet->fromArray([
                $index + 1,
                ucfirst($question->category),
                $question->passage,
                $audioFileName ?? '-',
                $question->audio_transcript,
                $question->question_text,
                $question->option_a,
                $question->option_b,
                $question->option_c,
                $question->option_d,
                $question->correct_answer,
            ], null, 'A' . $rowNumber);
            $rowNumber++;
        }

        foreach (range('A', chr(64 + count($headers))) as $columnId) {
            $sheet->getColumnDimension($columnId)->setAutoSize(true);
        }
        foreach (['C', 'E', 'F'] as $columnId) {
            $sheet->getColumnDimension($columnId)->setAutoSize(false);
            $sheet->getColumnDimension($columnId)->setWidth(50);
        }
        $sheet->getStyle('C:F')->getAlignment()->setWrapText(true);

        $baseFileName = 'soal_latihan_toefl_' . ($category ?? 'all') . '_' . now()->format('Ymd_His');

        if (empty($audioFiles)) {
            return response()->streamDownload(function () use ($spreadsheet): void {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            }, $baseFileName . '.xlsx', [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ]);
        }

        $tempDir = storage_path('app/temp_export_' . uniqid());
        mkdir($tempDir, 0755, true);

        $xlsxPath = $tempDir . '/' . $baseFileName . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($xlsxPath);

        $zipPath = $tempDir . '/' . $baseFileName . '.zip';
        $zip = new ZipArchive();
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($xlsxPath, $baseFileName . '.xlsx');

        foreach ($audioFiles as $zipFileName => $storagePath) {
            $fullPath = Storage::disk('public')->path($storagePath);
            if (file_exists($fullPath)) {
                $zip->addFile($fullPath, 'audio/' . $zipFileName);
            }
        }

        $zip->close();

        return response()->streamDownload(function () use ($zipPath, $tempDir, $xlsxPath): void {
            readfile($zipPath);
            @unlink($xlsxPath);
            @unlink($zipPath);
            @rmdir($tempDir);
        }, $baseFileName . '.zip', [
            'Content-Type' => 'application/zip',
        ]);
    }

    protected function validatedCategory(Request $request): ?string
    {
        $categories = implode(',', array_map(fn ($case) => $case->value, QuestionCategory::cases()));
        $validated = $request->validate([
            'category' => ["nullable", "in:{$categories}"],
        ]);

        return $validated['category'] ?? null;
    }

    protected function streamDownload(string $fileName, array $headers, callable $callback): StreamedResponse
    {
        $headers = array_merge($headers, [
            'Content-Disposition' => 'attachment; filename=' . $fileName,
        ]);

        return response()->streamDownload(function () use ($callback): void {
            $handle = fopen('php://output', 'w');

            if ($handle === false) {
                return;
            }

            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $callback($handle);

            fclose($handle);
        }, $fileName, $headers);
    }
}

interface QuestionRepositoryInterfaceProxy
{
    public function queryFiltered(array $filters);
}