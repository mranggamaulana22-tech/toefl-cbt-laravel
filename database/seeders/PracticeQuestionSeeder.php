<?php

namespace Database\Seeders;

use App\Models\PracticeQuestion;
use App\Enums\QuestionCategory;
use Illuminate\Database\Seeder;

class PracticeQuestionSeeder extends Seeder
{
    public function run(): void
    {

        // 1. SEED LISTENING (Ambil jatah soal baris 51 - 100)
        $this->seedFromCsv(
            base_path('database/data/listening.csv'), 
            QuestionCategory::LISTENING, 
            51, 100
        );

        // 2. SEED STRUCTURE (Ambil jatah soal baris 41 - 80)
        $this->seedFromCsv(
            base_path('database/data/structure.csv'), 
            QuestionCategory::STRUCTURE, 
            41, 80
        );

        // 3. SEED READING (Ambil jatah soal baris 51 - 100)
        $this->seedFromCsv(
            base_path('database/data/reading.csv'), 
            QuestionCategory::READING, 
            51, 100
        );
    }

    private function seedFromCsv($filePath, $category, $startRow, $endRow)
    {
        if (!file_exists($filePath)) return;

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file);
        
        $currentRow = 1;
        while (($row = fgetcsv($file, 4000, ",")) !== FALSE) {
            if ($currentRow >= $startRow && $currentRow <= $endRow) {
                
                if ($category === QuestionCategory::LISTENING) {
                    PracticeQuestion::create([
                        'category' => $category,
                        'audio_path' => $row[2],
                        'audio_transcript' => $row[8],
                        'question_text' => $row[9],
                        'option_a' => $row[4],
                        'option_b' => $row[5],
                        'option_c' => $row[6],
                        'option_d' => $row[7],
                        'correct_answer' => strtoupper($row[3]),
                    ]);
                } 
                elseif ($category === QuestionCategory::STRUCTURE) {
                    PracticeQuestion::create([
                        'category' => $category,
                        'question_text' => $row[4],
                        'option_a' => $row[5],
                        'option_b' => $row[6],
                        'option_c' => $row[7],
                        'option_d' => $row[8],
                        'correct_answer' => strtoupper($row[9]),
                    ]);
                } 
                elseif ($category === QuestionCategory::READING) {
                    PracticeQuestion::create([
                        'category' => $category,
                        'question_text' => $row[3] . "\n\n" . $row[4],
                        'option_a' => $row[5],
                        'option_b' => $row[6],
                        'option_c' => $row[7],
                        'option_d' => $row[8],
                        'correct_answer' => strtoupper($row[9]),
                    ]);
                }
            }
            $currentRow++;
            if ($currentRow > $endRow) break;
        }
        fclose($file);
    }
}