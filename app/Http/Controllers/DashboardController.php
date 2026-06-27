<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService)
    {
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            return view('admin.dashboard', $this->dashboardService->adminDashboardData());
        }

        return view('student.dashboard', $this->dashboardService->studentDashboardData($user));
    }

    /**
     * Menampilkan halaman Leaderboard untuk Mode Latihan dengan Pagination.
     */
    public function leaderboard()
    {
        $perPage = (int) request('per_page', 20);
        $sortBy = (string) request('sort', 'best_score');

        return view('student.leaderboard.index', $this->dashboardService->leaderboardData($perPage, $sortBy));
    }
}