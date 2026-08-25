<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\StartExamRequest;
use App\Http\Requests\SubmitExamRequest;
use App\Models\ExamSetting;
use App\Models\Result;
use App\Services\ExamControlService;
use App\Services\ExamFlowService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Controller for exam flow (full 140-question exam)
 * Handles: start exam, verify access code, display exam test, submit exam answers
 * 
 * Related controllers:
 * - PracticeController: handles practice mode (unlimited questions)
 * - PracticeProgressController: handles progress API endpoints
 */
class ExamController extends Controller
{
    public function __construct(
        private ExamFlowService $examFlowService,
        private ExamControlService $examControlService,
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
     * Verify access code before allowing entry to the exam test page.
     */
    public function enter(StartExamRequest $request): RedirectResponse
    {
        $setting = ExamSetting::current();

        if (! $this->examControlService->verifyAccessCode($request->validated()['access_code'])) {
            return redirect()->route('exam.start')->withErrors([
                'access_code' => 'Kode akses salah. Tanyakan kode ke pengawas ujian.',
            ]);
        }

        session(['exam_verified_cycle' => $setting->current_cycle]);

        return redirect()->route('exam.test');
    }

    /**
    * Display exam test page with 140 questions
     */
    public function test(): View|RedirectResponse
    {
        $setting = ExamSetting::current();

        if (session('exam_verified_cycle') !== $setting->current_cycle) {
            return redirect()->route('exam.start')->with('error', 'Masukkan kode akses ujian terlebih dahulu.');
        }

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

        // Clear access code verification for this cycle after finishing
        session()->forget('exam_verified_cycle');

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