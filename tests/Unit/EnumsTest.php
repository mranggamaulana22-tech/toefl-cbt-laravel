<?php

namespace Tests\Unit;

use App\Enums\QuestionCategory;
use App\Enums\UserRole;
use App\Enums\AiStatus;
use PHPUnit\Framework\TestCase;

class EnumsTest extends TestCase
{
    public function test_question_category_enum_has_correct_values(): void
    {
        $this->assertEquals('listening', QuestionCategory::LISTENING->value);
        $this->assertEquals('structure', QuestionCategory::STRUCTURE->value);
        $this->assertEquals('reading', QuestionCategory::READING->value);
    }

    public function test_question_category_enum_labels(): void
    {
        $this->assertEquals('Listening', QuestionCategory::LISTENING->label());
        $this->assertEquals('Structure & Written Expression', QuestionCategory::STRUCTURE->label());
        $this->assertEquals('Reading Comprehension', QuestionCategory::READING->label());
    }

    public function test_question_category_enum_values_method(): void
    {
        $values = QuestionCategory::values();
        $this->assertCount(3, $values);
        $this->assertContains('listening', $values);
        $this->assertContains('structure', $values);
        $this->assertContains('reading', $values);
    }

    public function test_user_role_enum_has_correct_values(): void
    {
        $this->assertEquals('admin', UserRole::ADMIN->value);
        $this->assertEquals('student', UserRole::STUDENT->value);
    }

    public function test_user_role_enum_labels(): void
    {
        $this->assertEquals('Administrator', UserRole::ADMIN->label());
        $this->assertEquals('Student', UserRole::STUDENT->label());
    }

    public function test_ai_status_enum_has_correct_values(): void
    {
        $this->assertEquals('pending', AiStatus::PENDING->value);
        $this->assertEquals('processing', AiStatus::PROCESSING->value);
        $this->assertEquals('done', AiStatus::DONE->value);
        $this->assertEquals('failed', AiStatus::FAILED->value);
    }

    public function test_ai_status_enum_labels(): void
    {
        $this->assertEquals('Pending', AiStatus::PENDING->label());
        $this->assertEquals('Processing', AiStatus::PROCESSING->label());
        $this->assertEquals('Done', AiStatus::DONE->label());
        $this->assertEquals('Failed', AiStatus::FAILED->label());
    }

    public function test_ai_status_is_terminal(): void
    {
        $this->assertFalse(AiStatus::PENDING->isTerminal());
        $this->assertFalse(AiStatus::PROCESSING->isTerminal());
        $this->assertTrue(AiStatus::DONE->isTerminal());
        $this->assertTrue(AiStatus::FAILED->isTerminal());
    }
}
