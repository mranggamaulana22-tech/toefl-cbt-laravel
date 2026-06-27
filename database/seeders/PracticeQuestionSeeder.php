<?php

namespace Database\Seeders;

use App\Models\PracticeQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PracticeQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'category' => 'listening',
                'audio_path' => null,
                'passage' => null,
                'question_text' => '[Listening Practice] What time does the office close?',
                'option_a' => '4:00 PM',
                'option_b' => '5:00 PM',
                'option_c' => '4:30 PM',
                'option_d' => '5:30 PM',
                'correct_answer' => 'A',
            ],
            [
                'category' => 'listening',
                'audio_path' => null,
                'passage' => null,
                'question_text' => '[Listening Practice] Where is the man traveling next week?',
                'option_a' => 'To New York',
                'option_b' => 'To Boston',
                'option_c' => 'To Chicago',
                'option_d' => 'To Los Angeles',
                'correct_answer' => 'B',
            ],
            [
                'category' => 'listening',
                'audio_path' => null,
                'passage' => null,
                'question_text' => '[Listening Practice] What does the woman need to complete her project?',
                'option_a' => 'More time',
                'option_b' => 'Additional resources',
                'option_c' => 'Team members',
                'option_d' => 'Budget approval',
                'correct_answer' => 'D',
            ],
            [
                'category' => 'structure',
                'audio_path' => null,
                'passage' => null,
                'question_text' => '[Grammar Practice] The students ____ their homework before class.',
                'option_a' => 'completes',
                'option_b' => 'complete',
                'option_c' => 'completed',
                'option_d' => 'have completed',
                'correct_answer' => 'D',
            ],
            [
                'category' => 'structure',
                'audio_path' => null,
                'passage' => null,
                'question_text' => '[Grammar Practice] Neither the teacher nor the students ____ the correct answer.',
                'option_a' => 'knows',
                'option_b' => 'know',
                'option_c' => 'have known',
                'option_d' => 'is knowing',
                'correct_answer' => 'B',
            ],
            [
                'category' => 'reading',
                'passage' => "Climate change is affecting weather patterns globally. Rising temperatures are causing glaciers to melt, sea levels to rise, and ecosystems to shift. Scientists warn that immediate action is necessary to prevent further damage.",
                'audio_path' => null,
                'question_text' => '[Reading Practice] What is melting due to rising temperatures?',
                'option_a' => 'Ocean water',
                'option_b' => 'Glaciers',
                'option_c' => 'Polar ice caps',
                'option_d' => 'Mountain rocks',
                'correct_answer' => 'B',
            ],
            [
                'category' => 'reading',
                'passage' => "Climate change is affecting weather patterns globally. Rising temperatures are causing glaciers to melt, sea levels to rise, and ecosystems to shift. Scientists warn that immediate action is necessary to prevent further damage.",
                'audio_path' => null,
                'question_text' => '[Reading Practice] According to the passage, what do scientists warn is necessary?',
                'option_a' => 'Reducing pollution',
                'option_b' => 'Planting trees',
                'option_c' => 'Immediate action',
                'option_d' => 'Building dams',
                'correct_answer' => 'C',
            ],
            [
                'category' => 'reading',
                'passage' => "The internet has revolutionized communication, making it possible to connect with people worldwide instantly. However, excessive screen time has raised concerns about mental health and social isolation among young people.",
                'audio_path' => null,
                'question_text' => '[Reading Practice] What is one benefit of the internet mentioned in the passage?',
                'option_a' => 'Improved productivity',
                'option_b' => 'Instant worldwide communication',
                'option_c' => 'Better education',
                'option_d' => 'Enhanced entertainment',
                'correct_answer' => 'B',
            ],
            [
                'category' => 'reading',
                'passage' => "The internet has revolutionized communication, making it possible to connect with people worldwide instantly. However, excessive screen time has raised concerns about mental health and social isolation among young people.",
                'audio_path' => null,
                'question_text' => '[Reading Practice] What concern is raised about excessive screen time?',
                'option_a' => 'Eye damage',
                'option_b' => 'Addiction to games',
                'option_c' => 'Mental health and social isolation',
                'option_d' => 'Radiation exposure',
                'correct_answer' => 'C',
            ],
            [
                'category' => 'reading',
                'passage' => "Regular exercise has been proven to improve both physical and mental health. It reduces the risk of heart disease, diabetes, and obesity, while also boosting mood and reducing stress levels in individuals.",
                'audio_path' => null,
                'question_text' => '[Reading Practice] What diseases can regular exercise reduce the risk of?',
                'option_a' => 'Flu and cold',
                'option_b' => 'Heart disease and diabetes',
                'option_c' => 'Allergies',
                'option_d' => 'Asthma and arthritis',
                'correct_answer' => 'B',
            ],
        ];

        foreach ($questions as $question) {
            PracticeQuestion::updateOrCreate(
                [
                    'category' => $question['category'],
                    'question_text' => $question['question_text'],
                ],
                $question
            );
        }
    }
}
