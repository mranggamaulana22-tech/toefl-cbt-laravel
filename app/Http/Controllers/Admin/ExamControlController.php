<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExamControlService;
use Illuminate\Http\RedirectResponse;

class ExamControlController extends Controller
{
    public function __construct(private ExamControlService $examControlService)
    {
    }

    public function startSession(): RedirectResponse
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403);
        }

        $result = $this->examControlService->startSession();

        return $result['ok']
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }

    public function closeSession(): RedirectResponse
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403);
        }

        $result = $this->examControlService->closeSession();

        return $result['ok']
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }
}
