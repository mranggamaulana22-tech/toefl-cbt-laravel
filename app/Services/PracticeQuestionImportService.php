<?php

namespace App\Services;

use App\Models\PracticeQuestion;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipArchive;

/**
 * Import massal soal latihan dari file Excel (.xlsx), opsional dibundel
 * bersama file-file audio dalam .zip (folder audio/ di dalamnya).
 *
 * Format kolom template SENGAJA dibuat identik dengan
 * QuestionExportService::exportPracticeQuestionsXlsx() — supaya admin bisa
 * export data lama, edit di Excel, lalu import lagi tanpa perlu menyusun
 * ulang formatnya dari nol.
 */
class PracticeQuestionImportService
{
    private const HEADERS = ['No', 'Kategori', 'Passage/Bacaan', 'Nama File Audio', 'Transkrip Audio', 'Pertanyaan', 'Pilihan A', 'Pilihan B', 'Pilihan C', 'Pilihan D', 'Jawaban Benar'];
    private const VALID_CATEGORIES = ['listening', 'structure', 'reading'];
    private const VALID_ANSWERS = ['A', 'B', 'C', 'D'];

    /**
     * Satu contoh lengkap per kategori, ditulis di baris pertama tiap
     * kategori pada template supaya admin punya acuan konkret. Baris ini
     * dikenali & di-skip otomatis saat import (lihat isExampleRow()).
     */
    private const EXAMPLES = [
        'listening' => [
            'audio_filename' => 'listening_01.mp3',
            'audio_transcript' => 'Woman: Have you finished the report for tomorrow\'s meeting? Man: Almost, I just need to double-check the figures in the last section.',
            'question_text' => 'What does the man imply about the report?',
            'option_a' => 'It is completely finished',
            'option_b' => 'It still needs minor checking',
            'option_c' => 'He has not started it',
            'option_d' => 'It was due last week',
            'correct_answer' => 'B',
        ],
        'structure' => [
            'question_text' => 'By the time the workshop begins, all participants ____ their registration forms.',
            'option_a' => 'will have submitted',
            'option_b' => 'submit',
            'option_c' => 'are submitting',
            'option_d' => 'submitted',
            'correct_answer' => 'A',
        ],
        'reading' => [
            'passage' => 'Coral reefs, often called the rainforests of the sea, support roughly a quarter of all marine species despite covering less than one percent of the ocean floor. Rising sea temperatures, however, have caused widespread coral bleaching in recent decades, threatening this delicate ecosystem.',
            'question_text' => 'According to the passage, what is a major threat to coral reefs?',
            'option_a' => 'Overpopulation of marine species',
            'option_b' => 'Rising sea temperatures',
            'option_c' => 'Their small size',
            'option_d' => 'Lack of sunlight',
            'correct_answer' => 'B',
        ],
    ];

    /**
     * Buat file template dengan 140 baris SUDAH PRE-FILLED sesuai porsi
     * kategori (default 50 listening, 40 structure, 50 reading, mengikuti
     * config('exam.exam.sections') supaya konsisten dengan porsi ujian
     * resmi — sesuai keinginan admin: soal latihan dibuat semirip mungkin
     * dengan ujian TOEFL). Admin tinggal isi sisa kolomnya per baris,
     * tidak perlu mengetik ulang nama kategori satu-satu.
     */
    public function generateTemplate(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Soal Latihan');

        $sheet->fromArray(self::HEADERS, null, 'A1');

        $headerRange = 'A1:' . chr(64 + count(self::HEADERS)) . '1';
        $sheet->getStyle($headerRange)->getFont()->setBold(true);
        $sheet->getStyle($headerRange)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('E5E7EB');
        $sheet->getStyle($headerRange)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Ambil porsi per kategori dari config yang sama dipakai ujian resmi
        // (default: listening 50, structure 40, reading 50 = 140 total),
        // supaya jumlah soal latihan tetap selaras kalau config ini diubah.
        $sectionCounts = config('exam.exam.sections', ['listening' => 50, 'structure' => 40, 'reading' => 50]);
        $sectionOrder = config('exam.exam.section_order', ['listening', 'structure', 'reading']);

        $row = 2;
        foreach ($sectionOrder as $category) {
            $count = (int) ($sectionCounts[$category] ?? 0);
            $example = self::EXAMPLES[$category] ?? null;

            for ($i = 1; $i <= $count; $i++) {
                // Baris pertama tiap kategori diisi contoh lengkap yang bisa
                // ditimpa admin. Baris ini otomatis dilewati saat import
                // selama isinya masih persis sama dengan contoh bawaan
                // (lihat isExampleRow()), supaya tidak nyelip jadi soal
                // sungguhan kalau lupa dihapus/diedit.
                if ($i === 1 && $example) {
                    $sheet->fromArray([
                        'CONTOH', $category,
                        $example['passage'] ?? ($category === 'reading' ? '' : '-'),
                        $example['audio_filename'] ?? ($category === 'listening' ? '' : '-'),
                        $example['audio_transcript'] ?? ($category === 'listening' ? '' : '-'),
                        $example['question_text'], $example['option_a'], $example['option_b'],
                        $example['option_c'], $example['option_d'], $example['correct_answer'],
                    ], null, 'A' . $row);
                    $sheet->getStyle('A' . $row . ':K' . $row)->getFont()->setItalic(true)->getColor()->setRGB('9CA3AF');
                    $row++;
                    continue;
                }

                $sheet->fromArray([
                    $i,
                    $category,
                    $category === 'reading' ? '' : '-',
                    $category === 'listening' ? '' : '-',
                    $category === 'listening' ? '' : '-',
                    '', '', '', '', '', '',
                ], null, 'A' . $row);
                $row++;
            }
        }

        $lastRow = $row - 1;

        // Dropdown validasi tetap dipasang sebagai jaring pengaman kalau
        // admin mengganti kategori atau salah ketik jawaban benar.
        $this->applyDropdownValidation($sheet, 'B', 2, $lastRow, self::VALID_CATEGORIES);
        $this->applyDropdownValidation($sheet, 'K', 2, $lastRow, self::VALID_ANSWERS);

        foreach (range('A', 'K') as $columnId) {
            $sheet->getColumnDimension($columnId)->setAutoSize(true);
        }
        foreach (['C', 'E', 'F'] as $columnId) {
            $sheet->getColumnDimension($columnId)->setAutoSize(false);
            $sheet->getColumnDimension($columnId)->setWidth(50);
        }
        $sheet->getStyle('C:F')->getAlignment()->setWrapText(true);
        $sheet->freezePane('A2');

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'template_soal_latihan.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function applyDropdownValidation(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet, string $column, int $startRow, int $endRow, array $options): void
    {
        $formula = '"' . implode(',', $options) . '"';

        for ($row = $startRow; $row <= $endRow; $row++) {
            $validation = $sheet->getCell($column . $row)->getDataValidation();
            $validation->setType(DataValidation::TYPE_LIST);
            $validation->setErrorStyle(DataValidation::STYLE_STOP);
            $validation->setAllowBlank(true);
            $validation->setShowDropDown(true);
            $validation->setShowErrorMessage(true);
            $validation->setErrorTitle('Nilai tidak valid');
            $validation->setError('Pilih salah satu dari daftar dropdown.');
            $validation->setFormula1($formula);
        }
    }

    /**
     * Proses file yang diupload admin (.xlsx langsung, atau .zip berisi
     * 1 file .xlsx + folder audio/).
     *
     * Validasi bersifat all-or-nothing: seluruh baris divalidasi dulu;
     * kalau ada satu saja yang error, TIDAK ADA yang disimpan ke database.
     * Ini mencegah data setengah-jadi/rusak nyelip masuk hanya karena satu
     * baris salah ketik.
     *
     * @return array{success: bool, created: int, audio_attached: int, audio_missing: array<int,string>, errors: array<int,string>}
     */
    public function import(UploadedFile $file): array
    {
        // Kode referensi unik untuk kasus ini — ditampilkan ke admin DAN
        // dicatat di log, supaya kalau admin lapor "gagal import", developer
        // tinggal `grep KODE_INI storage/logs/laravel.log` untuk langsung
        // menemukan detail lengkapnya tanpa harus menyisir log yang panjang.
        $referenceCode = 'IMPORT-' . strtoupper(substr(uniqid(), -6));
        $originalName = $file->getClientOriginalName();

        $tempDir = storage_path('app/temp_import_' . uniqid());
        mkdir($tempDir, 0755, true);

        try {
            $audioFiles = []; // ['listening_02.mp3' => '/absolute/tmp/path/listening_02.mp3']
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'zip') {
                $zip = new ZipArchive();
                $openResult = $zip->open($file->getPathname());

                if ($openResult !== true) {
                    Log::error("[{$referenceCode}] Gagal membuka file ZIP saat import soal latihan.", [
                        'file_name' => $originalName,
                        'file_size_kb' => round($file->getSize() / 1024, 1),
                        'zip_error_code' => $openResult,
                    ]);

                    return $this->failure(
                        'file_format',
                        ['File ZIP tidak bisa dibuka. Kemungkinan file rusak saat diupload, atau bukan file ZIP yang valid. Coba compress ulang filenya dari awal, lalu upload kembali.']
                    );
                }

                $zip->extractTo($tempDir);
                $zip->close();

                $xlsxPath = $this->findXlsxInDirectory($tempDir);
                if (! $xlsxPath) {
                    Log::error("[{$referenceCode}] ZIP berhasil dibuka tapi tidak ada file .xlsx di dalamnya.", [
                        'file_name' => $originalName,
                    ]);

                    return $this->failure(
                        'file_format',
                        ['File ZIP yang diupload tidak berisi file Excel (.xlsx) di dalamnya. Pastikan file Excel-nya ikut dimasukkan ke dalam ZIP, sejajar dengan folder audio/.']
                    );
                }

                $audioFiles = $this->collectAudioFiles($tempDir);
            } else {
                $xlsxPath = $tempDir . '/upload.xlsx';
                $file->move($tempDir, 'upload.xlsx');
            }

            try {
                $spreadsheet = IOFactory::load($xlsxPath);
            } catch (\Throwable $e) {
                Log::error("[{$referenceCode}] Gagal membaca file Excel saat import soal latihan.", [
                    'file_name' => $originalName,
                    'file_size_kb' => round($file->getSize() / 1024, 1),
                    'exception' => $e->getMessage(),
                    'exception_class' => get_class($e),
                ]);

                return $this->failure(
                    'file_format',
                    ['File Excel tidak bisa dibaca oleh sistem. Kemungkinan penyebabnya: file rusak, formatnya bukan .xlsx asli (misalnya file .xls lama atau .csv yang cuma diganti namanya), atau file sedang dibuka di program lain saat diupload. Coba buka & simpan ulang filenya sebagai .xlsx, lalu upload kembali.']
                );
            }

            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);

            // Baris pertama = header, dilewati.
            array_shift($rows);

            $errors = [];
            $parsedRows = [];

            foreach ($rows as $index => $row) {
                $excelRowNumber = $index + 2; // +2: offset header + index dimulai dari 0

                // Lewati baris yang benar-benar kosong (spreadsheet sering punya baris kosong trailing).
                if ($this->isRowEmpty($row)) {
                    continue;
                }

                // Lewati baris contoh bawaan template kalau admin tidak
                // mengubahnya sama sekali — mencegah data contoh nyelip
                // jadi soal sungguhan kalau admin lupa menghapusnya.
                if ($this->isUntouchedExampleRow($row)) {
                    continue;
                }

                [$parsed, $rowErrors] = $this->validateRow($row, $excelRowNumber, $audioFiles);

                if (! empty($rowErrors)) {
                    $errors = array_merge($errors, $rowErrors);
                    continue;
                }

                $parsedRows[] = $parsed;
            }

            if (! empty($errors)) {
                Log::info("[{$referenceCode}] Import soal latihan ditolak karena validasi baris gagal.", [
                    'file_name' => $originalName,
                    'total_error_rows' => count($errors),
                ]);

                return $this->failure('validation', $errors);
            }

            if (empty($parsedRows)) {
                return $this->failure(
                    'file_empty',
                    ['File yang diupload tidak berisi data soal apapun. Pastikan minimal ada satu baris soal yang sudah diisi (baris "CONTOH" yang belum diubah akan otomatis dilewati dan tidak dihitung sebagai data).']
                );
            }

            $created = 0;
            $audioAttached = 0;
            $audioMissing = [];

            DB::transaction(function () use ($parsedRows, $audioFiles, &$created, &$audioAttached, &$audioMissing): void {
                foreach ($parsedRows as $row) {
                    $audioPath = null;

                    if ($row['category'] === 'listening' && $row['audio_filename']) {
                        if (isset($audioFiles[$row['audio_filename']])) {
                            $extension = pathinfo($row['audio_filename'], PATHINFO_EXTENSION) ?: 'mp3';
                            $storedName = 'practice/' . uniqid('audio_') . '.' . $extension;
                            Storage::disk('public')->put($storedName, file_get_contents($audioFiles[$row['audio_filename']]));
                            $audioPath = $storedName;
                            $audioAttached++;
                        } else {
                            $audioMissing[] = "Baris {$row['excel_row']}: file audio \"{$row['audio_filename']}\" tidak ditemukan di dalam ZIP.";
                        }
                    }

                    PracticeQuestion::create([
                        'category' => $row['category'],
                        'passage' => $row['passage'],
                        'audio_path' => $audioPath,
                        'audio_transcript' => $row['audio_transcript'],
                        'question_text' => $row['question_text'],
                        'option_a' => $row['option_a'],
                        'option_b' => $row['option_b'],
                        'option_c' => $row['option_c'],
                        'option_d' => $row['option_d'],
                        'correct_answer' => $row['correct_answer'],
                    ]);

                    $created++;
                }
            });

            Log::info("[{$referenceCode}] Import soal latihan berhasil.", [
                'file_name' => $originalName,
                'created' => $created,
                'audio_attached' => $audioAttached,
                'audio_missing_count' => count($audioMissing),
            ]);

            return [
                'success' => true,
                'created' => $created,
                'audio_attached' => $audioAttached,
                'audio_missing' => $audioMissing,
                'errors' => [],
            ];
        } catch (\Throwable $e) {
            // Jaring pengaman terakhir untuk error yang benar-benar tak
            // terduga (memory limit, permission storage, dll) — supaya
            // admin tidak pernah melihat halaman error 500 polos Laravel
            // untuk fitur ini, dan developer tetap punya jejak lengkap di log.
            Log::error("[{$referenceCode}] Import soal latihan gagal karena error tak terduga.", [
                'file_name' => $originalName,
                'exception' => $e->getMessage(),
                'exception_class' => get_class($e),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return $this->failure(
                'system',
                ["Sistem mengalami kendala teknis saat memproses file ini. Coba upload ulang. Jika masalah terus terjadi, hubungi tim IT dan sebutkan kode berikut: {$referenceCode}"]
            );
        } finally {
            $this->cleanupDirectory($tempDir);
        }
    }

    /**
     * @return array{0: array|null, 1: array<int,string>}
     */
    private function validateRow(array $row, int $excelRowNumber, array $audioFiles): array
    {
        $errors = [];

        $category = strtolower(trim((string) ($row[1] ?? '')));
        $passage = trim((string) ($row[2] ?? ''));
        $passage = in_array($passage, ['', '-'], true) ? null : $passage;
        $audioFilename = trim((string) ($row[3] ?? ''));
        $audioFilename = in_array($audioFilename, ['', '-'], true) ? null : $audioFilename;
        $audioTranscript = trim((string) ($row[4] ?? ''));
        $audioTranscript = in_array($audioTranscript, ['', '-'], true) ? null : $audioTranscript;
        $questionText = trim((string) ($row[5] ?? ''));
        $optionA = trim((string) ($row[6] ?? ''));
        $optionB = trim((string) ($row[7] ?? ''));
        $optionC = trim((string) ($row[8] ?? ''));
        $optionD = trim((string) ($row[9] ?? ''));
        $correctAnswer = strtoupper(trim((string) ($row[10] ?? '')));

        if (! in_array($category, self::VALID_CATEGORIES, true)) {
            $errors[] = "Baris {$excelRowNumber}: kategori \"{$category}\" tidak valid (harus listening/structure/reading).";
        }

        if ($questionText === '') {
            $errors[] = "Baris {$excelRowNumber}: kolom Pertanyaan tidak boleh kosong.";
        }

        foreach (['A' => $optionA, 'B' => $optionB, 'C' => $optionC, 'D' => $optionD] as $label => $value) {
            if ($value === '') {
                $errors[] = "Baris {$excelRowNumber}: kolom Pilihan {$label} tidak boleh kosong.";
            }
        }

        if (! in_array($correctAnswer, self::VALID_ANSWERS, true)) {
            $errors[] = "Baris {$excelRowNumber}: Jawaban Benar \"{$correctAnswer}\" tidak valid (harus A/B/C/D).";
        }

        if ($category === 'reading' && ! $passage) {
            $errors[] = "Baris {$excelRowNumber}: kategori reading wajib mengisi kolom Passage/Bacaan.";
        }

        if ($category === 'listening' && ! $audioTranscript) {
            $errors[] = "Baris {$excelRowNumber}: kategori listening wajib mengisi kolom Transkrip Audio.";
        }

        if (! empty($errors)) {
            return [null, $errors];
        }

        return [[
            'excel_row' => $excelRowNumber,
            'category' => $category,
            'passage' => $passage,
            'audio_filename' => $audioFilename,
            'audio_transcript' => $audioTranscript,
            'question_text' => $questionText,
            'option_a' => $optionA,
            'option_b' => $optionB,
            'option_c' => $optionC,
            'option_d' => $optionD,
            'correct_answer' => $correctAnswer,
        ], []];
    }

    private function isUntouchedExampleRow(array $row): bool
    {
        $marker = trim((string) ($row[0] ?? ''));
        if (strtoupper($marker) !== 'CONTOH') {
            return false;
        }

        $category = strtolower(trim((string) ($row[1] ?? '')));
        $example = self::EXAMPLES[$category] ?? null;

        if (! $example) {
            return false;
        }

        $questionText = trim((string) ($row[5] ?? ''));

        // Cukup bandingkan kolom pertanyaan saja — kalau admin sudah edit
        // pertanyaannya (meski kolom lain masih sama), anggap baris ini
        // sudah "disentuh" dan tetap diproses sebagai data biasa.
        return $questionText === $example['question_text'];
    }

    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if (trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    private function findXlsxInDirectory(string $dir): ?string
    {
        foreach (glob($dir . '/*.xlsx') as $path) {
            return $path;
        }

        // Kalau xlsx-nya ada di dalam subfolder (misal ikut ter-zip dengan folder pembungkus).
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir));
        foreach ($iterator as $fileInfo) {
            if (strtolower($fileInfo->getExtension()) === 'xlsx') {
                return $fileInfo->getPathname();
            }
        }

        return null;
    }

    /**
     * @return array<string,string> nama_file => path_absolut_di_temp
     */
    private function collectAudioFiles(string $dir): array
    {
        $audioDir = $dir . '/audio';
        if (! is_dir($audioDir)) {
            return [];
        }

        $files = [];
        foreach (scandir($audioDir) as $name) {
            if ($name === '.' || $name === '..') {
                continue;
            }
            $files[$name] = $audioDir . '/' . $name;
        }

        return $files;
    }

    private function cleanupDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($iterator as $fileInfo) {
            $fileInfo->isDir() ? @rmdir($fileInfo->getPathname()) : @unlink($fileInfo->getPathname());
        }

        @rmdir($dir);
    }

    private function failure(string $type, array $errors): array
    {
        return [
            'success' => false,
            'created' => 0,
            'audio_attached' => 0,
            'audio_missing' => [],
            'error_type' => $type,
            'errors' => $errors,
        ];
    }
}