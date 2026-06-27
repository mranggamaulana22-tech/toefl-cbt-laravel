<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\StudentDirectoryService;

class StudentController extends Controller
{
    public function __construct(private StudentDirectoryService $studentDirectoryService)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'class' => ['nullable', 'string', 'max:50'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        return view('admin.students.index', $this->studentDirectoryService->indexData($filters));
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
