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
     * - Jika login besoknya, tambah 1.
     * - Jika login hari ini lagi, tidak berubah.
     * - Jika bolos, balik ke 1.
     *
     * @return bool $increased True jika streak bertambah/reset, false jika tidak berubah
     */
    public function updateStreak()
    {
        $now = now();
        $today = $now->copy()->startOfDay();
        $increased = false;

        if (!$this->last_active_at) {
            $this->streak_count = 1;
            $increased = true; // Login pertama kali
        } else {
            $lastActive = \Carbon\Carbon::parse($this->last_active_at)->startOfDay();
            $diff = $today->diffInDays($lastActive);

            if ($diff == 1) {
                $this->streak_count += 1;
                $increased = true; // Streak bertambah
            } elseif ($diff > 1) {
                $this->streak_count = 1;
                $increased = true; // Reset setelah bolos
            }
            // Jika $diff == 0, tidak berubah
        }

        $this->last_active_at = $now;
        $this->save();

        return $increased;
    }
}
