<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            QuestionSeeder::class,         // Memasukkan 140 Soal Ujian
            PracticeQuestionSeeder::class, // Memasukkan 140 Soal Latihan
        ]);
    }
}