<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category' => fake()->randomElement(['listening', 'structure', 'reading']),
            'question_text' => fake()->sentence(),
            'passage' => fake()->paragraph(),
            'option_a' => fake()->sentence(),
            'option_b' => fake()->sentence(),
            'option_c' => fake()->sentence(),
            'option_d' => fake()->sentence(),
            'correct_answer' => fake()->randomElement(['A', 'B', 'C', 'D']),
            'audio_path' => null,
            'audio_transcript' => fake()->paragraph(),
        ];
    }
}
