<?php

namespace Tests\Feature;

use App\Models\ExamSession;
use App\Models\ExamSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_exam_session_can_be_created(): void
    {
        $user = User::factory()->create();
        $setting = ExamSetting::factory()->create(['is_open' => true]);

        $session = ExamSession::create([
            'user_id' => $user->id,
            'exam_settings_id' => $setting->id,
            'exam_cycle' => 1,
            'question_ids' => [1, 2, 3, 4, 5],
            'current_question_index' => 0,
            'answers' => [],
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $this->assertDatabaseHas('exam_sessions', [
            'user_id' => $user->id,
            'exam_cycle' => 1,
            'status' => 'in_progress',
        ]);
    }

    public function test_exam_session_is_in_progress(): void
    {
        $user = User::factory()->create();
        $setting = ExamSetting::factory()->create();

        $session = ExamSession::create([
            'user_id' => $user->id,
            'exam_settings_id' => $setting->id,
            'exam_cycle' => 1,
            'question_ids' => [1, 2, 3],
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $this->assertTrue($session->isInProgress());
        $this->assertFalse($session->isSubmitted());
    }

    public function test_exam_session_can_be_marked_submitted(): void
    {
        $user = User::factory()->create();
        $setting = ExamSetting::factory()->create();

        $session = ExamSession::create([
            'user_id' => $user->id,
            'exam_settings_id' => $setting->id,
            'exam_cycle' => 1,
            'question_ids' => [1, 2, 3],
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $session->markSubmitted();

        $this->assertTrue($session->isSubmitted());
        $this->assertNotNull($session->submitted_at);
        $this->assertEquals('submitted', $session->status);
    }

    public function test_exam_session_can_record_answer(): void
    {
        $user = User::factory()->create();
        $setting = ExamSetting::factory()->create();

        $session = ExamSession::create([
            'user_id' => $user->id,
            'exam_settings_id' => $setting->id,
            'exam_cycle' => 1,
            'question_ids' => [1, 2, 3],
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $session->recordAnswer(1, 'A');
        $this->assertEquals('A', $session->getAnswer(1));

        $session->recordAnswer(2, 'B');
        $this->assertEquals('B', $session->getAnswer(2));
    }

    public function test_exam_session_get_current_question_id(): void
    {
        $user = User::factory()->create();
        $setting = ExamSetting::factory()->create();

        $session = ExamSession::create([
            'user_id' => $user->id,
            'exam_settings_id' => $setting->id,
            'exam_cycle' => 1,
            'question_ids' => [10, 20, 30, 40, 50],
            'current_question_index' => 0,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $this->assertEquals(10, $session->getCurrentQuestionId());
    }

    public function test_exam_session_move_to_next_question(): void
    {
        $user = User::factory()->create();
        $setting = ExamSetting::factory()->create();

        $session = ExamSession::create([
            'user_id' => $user->id,
            'exam_settings_id' => $setting->id,
            'exam_cycle' => 1,
            'question_ids' => [10, 20, 30],
            'current_question_index' => 0,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $this->assertEquals(10, $session->getCurrentQuestionId());
        $this->assertTrue($session->moveToNextQuestion());
        $this->assertEquals(20, $session->getCurrentQuestionId());
        $this->assertTrue($session->moveToNextQuestion());
        $this->assertEquals(30, $session->getCurrentQuestionId());
        // Should return false when at end
        $this->assertFalse($session->moveToNextQuestion());
    }

    public function test_exam_session_can_be_marked_abandoned(): void
    {
        $user = User::factory()->create();
        $setting = ExamSetting::factory()->create();

        $session = ExamSession::create([
            'user_id' => $user->id,
            'exam_settings_id' => $setting->id,
            'exam_cycle' => 1,
            'question_ids' => [1, 2, 3],
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $session->markAbandoned();

        $this->assertEquals('abandoned', $session->status);
        $this->assertNotNull($session->abandoned_at);
    }
}
