<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PracticeResult;
use App\Models\PracticeResultItem;
use App\Services\PracticeReviewFlowService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PracticeReviewController extends Controller
{
    public function __construct(private PracticeReviewFlowService $practiceReviewFlowService)
    {
    }

    public function index(Request $request): View
    {
        $sessions = $this->practiceReviewFlowService->sessionsForUser((int) $request->user()->id);

        return view('student.review.index', [
            'sessions' => $sessions,
        ]);
    }

    public function show(Request $request, PracticeResult $practiceResult): View
    {
        $this->authorize('view', $practiceResult);

        return view('student.review.show', $this->practiceReviewFlowService->showPayload((int) $request->user()->id, $practiceResult));
    }

    public function reviewItem(Request $request, PracticeResult $practiceResult, PracticeResultItem $item): JsonResponse
    {
        $this->authorize('view', $practiceResult);

        if ((int) $item->practice_result_id !== (int) $practiceResult->id) {
            abort(404);
        }

        $response = $this->practiceReviewFlowService->reviewItemForUser((int) $request->user()->id, $item);

        return response()->json($response['body'], $response['status']);
    }
}
