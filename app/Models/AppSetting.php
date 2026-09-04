<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Ambil 1 nilai setting berdasarkan key. Kembalikan $default kalau
     * key belum pernah disimpan sama sekali.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    /**
     * Simpan/update 1 nilai setting. Kalau key sudah ada, di-update;
     * kalau belum, dibuat baru.
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}