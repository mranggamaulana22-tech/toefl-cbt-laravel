<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_open',
        'current_cycle',
    ];

    protected $casts = [
        'is_open' => 'boolean',
    ];

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
