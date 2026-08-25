<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaketSoal extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
    ];

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Cek apakah paket ini sudah memenuhi komposisi wajib
     * (default 50 listening, 40 structure, 50 reading = 140 total)
     * berdasarkan config/exam.php, bukan angka hardcode.
     */
    public function isComplete(): bool
    {
        return $this->readinessReport()['can_start'];
    }

    /**
     * Laporan kelengkapan per kategori untuk paket ini.
     * Struktur returnnya sama persis dengan
     * QuestionSelectionService::getExamReadinessReport(), tapi discope
     * ke paket ini saja — dipakai baik di halaman kelola paket maupun
     * saat validasi sebelum paket boleh dipilih untuk sesi ujian.
     */
    public function readinessReport(): array
    {
        $examConfig = config('exam.exam');
        $sectionTargets = $examConfig['sections'];
        $sectionOrder = $examConfig['section_order'];

        $sections = [];
        $canStart = true;

        foreach ($sectionOrder as $section) {
            $required = (int) ($sectionTargets[$section] ?? 0);
            $available = (int) $this->questions()->where('category', $section)->count();
            $shortage = max(0, $required - $available);

            if ($shortage > 0) {
                $canStart = false;
            }

            $sections[$section] = [
                'required' => $required,
                'available' => $available,
                'shortage' => $shortage,
            ];
        }

        return [
            'can_start' => $canStart,
            'required_total' => (int) $examConfig['total_questions'],
            'available_total' => (int) $this->questions()->count(),
            'sections' => $sections,
        ];
    }
}