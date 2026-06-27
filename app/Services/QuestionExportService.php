<?php

namespace App\Services;

use App\Enums\QuestionCategory;
use App\Models\PracticeQuestion;
use App\Models\Question;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

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