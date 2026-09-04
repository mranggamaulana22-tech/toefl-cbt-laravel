<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\GradebookController;
use App\Http\Controllers\Admin\ExamControlController;
use App\Http\Controllers\Admin\PracticeHistoryController;
use App\Http\Controllers\Admin\PaketSoalController;
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
    Route::post('/admin/exam/regenerate-code', [ExamControlController::class, 'regenerateAccessCode'])->name('admin.exam.regenerate-code');

    // Paket Soal: halaman induk yang list semua paket ("bank soal" mandiri).
    Route::get('/admin/paket-soal', [PaketSoalController::class, 'index'])->name('paket-soal.index');
    Route::post('/admin/paket-soal', [PaketSoalController::class, 'store'])->name('paket-soal.store');
    Route::delete('/admin/paket-soal/{paketSoal}', [PaketSoalController::class, 'destroy'])->name('paket-soal.destroy');

    // Soal Ujian: sekarang dinested di bawah satu paket. Route custom
    // (export/import/template) didaftarkan SEBELUM resource, supaya kata
    // "export"/"import" tidak ketangkap sebagai {question} oleh route
    // "show" milik resource.
    Route::get('/admin/paket-soal/{paketSoal}/questions/export/csv', [QuestionController::class, 'exportXlsx'])
        ->name('paket-soal.questions.export.csv');
    Route::get('/admin/paket-soal/{paketSoal}/questions/import/template', [QuestionController::class, 'importTemplate'])
        ->name('paket-soal.questions.import.template');
    Route::post('/admin/paket-soal/{paketSoal}/questions/import', [QuestionController::class, 'import'])
        ->name('paket-soal.questions.import');

    // Azure TTS: setting suara per-paket + generate audio untuk Soal Ujian.
    // Didaftarkan SEBELUM resource, alasan sama seperti export/import di atas.
    Route::post('/admin/paket-soal/{paketSoal}/voice-settings', [QuestionController::class, 'saveVoiceSettings'])
        ->name('paket-soal.voice-settings.save');
    Route::post('/admin/paket-soal/{paketSoal}/questions/generate-audio-batch', [QuestionController::class, 'generateAudioBatch'])
        ->name('paket-soal.questions.generate-audio-batch');
    Route::post('/admin/paket-soal/{paketSoal}/questions/{question}/generate-audio', [QuestionController::class, 'generateAudio'])
        ->name('paket-soal.questions.generate-audio');

    Route::resource('admin/paket-soal/{paketSoal}/questions', QuestionController::class)
        ->names('paket-soal.questions');

    Route::get('/admin/practice-questions/export/csv', [PracticeQuestionController::class, 'exportXlsx'])->name('admin.practice-questions.export.csv');
    Route::get('/admin/practice-questions/import/template', [PracticeQuestionController::class, 'importTemplate'])->name('admin.practice-questions.import.template');
    Route::post('/admin/practice-questions/import', [PracticeQuestionController::class, 'import'])->name('admin.practice-questions.import');
    Route::delete('/admin/practice-questions', [PracticeQuestionController::class, 'destroyAll'])->name('admin.practice-questions.destroy-all');

    Route::get('/admin/practice-questions/voice-settings', [PracticeQuestionController::class, 'getVoiceSettings'])->name('admin.practice-questions.voice-settings.get');
    Route::post('/admin/practice-questions/voice-settings', [PracticeQuestionController::class, 'saveVoiceSettings'])->name('admin.practice-questions.voice-settings.save');
    Route::post('/admin/practice-questions/generate-audio-batch', [PracticeQuestionController::class, 'generateAudioBatch'])->name('admin.practice-questions.generate-audio-batch');
    Route::post('/admin/practice-questions/{practiceQuestion}/generate-audio', [PracticeQuestionController::class, 'generateAudio'])->name('admin.practice-questions.generate-audio');
    Route::post('/admin/tts/preview-voice', [PracticeQuestionController::class, 'previewVoice'])->name('admin.tts.preview-voice');

    Route::resource('admin/practice-questions', PracticeQuestionController::class, ['names' => 'admin.practice-questions']);
    Route::get('/admin/practice-history', [PracticeHistoryController::class, 'index'])->name('practice-history.index');
    Route::get('/admin/practice-history/export/xlsx', [PracticeHistoryController::class, 'exportXlsx'])->name('practice-history.export.csv');
    Route::get('/admin/gradebook', [GradebookController::class, 'index'])->name('gradebook.index');
    Route::get('/admin/gradebook/export/xlsx', [GradebookController::class, 'exportXlsx'])->name('gradebook.export.csv');

    // Student Management Routes
    Route::get('/admin/students', [StudentController::class, 'index'])->name('students.index');
    Route::put('/admin/students/{student}', [StudentController::class, 'update'])->name('students.update'); // Route Baru untuk Update & Reset Password
    Route::delete('/admin/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
});

Route::middleware(['auth', 'verified', 'student'])->group(function () {
    // Leaderboard
    Route::get('/student/leaderboard', [DashboardController::class, 'leaderboard'])->name('leaderboard');

    // Exam routes (full 140-question exam)
    Route::get('/exam/start', [ExamController::class, 'start'])->name('exam.start');
    Route::post('/exam/enter', [ExamController::class, 'enter'])->name('exam.enter');
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
    Route::get('/student/results/{result}/certificate/download', [ResultController::class, 'downloadCertificate'])->name('student.results.certificate.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';