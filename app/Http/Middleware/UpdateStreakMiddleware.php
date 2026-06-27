<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateStreakMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();
            $isIncreased = $user->updateStreak();

            if ($isIncreased) {
                // Simpan angka streak ke session untuk ditampilkan di dashboard
                session()->flash('show_streak_modal', $user->streak_count);
            }
        }

        return $next($request);
    }
}
