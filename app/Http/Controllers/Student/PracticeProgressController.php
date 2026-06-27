<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\PracticeProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for practice progress API endpoints
 * Handles: get progress, save progress, clear progress
 * 
 * Related controllers:
 * - ExamController: handles exam flow
 * - PracticeController: handles practice flow
 */
class PracticeProgressController extends Controller
{
    public function __construct(
        private PracticeProgressService $progressService,
    ) {
    }

    /**
     * Get saved practice progress for current user
     */
    public function get(Request $request): JsonResponse
    {
        $questionIds = session('practice_questions');

        if (!is_array($questionIds) || empty($questionIds)) {
            return response()->json([
                'progress' => null,
                'reason' => 'practice_session_not_found',
            ]);
        }

        $progress = $this->progressService->getProgress($request, $questionIds);

        if ($progress === null) {
            return response()->json(['progress' => null]);
        }

        return response()->json([
            'progress' => $progress,
        ]);
    }

    /**
     * Save practice progress for current user
     */
    public function save(Request $request): JsonResponse
    {
        $questionIds = session('practice_questions');

        if (!is_array($questionIds) || empty($questionIds)) {
            return response()->json([
                'message' => 'Sesi latihan tidak ditemukan.',
            ], 409);
        }

        $validated = $request->validate([
            'answers' => ['required', 'array'],
            'active_question' => ['required', 'integer', 'min:0'],
            'time_left' => ['required', 'integer', 'min:0', 'max:7200'],
            'question_ids' => ['required', 'array'],
            'question_ids.*' => ['integer'],
            'tab_violation_count' => ['required', 'integer', 'min:0', 'max:3'],
        ]);

        $success = $this->progressService->saveProgress($request, $validated, $questionIds);

        if (!$success) {
            return response()->json([
                'message' => 'Set soal tidak cocok dengan sesi latihan aktif.',
            ], 422);
        }

        return response()->json(['saved' => true]);
    }

    /**
     * Clear practice progress for current user
     */
    public function clear(Request $request): JsonResponse
    {
        $this->progressService->clearProgress($request);

        return response()->json(['cleared' => true]);
    }
}
