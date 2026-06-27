<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitExamRequest;
use App\Models\ExamSetting;
use App\Models\Result;
use App\Services\ExamFlowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Controller for exam flow (full 50-question exam)
 * Handles: start exam, display exam test, submit exam answers
 * 
 * Related controllers:
 * - PracticeController: handles practice mode (unlimited questions)
 * - PracticeProgressController: handles progress API endpoints
 */
class ExamController extends Controller
{
    public function __construct(
        private ExamFlowService $examFlowService,
    ) {
    }

    public function start(): View
    {
        $setting = ExamSetting::current();
        $userId = auth()->id();
        $hasAttemptedCurrentCycle = $setting->current_cycle > 0
            && Result::where('user_id', $userId)->where('exam_cycle', $setting->current_cycle)->whereNotNull('submitted_at')->exists();

        $canStart = $setting->is_open && !$hasAttemptedCurrentCycle;

        return view('student.exam.start', compact('canStart', 'setting', 'hasAttemptedCurrentCycle'));
    }

    /**
     * Display exam test page with 50 questions
     */
    public function test(): View|RedirectResponse
    {
        $userId = (int) auth()->id();

        try {
            $examData = $this->examFlowService->prepareTest($userId);
        } catch (\RuntimeException $exception) {
            return redirect()->route('exam.start')->with('error', $exception->getMessage());
        }

        $examSession = $examData['examSession'];
        $questionIds = $examData['questionIds'];

        $questions = $this->examFlowService->loadQuestionsByIds($questionIds);

        return view('student.exam.test', compact('questions', 'examSession'));
    }

    /**
     * Submit exam answers and calculate score
     */
    public function submit(SubmitExamRequest $request): View|RedirectResponse
    {
        $userId = (int) auth()->id();

        try {
            $submission = $this->examFlowService->submitExam($userId, $request->validated()['answers']);
        } catch (\RuntimeException $exception) {
            return redirect()->route('exam.start')->with('error', $exception->getMessage());
        }

        // Invalidate cached ranking for this user
        Cache::forget($this->rankCacheKey((int) $submission['cycle'], (int) $userId));

        return view('student.exam.result', ['result' => $submission['result']]);
    }

    /**
     * Get cache key for user ranking in specific cycle
     */
    private function rankCacheKey(int $cycle, int $userId): string
    {
        return "dashboard:rank:cycle:{$cycle}:user:{$userId}";
    }
}