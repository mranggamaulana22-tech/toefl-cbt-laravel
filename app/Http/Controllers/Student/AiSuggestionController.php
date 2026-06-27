<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PracticeResult;
use App\Models\Result;
use App\Services\AiSuggestionService;
use Illuminate\Http\Request;

class AiSuggestionController extends Controller
{
    public function __construct(private AiSuggestionService $aiSuggestionService)
    {
    }

    public function index()
    {
        // UPDATE: Path view disesuaikan dengan struktur folder baru
        return view('student.ai-analysis.index');
    }

    /**
     * Generate AI suggestion for exam result
     */
    public function generateForExam(Request $request, Result $result)
    {
        $this->authorize('view', $result);

        return $this->aiSuggestionService->generateForExam($request, $result);
    }

    /**
     * Generate AI suggestion for practice result
     */
    public function generateForPractice(Request $request, PracticeResult $practiceResult)
    {
        $this->authorize('view', $practiceResult);

        return $this->aiSuggestionService->generateForPractice($request, $practiceResult);
    }

    public function examStatus(Request $request, Result $result)
    {
        $this->authorize('view', $result);

        return response()->json($this->aiSuggestionService->buildExamStatusPayload($result));
    }

    public function practiceStatus(Request $request, PracticeResult $practiceResult)
    {
        $this->authorize('view', $practiceResult);

        return response()->json($this->aiSuggestionService->buildPracticeStatusPayload($practiceResult));
    }

    /**
     * Get dashboard suggestions summary
     */
    public function getDashboardSuggestions(Request $request)
    {
        return response()->json($this->aiSuggestionService->dashboardSuggestionsForUser((int) $request->user()->id));
    }
}