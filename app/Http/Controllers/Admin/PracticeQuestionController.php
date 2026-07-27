<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PracticeQuestion;
use App\Enums\QuestionCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\QuestionExportService;

class PracticeQuestionController extends Controller
{
    public function __construct(private QuestionExportService $questionExportService)
    {
    }

    public function index(Request $request)
    {
        $category = $request->get('category');
        $query = PracticeQuestion::query();

        if ($category) {
            $query->where('category', $category);
        }

        $practiceQuestions = $query->paginate(10);
        $categories = PracticeQuestion::distinct('category')->pluck('category')->sort();
        $stats = [
            'total_questions' => PracticeQuestion::count(),
            'listening_count' => PracticeQuestion::where('category', 'listening')->count(),
            'structure_count' => PracticeQuestion::where('category', 'structure')->count(),
            'reading_count' => PracticeQuestion::where('category', 'reading')->count(),
        ];

        return view('admin.practice-questions.index', compact('practiceQuestions', 'categories', 'category', 'stats'));
    }

    public function create()
    {
        return view('admin.practice-questions.create');
    }

    public function store(Request $request)
    {
        $categories = implode(',', array_map(fn($case) => $case->value, QuestionCategory::cases()));
        $validated = $request->validate([
            'category' => "required|in:{$categories}",
            'passage' => 'nullable|string',
            'audio_path' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
            'audio_transcript' => 'nullable|string|required_with:audio_path',
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        if ($request->hasFile('audio_path')) {
            $validated['audio_path'] = $request->file('audio_path')->store('practice', 'public');
        }

        PracticeQuestion::create($validated);

        return redirect()->route('admin.practice-questions.index')
            ->with('success', 'Soal latihan berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail soal.
     * Jika diakses via AJAX (dari modal "Lihat"), kembalikan HTML partial saja.
     * Jika diakses langsung via URL, tetap render halaman penuh seperti biasa.
     */
    public function show(Request $request, PracticeQuestion $practiceQuestion)
    {
        return response()->json([
            'html' => view('admin.practice-questions.partials.show-detail', compact('practiceQuestion'))->render(),
        ]);
    }

    /**
     * Menampilkan form edit.
     * Jika diakses via AJAX (dari modal "Edit"), kembalikan HTML form saja.
     * Jika diakses langsung via URL, tetap render halaman penuh seperti biasa (fallback aman).
     */
    public function edit(Request $request, PracticeQuestion $practiceQuestion)
    {
        return response()->json([
            'html' => view('admin.practice-questions.partials.edit-form', compact('practiceQuestion'))->render(),
        ]);
    }

    /**
     * Update soal.
     * Jika request AJAX (dari modal), balas JSON berisi HTML baris tabel yang sudah diperbarui,
     * supaya front-end bisa ganti baris itu saja tanpa reload halaman.
     * Jika request biasa (form penuh diakses via URL), tetap redirect seperti semula.
     */
    public function update(Request $request, PracticeQuestion $practiceQuestion)
    {
        $hasAudio = $request->hasFile('audio_path') || !empty($practiceQuestion->audio_path);
        $categories = implode(',', array_map(fn($case) => $case->value, QuestionCategory::cases()));

        $rules = [
            'category' => "required|in:{$categories}",
            'passage' => 'nullable|string',
            'audio_path' => 'nullable|file|mimes:mp3,wav,ogg|max:20480',
            'audio_transcript' => ['nullable', 'string'],
            'question_text' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ];

        if ($hasAudio) {
            $rules['audio_transcript'][] = 'required';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('audio_path')) {
            if ($practiceQuestion->audio_path) {
                Storage::disk('public')->delete($practiceQuestion->audio_path);
            }
            $validated['audio_path'] = $request->file('audio_path')->store('practice', 'public');
        }

        $practiceQuestion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Soal latihan berhasil diperbarui.',
            'row_html' => view('admin.practice-questions.partials.row', ['question' => $practiceQuestion->fresh()])->render(),
            'question_id' => $practiceQuestion->id,
        ]);
    }

    public function destroy(PracticeQuestion $practiceQuestion)
    {
        if ($practiceQuestion->audio_path) {
            Storage::disk('public')->delete($practiceQuestion->audio_path);
        }

        $practiceQuestion->delete();

        return redirect()->route('admin.practice-questions.index')
            ->with('success', 'Soal latihan berhasil dihapus.');
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        return $this->questionExportService->exportPracticeQuestions($request);
    }
}