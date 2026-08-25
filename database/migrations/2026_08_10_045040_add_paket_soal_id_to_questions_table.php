<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mengikat tiap soal ke satu paket. Nullable dulu supaya migration ini
     * tidak gagal terhadap 140 soal existing — pengisian paket_soal_id untuk
     * soal-soal lama dilakukan lewat seeder (migrasi data), bukan di sini.
     *
     * cascadeOnDelete(): kalau satu paket dihapus, seluruh soal miliknya
     * ikut terhapus (bukan jadi orphan tanpa paket).
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('paket_soal_id')
                ->nullable()
                ->after('id')
                ->constrained('paket_soals')
                ->cascadeOnDelete();

            $table->index(['paket_soal_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('paket_soal_id');
        });
    }
};