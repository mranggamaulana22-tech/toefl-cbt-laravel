<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\PracticeResult;
use App\Models\Result;
use App\Services\CertificateService;
use App\Services\StudentResultService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function __construct(
        private StudentResultService $resultService,
        private CertificateService $certificateService,
    ) {
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

    /**
     * Preview sertifikat (halaman HTML, bisa di-print manual via browser).
     */
    public function certificate(Result $result): View
    {
        if ($result->user_id !== auth()->id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return view('student.results.certificate', compact('result'));
    }

    /**
     * Download sertifikat sebagai file PDF asli.
     * PDF sudah di-generate otomatis saat submit ujian; kalau karena suatu
     * sebab belum ada (misal generate sempat gagal), di-generate ulang di sini.
     */
    public function downloadCertificate(Result $result)
    {
        if ($result->user_id !== auth()->id()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $path = $this->certificateService->ensureGenerated($result);

        $fileName = 'Sertifikat_TOEFL_' . str_replace(' ', '_', $result->user->name) . '.pdf';

        return Storage::disk('public')->download($path, $fileName);
    }
}