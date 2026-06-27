<?php

namespace App\Services;

use App\Models\ExamSetting;
use App\Models\Result;

class ExamControlService
{
    public function startSession(): array
    {
        $setting = ExamSetting::current();

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
            'message' => 'Sesi ujian dibuka. Mahasiswa sekarang bisa mengikuti 1 kali tes untuk sesi ini.',
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