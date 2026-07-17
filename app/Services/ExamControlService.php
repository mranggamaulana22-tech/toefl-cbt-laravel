<?php

namespace App\Services;

use App\Models\ExamSetting;
use App\Models\Result;

class ExamControlService
{
    public function __construct(
        private QuestionSelectionService $questionSelectionService,
    ) {
    }

    public function startSession(): array
    {
        $setting = ExamSetting::current();

        $readiness = $this->questionSelectionService->getExamReadinessReport();

        if (! $readiness['can_start']) {
            $messages = [];

            foreach ($readiness['sections'] as $section => $data) {
                if (($data['shortage'] ?? 0) > 0) {
                    $messages[] = ucfirst($section) . ' kurang ' . $data['shortage'] . ' soal (tersedia ' . $data['available'] . ', butuh ' . $data['required'] . ').';
                }
            }

            return [
                'ok' => false,
                'message' => 'Sesi ujian tidak bisa dibuka karena bank soal belum memenuhi kebutuhan 140 soal: ' . implode(' ', $messages),
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
        $setting->save();

        return [
            'ok' => true,
            'message' => 'Sesi ujian dibuka. Mahasiswa sekarang bisa mengikuti 140 soal dengan komposisi 50 listening, 40 structure, dan 50 reading.',
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
}