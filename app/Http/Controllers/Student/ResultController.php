<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PracticeResult;
use App\Models\Result;
use App\Services\StudentResultService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function __construct(private StudentResultService $resultService)
    {
    }

    public function index(): View
    {
        return view('student.results.index', $this->resultService->dashboardData((int) auth()->id()));
    }

    public function examHistory(): View
    {
        return view('student.results.exam-history', $this->resultService->examHistoryData((int) auth()->id()));
    }

    public function practiceHistory(): View
    {
        return view('student.results.practice-history', $this->resultService->practiceHistoryData((int) auth()->id()));
    }

    public function certificate(Result $result): View
    {
        if ($result->user_id !== auth()->id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return view('student.results.certificate', compact('result'));
    }
}
