<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Repositories\QuestionRepositoryInterface;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\QuestionExportService;

class QuestionController extends Controller
{
    private QuestionRepositoryInterface $questionRepo;

    public function __construct(
        QuestionRepositoryInterface $questionRepo,
        private QuestionExportService $questionExportService,
    )
    {
        $this->questionRepo = $questionRepo;
    }

    public function index(Request $request): View
    {
        $category = $this->validatedCategory($request);

        $filters = ['category' => $category];

        $questions = $this->questionRepo->paginateFiltered($filters, 10);
        $questions->appends($request->only('category'));

        $stats = [
            'total_questions' => $this->questionRepo->totalCount(),
            'listening_count' => $this->questionRepo->countByCategory('listening'),
            'structure_count' => $this->questionRepo->countByCategory('structure'),
            'reading_count' => $this->questionRepo->countByCategory('reading'),
        ];

        return view('admin.questions.index', compact('questions', 'category', 'stats'));
    }

    public function create(): View
    {
        return view('admin.questions.create');
    }

    public function store(StoreQuestionRequest $request): RedirectResponse
    {
        $data = $request->only([
            'category',
            'passage',
            'audio_transcript',
            'question_text',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'correct_answer',
        ]);

        if ($request->hasFile('audio')) {
            $data['audio_path'] = $request->file('audio')->store('questions/audio', 'public');
        }

        $this->questionRepo->create($data);

        return redirect()->route('questions.index')->with('success', 'Soal TOEFL Berhasil Ditambahkan!');
    }

    /**
     * Menampilkan detail soal.
     * Jika diakses via AJAX (dari modal "Lihat"), kembalikan HTML partial saja.
     * Jika diakses langsung via URL, tetap render halaman penuh (fallback aman).
     */
    public function show(Request $request, Question $question)
    {
        return response()->json([
            'html' => view('admin.questions.partials.show-detail', compact('question'))->render(),
        ]);
    }

    /**
     * Menampilkan form edit.
     * Jika diakses via AJAX (dari modal "Edit"), kembalikan HTML form saja.
     * Jika diakses langsung via URL, tetap render halaman penuh (fallback aman).
     */
    public function edit(Request $request, Question $question)
    {
        $rowNo = $request->query('row_no', $question->id);

        return response()->json([
            'html' => view('admin.questions.partials.edit-form', compact('question', 'rowNo'))->render(),
        ]);
    }

    /**
     * Update soal.
     * Jika request AJAX (dari modal), balas JSON berisi HTML baris tabel yang sudah diperbarui.
     * Jika request biasa, tetap redirect seperti semula.
     */
    public function update(UpdateQuestionRequest $request, Question $question)
    {
        $data = $request->only([
            'category',
            'passage',
            'audio_transcript',
            'question_text',
            'option_a',
            'option_b',
            'option_c',
            'option_d',
            'correct_answer',
        ]);

        if ($request->hasFile('audio')) {
            if ($question->audio_path) {
                Storage::disk('public')->delete($question->audio_path);
            }

            $data['audio_path'] = $request->file('audio')->store('questions/audio', 'public');
        }

        $this->questionRepo->update($question->id, $data);

        return response()->json([
            'success' => true,
            'message' => 'Soal TOEFL berhasil diperbarui.',
            'row_html' => view('admin.questions.partials.row', [
                'q' => $question->fresh(),
                'no' => $request->input('_row_no'),
            ])->render(),
            'question_id' => $question->id,
        ]);
    }

    public function destroy(Question $question): RedirectResponse
    {
        if ($question->audio_path) {
            Storage::disk('public')->delete($question->audio_path);
        }

        $this->questionRepo->delete($question->id);

        return redirect()->route('questions.index');
    }

    public function exportCsv(Request $request)
    {
        return $this->questionExportService->exportQuestions($request, new class($this->questionRepo) implements \App\Services\QuestionRepositoryInterfaceProxy {
            public function __construct(private QuestionRepositoryInterface $questionRepo)
            {
            }

            public function queryFiltered(array $filters)
            {
                return $this->questionRepo->queryFiltered($filters);
            }
        });
    }

    /**
     * Validate and extract category from request.
     * Returns only if category is one of the valid TOEFL categories.
     */
    private function validatedCategory(Request $request): ?string
    {
        $category = $request->query('category');
        $validCategories = ['listening', 'structure', 'reading'];

        return in_array($category, $validCategories, true) ? $category : null;
    }
}