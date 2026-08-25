<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ExamControlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExamControlController extends Controller
{
    public function __construct(private ExamControlService $examControlService)
    {
    }

    public function startSession(Request $request): RedirectResponse
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'paket_soal_id' => ['required', 'integer', 'exists:paket_soals,id'],
        ], [
            'paket_soal_id.required' => 'Pilih paket soal terlebih dahulu sebelum membuka sesi ujian.',
            'paket_soal_id.exists' => 'Paket soal yang dipilih tidak valid.',
        ]);

        $result = $this->examControlService->startSession((int) $validated['paket_soal_id']);

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

    public function regenerateAccessCode(): RedirectResponse
    {
        if (auth()->user()?->role !== 'admin') {
            abort(403);
        }

        $result = $this->examControlService->regenerateAccessCode();

        return $result['ok']
            ? back()->with('success', $result['message'])
            : back()->with('error', $result['message']);
    }
}