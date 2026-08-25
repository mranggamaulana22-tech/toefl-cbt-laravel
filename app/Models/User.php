<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'npm',
        'class',
        'angkatan',
        'role',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // PENTING: last_active_at WAJIB di-cast ke datetime, supaya setiap kali
        // model di-load dari DB, atribut ini otomatis jadi instance Carbon yang
        // konsisten (zona waktu & format seragam) — bukan string mentah yang
        // harus di-parse ulang manual tiap kali dipakai.
        'last_active_at' => 'datetime',
    ];

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (! $this->profile_photo_path) {
            return null;
        }

        return asset('storage/'.$this->profile_photo_path);
    }

    /**
     * Update user login streak dan return status apakah streak bertambah.
     *
     * - Jika login besoknya (hari kalender berikutnya), tambah 1.
     * - Jika login hari ini lagi (masih hari kalender yang sama), tidak berubah,
     *   dan TIDAK menyentuh database sama sekali (return false).
     * - Jika bolos lebih dari 1 hari, balik ke 1.
     *
     * Menggunakan isSameDay()/isYesterday() (bukan diffInDays) karena lebih
     * eksplisit membaca maksud "hari kalender yang sama", dan tidak rawan
     * salah hitung akibat perbedaan jam/menit/detik atau isu zona waktu
     * yang kadang muncul kalau bergantung pada selisih durasi mentah.
     *
     * @return bool $increased True jika streak bertambah/reset, false jika tidak berubah
     */
    public function updateStreak(): bool
    {
        $now = now();

        if (!$this->last_active_at) {
            $this->streak_count = 1;
            $this->last_active_at = $now;
            $this->save();

            return true;
        }

        // Berkat cast 'datetime' di atas, $this->last_active_at sudah pasti
        // instance Carbon yang valid, tidak perlu parse manual lagi.
        $lastActive = $this->last_active_at;

        if ($lastActive->isSameDay($now)) {
            // Sudah aktif hari ini — tidak ada yang berubah, skip update sama sekali.
            return false;
        }

        if ($lastActive->isSameDay($now->copy()->subDay())) {
            // Aktif terakhir kemarin -> lanjutkan streak.
            $this->streak_count += 1;
        } else {
            // Bolos lebih dari 1 hari -> reset ke 1.
            $this->streak_count = 1;
        }

        $this->last_active_at = $now;
        $this->save();

        return true;
    }
}