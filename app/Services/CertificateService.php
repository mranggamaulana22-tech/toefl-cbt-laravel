<?php

namespace App\Services;

use App\Models\Result;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class CertificateService
{
    /**
     * Generate the certificate PDF for a submitted exam result and store it.
     * Returns the storage path (relative to the "public" disk).
     */
    public function generate(Result $result): string
    {
        $result->loadMissing('user');

        $pdf = Pdf::loadView('student.results.certificate-pdf', ['result' => $result])
            ->setPaper('a4', 'landscape');

        $path = "certificates/certificate_{$result->id}.pdf";

        Storage::disk('public')->put($path, $pdf->output());

        return $path;
    }

    /**
     * Generate (if missing) and return the certificate path for a result.
     * Safe to call multiple times — regenerates only if the file doesn't exist yet.
     */
    public function ensureGenerated(Result $result): string
    {
        if ($result->certificate_path && Storage::disk('public')->exists($result->certificate_path)) {
            return $result->certificate_path;
        }

        $path = $this->generate($result);

        $result->update(['certificate_path' => $path]);

        return $path;
    }
}