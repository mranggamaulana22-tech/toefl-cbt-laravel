<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel induk "Paket Soal" — semacam bank soal mandiri.
     * Setiap paket harus berisi 140 soal (50 listening, 40 structure, 50 reading)
     * sebelum bisa dipakai untuk sesi ujian. Kelengkapan dihitung on-the-fly
     * lewat relasi ke questions, bukan disimpan sebagai kolom, supaya tidak
     * pernah "telat sync" kalau ada soal yang ditambah/dihapus manual.
     */
    public function up(): void
    {
        Schema::create('paket_soals', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_soals');
    }
};