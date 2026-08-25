<?php

namespace Database\Seeders;

use App\Models\PaketSoal;
use App\Models\Question;
use Illuminate\Database\Seeder;

/**
 * Seeder sekali-jalan: memindahkan 140 soal existing (yang paket_soal_id
 * -nya masih null, karena dibuat sebelum fitur paket ada) ke dalam
 * "Paket A" yang baru dibuat otomatis.
 *
 * firstOrCreate dipakai supaya seeder ini aman dijalankan berkali-kali
 * (idempotent) — tidak akan membuat "Paket A" duplikat kalau di-run ulang.
 */
class MigratePaketASeeder extends Seeder
{
    public function run(): void
    {
        $paketA = PaketSoal::firstOrCreate(['nama' => 'Paket A']);

        $updated = Question::whereNull('paket_soal_id')
            ->update(['paket_soal_id' => $paketA->id]);

        $this->command?->info("Paket A dibuat (id: {$paketA->id}). {$updated} soal existing dipindahkan ke Paket A.");
    }
}