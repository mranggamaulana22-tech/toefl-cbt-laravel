<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitPracticeRequest;
use App\Models\PracticeProgress;
use App\Models\PracticeQuestion;
use Illuminate\Http\RedirectResponse;
use App\Services\PracticeFlowService;
use Illuminate\View\View;

/**
 * Controller for practice flow (unlimited questions)
 * Handles: start practice, display practice test, submit practice answers
 * 
 * Related controllers:
 * - ExamController: handles full exam mode (50 questions)
 * - PracticeProgressController: handles progress API endpoints
 */
class PracticeController extends Controller
{
    public function __construct(
        private PracticeFlowService $practiceFlowService,
    ) {
    }

    /**
     * Display practice start page
     */
    public function start(): View
    {
        $questionCount = PracticeQuestion::count();

        return view('student.practice.start', compact('questionCount'));
    }

    /**
     * Display practice test page with questions
     */
    public function test(): View|RedirectResponse
    {
        if (!session()->has('practice_started_at')) {
            session(['practice_started_at' => now()->toDateTimeString()]);
        }

        $questionIds = session('practice_questions');

        if (!is_array($questionIds) || empty($questionIds)) {
            $questionIds = $this->practiceFlowService->generateOrGetQuestionIds();
            session(['practice_questions' => $questionIds]);
        }

        if (empty($questionIds)) {
            return redirect()->route('practice.start')->with('error', 'Soal latihan belum tersedia.');
        }

        // Load questions in correct order
        $questions = $this->practiceFlowService->loadQuestionsByIds($questionIds);

        if ($questions->isEmpty()) {
            session()->forget('practice_questions');

            return redirect()->route('practice.start')->with('error', 'Soal latihan belum tersedia.');
        }

        return view('student.practice.test', compact('questions'));
    }

    /**
     * Submit practice answers and calculate score
     */
    public function submit(SubmitPracticeRequest $request): View|RedirectResponse
    {
        $questionIds = session('practice_questions');

        if (!is_array($questionIds) || empty($questionIds)) {
            return redirect()->route('practice.start')->with('error', 'Sesi latihan berakhir. Silakan mulai lagi.');
        }

        // Load questions in correct order
        $questions = $this->practiceFlowService->loadQuestionsByIds($questionIds);

        if ($questions->isEmpty()) {
            session()->forget('practice_questions');

            return redirect()->route('practice.start')->with('error', 'Soal latihan tidak ditemukan.');
        }

        $userAnswers = $request->validated()['answers'];

        $submission = $this->practiceFlowService->submitPractice(
            (int) auth()->id(),
            $questions,
            $userAnswers,
            session('practice_started_at'),
        );

        $practiceResult = $submission['practiceResult'];
        $summary = $submission['summary'];

        // Clear progress records after successful submission
        PracticeProgress::where('user_id', auth()->id())->delete();

        // Clear session
        session()->forget(['practice_questions', 'practice_started_at']);

        return view('student.practice.result', [
            'result' => (object) $summary,
            'practiceResult' => $practiceResult,
        ]);
    }
}
