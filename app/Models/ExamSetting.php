<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_open',
        'current_cycle',
        'paket_soal_id',
        'access_code',
        'access_code_generated_at',
    ];

    protected $casts = [
        'is_open' => 'boolean',
        'access_code_generated_at' => 'datetime',
    ];

    public function paketSoal(): BelongsTo
    {
        return $this->belongsTo(PaketSoal::class);
    }

    public static function current(): self
    {
        $singleton = self::query()->find(1);
        if ($singleton) {
            return $singleton;
        }

        $latest = self::query()
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->first();

        $singleton = new self();
        $singleton->forceFill([
            'id' => 1,
            'is_open' => (bool) ($latest?->is_open ?? false),
            'current_cycle' => (int) ($latest?->current_cycle ?? 0),
        ]);
        $singleton->save();

        return $singleton;
    }
}