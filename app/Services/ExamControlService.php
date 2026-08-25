<?php

namespace App\Services;

use Illuminate\Support\Str;
use App\Models\ExamSetting;
use App\Models\PaketSoal;
use App\Models\Result;

class ExamControlService
{
    public function __construct(
        private QuestionSelectionService $questionSelectionService,
    ) {
    }

    /**
     * Membuka sesi ujian baru untuk paket soal tertentu.
     *
     * $paketSoalId wajib diisi admin dari dropdown pilihan paket saat
     * membuka sesi — ini yang menentukan soal mana yang akan ditarik
     * QuestionSelectionService nanti saat mahasiswa mulai ujian.
     */
    public function startSession(int $paketSoalId): array
    {
        $setting = ExamSetting::current();

        $paketSoal = PaketSoal::find($paketSoalId);

        if (! $paketSoal) {
            return [
                'ok' => false,
                'message' => 'Paket soal yang dipilih tidak ditemukan.',
            ];
        }

        $readiness = $this->questionSelectionService->getExamReadinessReport($paketSoalId);

        if (! $readiness['can_start']) {
            $messages = [];

            foreach ($readiness['sections'] as $section => $data) {
                if (($data['shortage'] ?? 0) > 0) {
                    $messages[] = ucfirst($section) . ' kurang ' . $data['shortage'] . ' soal (tersedia ' . $data['available'] . ', butuh ' . $data['required'] . ').';
                }
            }

            return [
                'ok' => false,
                'message' => "Sesi ujian tidak bisa dibuka karena \"{$paketSoal->nama}\" belum memenuhi kebutuhan 140 soal: " . implode(' ', $messages),
            ];
        }

        if ($setting->is_open) {
            return [
                'ok' => false,
                'message' => 'Sesi ujian masih aktif. Tutup sesi saat ini dulu sebelum membuka sesi baru.',
            ];
        }

        $setting->current_cycle = (int) $setting->current_cycle + 1;
        $setting->is_open = true;
        $setting->paket_soal_id = $paketSoal->id;
        $setting->access_code = Str::upper(Str::random(6));
        $setting->access_code_generated_at = now();
        $setting->save();

        return [
            'ok' => true,
            'message' => "Sesi ujian dibuka dengan {$paketSoal->nama}. Kode akses: " . $setting->access_code,
        ];
    }

    public function closeSession(): array
    {
        $setting = ExamSetting::current();

        if (!$setting->is_open) {
            return [
                'ok' => false,
                'message' => 'Tidak ada sesi ujian aktif untuk ditutup.',
            ];
        }

        $activeCount = Result::where('exam_cycle', $setting->current_cycle)
            ->whereNotNull('started_at')
            ->whereNull('submitted_at')
            ->count();

        if ($activeCount > 0) {
            return [
                'ok' => false,
                'message' => "Tidak bisa menutup sesi. Ada $activeCount mahasiswa yang masih mengerjakan ujian. Tunggu hingga semua selesai atau minta mahasiswa submit jawaban mereka.",
            ];
        }

        $setting->is_open = false;
        $setting->save();

        return [
            'ok' => true,
            'message' => 'Sesi ujian ditutup. Mahasiswa tidak bisa memulai ujian sampai sesi baru dibuka.',
        ];
    }

    public function regenerateAccessCode(): array
    {
        $setting = ExamSetting::current();

        if (! $setting->is_open) {
            return [
                'ok' => false,
                'message' => 'Tidak ada sesi ujian aktif untuk generate ulang kode akses.',
            ];
        }

        $setting->access_code = Str::upper(Str::random(6));
        $setting->access_code_generated_at = now();
        $setting->save();

        return [
            'ok' => true,
            'message' => 'Kode akses baru berhasil dibuat: ' . $setting->access_code,
        ];
    }

    public function getCurrentAccessCode(): ?string
    {
        return ExamSetting::current()->access_code;
    }

    public function verifyAccessCode(string $code): bool
    {
        $setting = ExamSetting::current();

        if (! $setting->is_open || ! $setting->access_code) {
            return false;
        }

        return strtoupper(trim($code)) === strtoupper(trim($setting->access_code));
    }
}