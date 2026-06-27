<?php

use App\Http\Controllers\Student\AiSuggestionController;
use App\Http\Controllers\Student\PracticeProgressController;
use App\Http\Controllers\Student\PracticeReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Health check endpoint (no auth required)
Route::get('/health', function () {
    return response()->json(['status' => 'ok'], 200);
});

// Protected routes requiring authentication
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // API v1 routes
    Route::prefix('v1')->group(function () {
        Route::prefix('student')->middleware('student')->group(function () {
            // Practice Progress API
            Route::prefix('practice')->group(function () {
                Route::get('progress', [PracticeProgressController::class, 'get'])
                    ->middleware('throttle:student-practice-progress')
                    ->name('api.v1.practice.progress.show');
                Route::post('progress', [PracticeProgressController::class, 'save'])
                    ->middleware('throttle:student-practice-progress')
                    ->name('api.v1.practice.progress.save');
                Route::delete('progress', [PracticeProgressController::class, 'clear'])
                    ->middleware('throttle:student-practice-progress')
                    ->name('api.v1.practice.progress.clear');
            });

            // AI Suggestion API
            Route::prefix('suggestion')->group(function () {
                Route::post('exam/{result}', [AiSuggestionController::class, 'generateForExam'])
                    ->middleware('throttle:student-ai-suggestion')
                    ->name('api.v1.suggestion.exam.generate');
                Route::get('exam/{result}/status', [AiSuggestionController::class, 'examStatus'])
                    ->middleware('throttle:student-ai-dashboard')
                    ->name('api.v1.suggestion.exam.status');
                Route::post('practice/{practiceResult}', [AiSuggestionController::class, 'generateForPractice'])
                    ->middleware('throttle:student-ai-suggestion')
                    ->name('api.v1.suggestion.practice.generate');
                Route::get('practice/{practiceResult}/status', [AiSuggestionController::class, 'practiceStatus'])
                    ->middleware('throttle:student-ai-dashboard')
                    ->name('api.v1.suggestion.practice.status');
                Route::get('dashboard', [AiSuggestionController::class, 'getDashboardSuggestions'])
                    ->middleware('throttle:student-ai-dashboard')
                    ->name('api.v1.suggestion.dashboard');
            });

            // Practice Review API
            Route::prefix('review')->group(function () {
                Route::post('{practiceResult}/items/{item}', [PracticeReviewController::class, 'reviewItem'])
                    ->middleware('throttle:student-practice-review')
                    ->name('api.v1.review.item');
            });
        });
    });
});
