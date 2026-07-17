<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Enums\QuestionCategory;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {

        // 1. SEED LISTENING (Ambil 50 Soal pertama)
        $this->seedFromCsv(
            base_path('database/data/listening.csv'), 
            QuestionCategory::LISTENING, 
            1, 50
        );

        // 2. SEED STRUCTURE (Ambil 40 Soal pertama)
        $this->seedFromCsv(
            base_path('database/data/structure.csv'), 
            QuestionCategory::STRUCTURE, 
            1, 40
        );

        // 3. SEED READING (Ambil 50 Soal pertama)
        $this->seedFromCsv(
            base_path('database/data/reading.csv'), 
            QuestionCategory::READING, 
            1, 50
        );
    }

    private function seedFromCsv($filePath, $category, $startRow, $endRow)
    {
        if (!file_exists($filePath)) return;

        $file = fopen($filePath, 'r');
        $header = fgetcsv($file); // Skip baris pertama (header)
        
        $currentRow = 1;
        while (($row = fgetcsv($file, 4000, ",")) !== FALSE) {
            // Jalankan hanya jika baris berada dalam jatah limits
            if ($currentRow >= $startRow && $currentRow <= $endRow) {
                
                // Pemetaan kolom disesuaikan dengan isi file CSV kamu
                if ($category === QuestionCategory::LISTENING) {
                    Question::create([
                        'category' => $category,
                        'audio_path' => $row[2], // Nama File Audio
                        'audio_transcript' => $row[8], // Audio Script
                        'question_text' => $row[9], // Pertanyaan
                        'option_a' => $row[4],
                        'option_b' => $row[5],
                        'option_c' => $row[6],
                        'option_d' => $row[7],
                        'correct_answer' => strtoupper($row[3]), // Kunci Jawaban (A, B, C, D)
                    ]);
                } 
                elseif ($category === QuestionCategory::STRUCTURE) {
                    Question::create([
                        'category' => $category,
                        'question_text' => $row[4], // Kalimat Soal / Stem
                        'option_a' => $row[5],
                        'option_b' => $row[6],
                        'option_c' => $row[7],
                        'option_d' => $row[8],
                        'correct_answer' => strtoupper($row[9]), // Kunci Jawaban
                    ]);
                } 
                elseif ($category === QuestionCategory::READING) {
                    Question::create([
                        'category' => $category,
                        'question_text' => $row[3] . "\n\n" . $row[4], // Gabungkan Passage Text + Pertanyaan
                        'option_a' => $row[5],
                        'option_b' => $row[6],
                        'option_c' => $row[7],
                        'option_d' => $row[8],
                        'correct_answer' => strtoupper($row[9]), // Kunci Jawaban
                    ]);
                }
            }
            $currentRow++;
            if ($currentRow > $endRow) break;
        }
        fclose($file);
    }
}