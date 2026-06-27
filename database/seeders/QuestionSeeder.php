<?php

namespace Database\Seeders;

use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    /**
     * Seed sample TOEFL questions.
     */
    public function run(): void
    {
        $questions = [
            [
                'category' => 'listening',
                'audio_path' => null,
                'passage' => null,
                'question_text' => 'What does the man suggest doing?',
                'option_a' => 'Leaving immediately',
                'option_b' => 'Waiting for the bus',
                'option_c' => 'Calling a taxi',
                'option_d' => 'Walking home',
                'correct_answer' => 'B',
            ],
            [
                'category' => 'listening',
                'audio_path' => null,
                'passage' => null,
                'question_text' => 'Why is the woman late?',
                'option_a' => 'She missed the train',
                'option_b' => 'Her car broke down',
                'option_c' => 'She overslept',
                'option_d' => 'She had a meeting',
                'correct_answer' => 'C',
            ],
            [
                'category' => 'listening',
                'audio_path' => null,
                'passage' => null,
                'question_text' => 'Where does the conversation probably take place?',
                'option_a' => 'In a restaurant',
                'option_b' => 'In a library',
                'option_c' => 'At a hospital',
                'option_d' => 'At a bank',
                'correct_answer' => 'B',
            ],
            [
                'category' => 'structure',
                'audio_path' => null,
                'passage' => null,
                'question_text' => 'Each of the students ____ a handbook before the exam.',
                'option_a' => 'receive',
                'option_b' => 'receives',
                'option_c' => 'received',
                'option_d' => 'receiving',
                'correct_answer' => 'B',
            ],
            [
                'category' => 'structure',
                'audio_path' => null,
                'passage' => null,
                'question_text' => 'The report, together with the charts, ____ on the desk.',
                'option_a' => 'is',
                'option_b' => 'are',
                'option_c' => 'were',
                'option_d' => 'have been',
                'correct_answer' => 'A',
            ],
            [
                'category' => 'structure',
                'audio_path' => null,
                'passage' => null,
                'question_text' => 'If he ____ harder, he would pass the test.',
                'option_a' => 'studies',
                'option_b' => 'studied',
                'option_c' => 'has studied',
                'option_d' => 'had studied',
                'correct_answer' => 'B',
            ],
            [
                'category' => 'reading',
                'passage' => "The library at Piksi University offers thousands of books, journals, and digital resources for students. It is open from 8 a.m. to 8 p.m. on weekdays and provides quiet study rooms for group work.",
                'audio_path' => null,
                'question_text' => 'What is the main purpose of the library?',
                'option_a' => 'To sell books',
                'option_b' => 'To provide learning resources',
                'option_c' => 'To host sports events',
                'option_d' => 'To serve meals',
                'correct_answer' => 'B',
            ],
            [
                'category' => 'reading',
                'passage' => "The library at Piksi University offers thousands of books, journals, and digital resources for students. It is open from 8 a.m. to 8 p.m. on weekdays and provides quiet study rooms for group work.",
                'audio_path' => null,
                'question_text' => 'When is the library open on weekdays?',
                'option_a' => '6 a.m. to 6 p.m.',
                'option_b' => '7 a.m. to 7 p.m.',
                'option_c' => '8 a.m. to 8 p.m.',
                'option_d' => '9 a.m. to 9 p.m.',
                'correct_answer' => 'C',
            ],
            [
                'category' => 'reading',
                'passage' => "The library at Piksi University offers thousands of books, journals, and digital resources for students. It is open from 8 a.m. to 8 p.m. on weekdays and provides quiet study rooms for group work.",
                'audio_path' => null,
                'question_text' => 'What can students use for group activities?',
                'option_a' => 'Study rooms',
                'option_b' => 'Cafeteria tables',
                'option_c' => 'Parking space',
                'option_d' => 'Laboratories',
                'correct_answer' => 'A',
            ],
            [
                'category' => 'reading',
                'passage' => "Students at the language center practice reading, listening, speaking, and writing every week. Their progress is evaluated at the end of each month to help them improve faster.",
                'audio_path' => null,
                'question_text' => 'How often are the students evaluated?',
                'option_a' => 'Every week',
                'option_b' => 'Every two weeks',
                'option_c' => 'At the end of each month',
                'option_d' => 'Once a year',
                'correct_answer' => 'C',
            ],
        ];

        foreach ($questions as $question) {
            Question::updateOrCreate(
                [
                    'category' => $question['category'],
                    'question_text' => $question['question_text'],
                ],
                $question
            );
        }
    }
}
