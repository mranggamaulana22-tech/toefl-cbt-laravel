<?php

namespace App\Enums;

enum Jurusan: string
{
    case TEK = 'TEK';
    case TERM = 'TERM';
    case TEKNIK_OTOMOTIF = 'Teknik Otomotif';
    case AKUNTANSI = 'Akuntansi';
    case MSDM = 'MSDM';

    public static function labels(): array
    {
        return [
            self::TEK->value => 'TEK - Teknik Elektronika dan Informatika Komputer',
            self::TERM->value => 'TERM - Teknik Elektro dan Rekam Medis',
            self::TEKNIK_OTOMOTIF->value => 'Teknik Otomotif',
            self::AKUNTANSI->value => 'Akuntansi',
            self::MSDM->value => 'MSDM - Manajemen Sumber Daya Manusia',
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}