<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\GradebookController;
use App\Http\Controllers\Admin\ExamControlController;
use App\Http\Controllers\Admin\PracticeHistoryController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\PracticeQuestionController;
use App\Http\Controllers\Admin\StudentController;
use App\Http\Controllers\Student\ExamController;
use App\Http\Controllers\Student\PracticeController;
use App\Http\Controllers\Student\ResultController;
use App\Http\Controllers\Student\AiSuggestionController;
use App\Http\Controllers\Student\PracticeReviewController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'update.streak'])
    ->name('dashboard');

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('/admin/exam/start-session', [ExamControlController::class, 'startSession'])->name('admin.exam.start-session');
    Route::post('/admin/exam/close-session', [ExamControlController::class, 'closeSession'])->name('admin.exam.close-session');
    Route::get('/questions/export/csv', [QuestionController::class, 'exportCsv'])->name('questions.export.csv');
    Route::resource('questions', QuestionController::class);
    Route::get('/admin/practice-questions/export/csv', [PracticeQuestionController::class, 'exportCsv'])->name('admin.practice-questions.export.csv');
    Route::resource('admin/practice-questions', PracticeQuestionController::class, ['names' => 'admin.practice-questions']);
    Route::get('/admin/practice-history', [PracticeHistoryController::class, 'index'])->name('practice-history.index');
    Route::get('/admin/gradebook', [GradebookController::class, 'index'])->name('gradebook.index');
    Route::get('/admin/gradebook/export/csv', [GradebookController::class, 'exportCsv'])->name('gradebook.export.csv');
    Route::get('/admin/students', [StudentController::class, 'index'])->name('students.index');
    Route::delete('/admin/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
});

Route::middleware(['auth', 'verified', 'student'])->group(function () {
    // Leaderboard
    Route::get('/student/leaderboard', [DashboardController::class, 'leaderboard'])->name('leaderboard');

    // Exam routes (full 50-question exam)
    Route::get('/exam/start', [ExamController::class, 'start'])->name('exam.start');
    Route::get('/exam/test', [ExamController::class, 'test'])->name('exam.test');
    Route::post('/exam/submit', [ExamController::class, 'submit'])->name('exam.submit');

    // Practice routes (unlimited questions)
    Route::get('/practice/start', [PracticeController::class, 'start'])->name('practice.start');
    Route::get('/practice/test', [PracticeController::class, 'test'])->name('practice.test');
    Route::post('/practice/submit', [PracticeController::class, 'submit'])->name('practice.submit');
    
    // Results routes
    Route::get('/student/results', [ResultController::class, 'index'])->name('student.results.index');
    Route::get('/student/ai-analysis', [AiSuggestionController::class, 'index'])->name('student.ai.index');
    Route::get('/student/review', [PracticeReviewController::class, 'index'])->name('student.review.index');
    Route::get('/student/review/{practiceResult}', [PracticeReviewController::class, 'show'])->name('student.review.show');
    Route::get('/student/results/exams', [ResultController::class, 'examHistory'])->name('student.results.exams');
    Route::get('/student/results/practices', [ResultController::class, 'practiceHistory'])->name('student.results.practices');
    Route::get('/student/results/{result}/certificate', [ResultController::class, 'certificate'])->name('student.results.certificate');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';