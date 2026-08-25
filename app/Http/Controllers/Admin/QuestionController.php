<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaketSoal;
use App\Models\Question;
use App\Repositories\QuestionRepositoryInterface;
use App\Http\Requests\StoreQuestionRequest;
use App\Http\Requests\UpdateQuestionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\QuestionExportService;
use App\Services\QuestionImportService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class QuestionController extends Controller
{
    private QuestionRepositoryInterface $questionRepo;

    public function __construct(
        QuestionRepositoryInterface $questionRepo,
        private QuestionExportService $questionExportService,
        private QuestionImportService $questionImportService,
    )
    {
        $this->questionRepo = $questionRepo;
    }

    public function index(Request $request, PaketSoal $paketSoal): View
    {
        $category = $this->validatedCategory($request);

        $filters = [
            'category' => $category,
            'paket_soal_id' => $paketSoal->id,
        ];

        $questions = $this->questionRepo->paginateFiltered($filters, 10);
        $questions->appends($request->only('category'));

        $stats = [
            'total_questions' => $this->questionRepo->totalCount($paketSoal->id),
            'listening_count' => $this->questionRepo->countByCategory('listening', $paketSoal->id),
            'structure_count' => $this->questionRepo->countByCategory('structure', $paketSoal->id),
            'reading_count' => $this->questionRepo->countByCategory('reading', $paketSoal->id),
        ];

        return view('admin.questions.index', compact('questions', 'category', 'stats', 'paketSoal'));
    }

    public function create(PaketSoal $paketSoal): View
    {
        return view('admin.questions.create', compact('paketSoal'));
    }

    public function store(StoreQuestionRequest $request, PaketSoal $paketSoal): RedirectResponse
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

        // paket_soal_id selalu ikut route, admin tidak memilihnya manual —
        // mencegah soal "nyasar" ke paket lain lewat form yang dimanipulasi.
        $data['paket_soal_id'] = $paketSoal->id;

        if ($request->hasFile('audio')) {
            $data['audio_path'] = $request->file('audio')->store('questions/audio', 'public');
        }

        $this->questionRepo->create($data);

        return redirect()
            ->route('paket-soal.questions.index', $paketSoal)
            ->with('success', 'Soal TOEFL Berhasil Ditambahkan!');
    }

    /**
     * Menampilkan detail soal.
     * Jika diakses via AJAX (dari modal "Lihat"), kembalikan HTML partial saja.
     * Jika diakses langsung via URL, tetap render halaman penuh (fallback aman).
     */
    public function show(Request $request, PaketSoal $paketSoal, Question $question)
    {
        $this->ensureQuestionBelongsToPaket($paketSoal, $question);

        return response()->json([
            'html' => view('admin.questions.partials.show-detail', compact('question'))->render(),
        ]);
    }

    /**
     * Menampilkan form edit.
     * Jika diakses via AJAX (dari modal "Edit"), kembalikan HTML form saja.
     * Jika diakses langsung via URL, tetap render halaman penuh (fallback aman).
     */
    public function edit(Request $request, PaketSoal $paketSoal, Question $question)
    {
        $this->ensureQuestionBelongsToPaket($paketSoal, $question);

        $rowNo = $request->query('row_no', $question->id);

        return response()->json([
            'html' => view('admin.questions.partials.edit-form', compact('question', 'rowNo', 'paketSoal'))->render(),
        ]);
    }

    /**
     * Update soal.
     * Jika request AJAX (dari modal), balas JSON berisi HTML baris tabel yang sudah diperbarui.
     * Jika request biasa, tetap redirect seperti semula.
     */
    public function update(UpdateQuestionRequest $request, PaketSoal $paketSoal, Question $question)
    {
        $this->ensureQuestionBelongsToPaket($paketSoal, $question);

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
                'paketSoal' => $paketSoal,
            ])->render(),
            'question_id' => $question->id,
        ]);
    }

    public function destroy(PaketSoal $paketSoal, Question $question): RedirectResponse
    {
        $this->ensureQuestionBelongsToPaket($paketSoal, $question);

        if ($question->audio_path) {
            Storage::disk('public')->delete($question->audio_path);
        }

        $this->questionRepo->delete($question->id);

        return redirect()->route('paket-soal.questions.index', $paketSoal);
    }

    public function exportXlsx(Request $request, PaketSoal $paketSoal): StreamedResponse
    {
        return $this->questionExportService->exportQuestionsXlsx($request, new class($this->questionRepo, $paketSoal->id) implements \App\Services\QuestionRepositoryInterfaceProxy {
            public function __construct(
                private QuestionRepositoryInterface $questionRepo,
                private int $paketSoalId,
            ) {
            }

            public function queryFiltered(array $filters)
            {
                $filters['paket_soal_id'] = $this->paketSoalId;

                return $this->questionRepo->queryFiltered($filters);
            }
        });
    }

    /**
     * Download template Excel kosong (140 baris pre-filled kategori +
     * contoh) untuk diisi admin sebelum import massal. Template ini sama
     * untuk semua paket, tidak spesifik ke satu paket tertentu.
     */
    public function importTemplate(): StreamedResponse
    {
        return $this->questionImportService->generateTemplate();
    }

    /**
     * Import massal soal dari .xlsx atau .zip ke dalam paket ini secara
     * spesifik. Lihat QuestionImportService::import() untuk detail validasi
     * (per baris + kapasitas kategori 50/40/50 per paket).
     */
    public function import(Request $request, PaketSoal $paketSoal): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,zip|max:51200',
        ], [
            'file.mimes' => 'File harus berformat .xlsx atau .zip.',
            'file.max' => 'Ukuran file maksimal 50MB.',
        ]);

        $result = $this->questionImportService->import($paketSoal, $request->file('file'));

        if (! $result['success']) {
            return back()
                ->with('import_errors', $result['errors'])
                ->with('import_error_type', $result['error_type']);
        }

        $message = "{$result['created']} soal berhasil diimport ke \"{$paketSoal->nama}\".";

        if ($result['audio_attached'] > 0) {
            $message .= " {$result['audio_attached']} file audio berhasil dilampirkan.";
        }

        if (! empty($result['audio_missing'])) {
            return redirect()->route('paket-soal.questions.index', $paketSoal)
                ->with('success', $message)
                ->with('import_warnings', $result['audio_missing']);
        }

        return redirect()->route('paket-soal.questions.index', $paketSoal)->with('success', $message);
    }

    /**
     * Guard: pastikan soal yang diakses benar-benar milik paket yang ada
     * di URL. Mencegah admin mengedit/menghapus soal paket lain hanya
     * dengan mengganti ID soal di request, sementara ID paket di URL
     * dibiarkan tetap.
     */
    private function ensureQuestionBelongsToPaket(PaketSoal $paketSoal, Question $question): void
    {
        if ($question->paket_soal_id !== $paketSoal->id) {
            abort(404);
        }
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