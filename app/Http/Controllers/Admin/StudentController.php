<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Services\StudentDirectoryService;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function __construct(private StudentDirectoryService $studentDirectoryService)
    {
    }

    public function index(Request $request): View|JsonResponse
    {
        $filters = $request->validate([
            'class' => ['nullable', 'string', 'max:50'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $data = $this->studentDirectoryService->indexData($filters);

        if ($request->boolean('partial')) {
            return response()->json([
                'html' => view('admin.students.partials.results', [
                    'students' => $data['students'],
                    'filters' => $data['filters'],
                ])->render(),
            ]);
        }

        return view('admin.students.index', $data);
    }

    public function update(Request $request, User $student)
    {
        // Pastikan hanya user dengan role student yang bisa diedit di sini
        if ($student->role !== 'student') {
            abort(404);
        }

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            // Jika kamu menggunakan username atau npm, ganti 'email' menjadi 'username'
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $student->id],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $student->name = $request->name;
        $student->email = $request->email;

        // Hash dan simpan password baru HANYA jika form password diisi
        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
        }

        $student->save();

        return back()->with('success', 'Data peserta ujian dan password berhasil diperbarui.');
    }

    public function destroy(User $student)
    {
        if ($student->role !== 'student') {
            abort(404);
        }

        $this->studentDirectoryService->deleteStudent($student);

        return back()->with('success', 'Data mahasiswa berhasil dihapus.');
    }
}