<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Repositories\QuestionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected QuestionRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(QuestionRepository::class);
    }

    public function test_repository_can_paginate_questions(): void
    {
        Question::factory()->count(25)->create();

        $paginated = $this->repository->paginateFiltered([], 10);

        $this->assertEquals(10, count($paginated->items()));
        $this->assertEquals(3, $paginated->lastPage());
    }

    public function test_repository_can_filter_by_category(): void
    {
        Question::factory()->count(5)->create(['category' => 'listening']);
        Question::factory()->count(3)->create(['category' => 'reading']);

        $paginated = $this->repository->paginateFiltered(['category' => 'listening'], 10);

        $this->assertEquals(5, count($paginated->items()));
        $this->assertEquals('listening', $paginated->items()[0]->category);
    }

    public function test_repository_can_query_with_filters(): void
    {
        Question::factory()->count(5)->create(['category' => 'structure']);
        Question::factory()->count(3)->create(['category' => 'reading']);

        $query = $this->repository->queryFiltered(['category' => 'structure']);
        $questions = $query->get();

        $this->assertCount(5, $questions);
    }

    public function test_repository_can_count_by_category(): void
    {
        Question::factory()->count(8)->create(['category' => 'listening']);
        Question::factory()->count(6)->create(['category' => 'structure']);
        Question::factory()->count(4)->create(['category' => 'reading']);

        $this->assertEquals(8, $this->repository->countByCategory('listening'));
        $this->assertEquals(6, $this->repository->countByCategory('structure'));
        $this->assertEquals(4, $this->repository->countByCategory('reading'));
    }

    public function test_repository_can_get_total_count(): void
    {
        Question::factory()->count(10)->create();

        $this->assertEquals(10, $this->repository->totalCount());
    }

    public function test_repository_can_create_question(): void
    {
        $data = [
            'category' => 'listening',
            'question_text' => 'What is the main idea?',
            'option_a' => 'Option A',
            'option_b' => 'Option B',
            'option_c' => 'Option C',
            'option_d' => 'Option D',
            'correct_answer' => 'A',
        ];

        $question = $this->repository->create($data);

        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'category' => 'listening',
            'question_text' => 'What is the main idea?',
        ]);
    }

    public function test_repository_can_update_question(): void
    {
        $question = Question::factory()->create(['category' => 'listening']);

        $this->repository->update($question->id, [
            'category' => 'reading',
            'question_text' => 'Updated question',
        ]);

        $this->assertDatabaseHas('questions', [
            'id' => $question->id,
            'category' => 'reading',
            'question_text' => 'Updated question',
        ]);
    }

    public function test_repository_can_delete_question(): void
    {
        $question = Question::factory()->create();

        $this->repository->delete($question->id);

        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }
}
