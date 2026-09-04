<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PracticeQuestion;
use App\Models\PracticeProgress;
use App\Enums\QuestionCategory;
use App\Models\AppSetting;
use App\Services\AzureTtsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Services\QuestionExportService;
use App\Services\PracticeQuestionImportService;

class PracticeQuestionController extends Controller
{
    public function __construct(
        private QuestionExportService $questionExportService,
        private PracticeQuestionImportService $practiceQuestionImportService,
        private AzureTtsService $azureTtsService,
    )
    {
    }

    public function index(Request $request)
    {
        $category = $request->get('category');
        $query = PracticeQuestion::query();

        if ($category) {
            $query->where('category', $category);
        }

        $practiceQuestions = $query
            ->orderByRaw("CASE category WHEN 'listening' THEN 1 WHEN 'structure' THEN 2 WHEN 'reading' THEN 3 ELSE 4 END")
            ->orderBy('id')
            ->paginate(10);
        $categories = collect(['listening', 'structure', 'reading'])
            ->filter(fn ($value) => PracticeQuestion::where('category', $value)->exists())
            ->values();
        $stats = [
            'total_questions' => PracticeQuestion::count(),
            'listening_count' => PracticeQuestion::where('category', 'listening')->count(),
            'structure_count' => PracticeQuestion::where('category', 'structure')->count(),
            'reading_count' => PracticeQuestion::where('category', 'reading')->count(),
        ];

        $initialWoman = AppSetting::get('practice_voice_woman', config('tts_voices.default_woman'));
        $initialMan = AppSetting::get('practice_voice_man', config('tts_voices.default_man'));

        return view('admin.practice-questions.index', compact('practiceQuestions', 'categories', 'category', 'stats', 'initialWoman', 'initialMan'));
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
            'row_html' => view('admin.practice-questions.partials.row', [
                'question' => $practiceQuestion->fresh(),
                'no' => $request->input('row_no'),
            ])->render(),
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

    public function destroyAll(): RedirectResponse
    {
        $audioPaths = PracticeQuestion::withTrashed()
            ->whereNotNull('audio_path')
            ->pluck('audio_path')
            ->all();
        $deletedCount = PracticeQuestion::withTrashed()->count();

        DB::transaction(function () {
            PracticeProgress::query()->delete();
            PracticeQuestion::withTrashed()->forceDelete();
        });

        foreach ($audioPaths as $audioPath) {
            Storage::disk('public')->delete($audioPath);
        }

        return redirect()->route('admin.practice-questions.index')
            ->with('success', "{$deletedCount} soal latihan berhasil dihapus permanen. Histori nilai tetap tersimpan.");
    }

    public function exportXlsx(Request $request): StreamedResponse
    {
        return $this->questionExportService->exportPracticeQuestionsXlsx($request);
    }

    /**
     * Download template Excel kosong (header + contoh baris + dropdown
     * validasi) untuk diisi admin sebelum import massal.
     */
    public function importTemplate(): StreamedResponse
    {
        return $this->practiceQuestionImportService->generateTemplate();
    }

    /**
     * Import massal soal latihan dari file .xlsx atau .zip (xlsx + folder
     * audio/). Validasi bersifat all-or-nothing — lihat
     * PracticeQuestionImportService::import() untuk detail.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,zip|max:51200',
        ], [
            'file.mimes' => 'File harus berformat .xlsx atau .zip.',
            'file.max' => 'Ukuran file maksimal 50MB.',
        ]);

        $result = $this->practiceQuestionImportService->import($request->file('file'));

        if (! $result['success']) {
            return back()
                ->with('import_errors', $result['errors'])
                ->with('import_error_type', $result['error_type']);
        }

        $message = "{$result['created']} soal latihan berhasil diimport.";

        if ($result['audio_attached'] > 0) {
            $message .= " {$result['audio_attached']} file audio berhasil dilampirkan.";
        }

        if (! empty($result['audio_missing'])) {
            return redirect()->route('admin.practice-questions.index')
                ->with('success', $message)
                ->with('import_warnings', $result['audio_missing']);
        }

        return redirect()->route('admin.practice-questions.index')->with('success', $message);
    }

        /**
     * Ambil setting suara global (untuk Voice Picker modal).
     */
    public function getVoiceSettings()
    {
        return response()->json([
            'voice_woman' => AppSetting::get('practice_voice_woman', config('tts_voices.default_woman')),
            'voice_man' => AppSetting::get('practice_voice_man', config('tts_voices.default_man')),
            'voices' => config('tts_voices'),
        ]);
    }

    /**
     * Simpan pilihan suara global untuk Soal Latihan.
     */
    public function saveVoiceSettings(Request $request)
    {
        $validated = $request->validate([
            'voice_woman' => 'required|string',
            'voice_man' => 'required|string',
        ]);

        AppSetting::set('practice_voice_woman', $validated['voice_woman']);
        AppSetting::set('practice_voice_man', $validated['voice_man']);

        return response()->json(['success' => true]);
    }

    /**
     * Generate audio Azure TTS untuk SATU soal latihan berdasarkan
     * audio_transcript yang sudah diketik admin.
     */
    public function generateAudio(Request $request, PracticeQuestion $practiceQuestion)
    {
        if (blank($practiceQuestion->audio_transcript)) {
            return response()->json([
                'success' => false,
                'message' => 'Transkrip Audio masih kosong. Isi dulu sebelum generate.',
            ], 422);
        }

        $voiceWoman = AppSetting::get('practice_voice_woman', config('tts_voices.default_woman'));
        $voiceMan = AppSetting::get('practice_voice_man', config('tts_voices.default_man'));

        try {
            if ($practiceQuestion->audio_path) {
                Storage::disk('public')->delete($practiceQuestion->audio_path);
            }

            $path = $this->azureTtsService->generateFromTranscript(
                $practiceQuestion->audio_transcript,
                $voiceWoman,
                $voiceMan,
                'practice'
            );

            $practiceQuestion->update(['audio_path' => $path]);

            return response()->json([
                'success' => true,
                'audio_url' => '/storage/' . ltrim($path, '/'),
                'row_html' => view('admin.practice-questions.partials.row', [
                    'question' => $practiceQuestion->fresh(),
                    'no' => $request->input('row_no', $practiceQuestion->id),
                ])->render(),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate audio: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate audio untuk SEMUA soal listening yang belum punya audio
     * tapi transkripnya sudah diisi. Diproses paralel per-batch (10
     * sekaligus) via AzureTtsService::generateBatch().
     */
    public function generateAudioBatch(Request $request)
    {
        $voiceWoman = AppSetting::get('practice_voice_woman', config('tts_voices.default_woman'));
        $voiceMan = AppSetting::get('practice_voice_man', config('tts_voices.default_man'));

        $questions = PracticeQuestion::where('category', 'listening')
            ->whereNull('audio_path')
            ->whereNotNull('audio_transcript')
            ->where('audio_transcript', '!=', '')
            ->get(['id', 'audio_transcript']);

        if ($questions->isEmpty()) {
            return response()->json([
                'success' => true,
                'processed' => 0,
                'total' => 0,
                'failed' => [],
                'message' => 'Tidak ada soal yang perlu digenerate.',
            ]);
        }

        $items = $questions->map(fn ($q) => ['id' => $q->id, 'transcript' => $q->audio_transcript])->all();

        $result = $this->azureTtsService->generateBatch($items, $voiceWoman, $voiceMan, 'practice', batchSize: 10);

        foreach ($result['success'] as $id => $path) {
            PracticeQuestion::whereKey($id)->update(['audio_path' => $path]);
        }

        return response()->json([
            'success' => true,
            'processed' => count($result['success']),
            'total' => $questions->count(),
            'failed' => $result['failed'],
        ]);
    }

    /**
     * Preview 1 suara (untuk Voice Picker modal).
     */
    public function previewVoice(Request $request)
    {
        $validated = $request->validate(['voice_id' => 'required|string']);

        try {
            $path = $this->azureTtsService->previewVoice($validated['voice_id']);

            return response()->json([
                'success' => true,
                'audio_url' => '/storage/' . ltrim($path, '/'),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate preview: ' . $e->getMessage(),
            ], 500);
        }
    }
}