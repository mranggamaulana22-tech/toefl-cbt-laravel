<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaketSoal;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaketSoalController extends Controller
{
    /**
     * List semua paket soal beserta status kelengkapannya
     * (140 soal: 50 listening, 40 structure, 50 reading).
     * Dari sini admin masuk ke satu paket untuk kelola soalnya
     * (lihat QuestionController::index yang sudah discope per paket).
     */
    public function index(): View
    {
        $pakets = PaketSoal::withCount('questions')
            ->oldest()
            ->get()
            ->map(function (PaketSoal $paket) {
                $report = $paket->readinessReport();

                return [
                    'model' => $paket,
                    'is_complete' => $report['can_start'],
                    'available_total' => $report['available_total'],
                    'required_total' => $report['required_total'],
                    'sections' => $report['sections'],
                ];
            });

        return view('admin.paket-soal.index', compact('pakets'));
    }

    /**
     * Buat paket baru (kosong). Soalnya diisi belakangan lewat
     * halaman kelola soal khusus paket ini (QuestionController).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255', 'unique:paket_soals,nama'],
        ], [
            'nama.unique' => 'Nama paket ini sudah dipakai, gunakan nama lain.',
        ]);

        $paket = PaketSoal::create($validated);

        return redirect()
            ->route('paket-soal.questions.index', $paket)
            ->with('success', "Paket \"{$paket->nama}\" berhasil dibuat. Silakan tambahkan 140 soal.");
    }

    /**
     * Hapus paket beserta seluruh soal di dalamnya (cascade lewat FK).
     *
     * Ditolak database (restrictOnDelete) kalau paket ini pernah/sedang
     * jadi paket aktif di exam_settings — supaya riwayat sesi ujian yang
     * memakainya tidak kehilangan jejak paket soal yang dipakai.
     */
    public function destroy(PaketSoal $paketSoal): RedirectResponse
    {
        try {
            $paketSoal->delete();
        } catch (QueryException $e) {
            return back()->with(
                'error',
                "Paket \"{$paketSoal->nama}\" tidak bisa dihapus karena pernah/sedang dipakai untuk sesi ujian. Hapus hanya paket yang belum pernah dipakai."
            );
        }

        return back()->with('success', "Paket \"{$paketSoal->nama}\" beserta soal di dalamnya berhasil dihapus.");
    }
}