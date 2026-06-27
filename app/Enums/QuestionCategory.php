<?php

namespace App\Enums;

enum QuestionCategory: string
{
    case LISTENING = 'listening';
    case STRUCTURE = 'structure';
    case READING = 'reading';

    public function label(): string
    {
        return match($this) {
            self::LISTENING => 'Listening',
            self::STRUCTURE => 'Structure & Written Expression',
            self::READING => 'Reading Comprehension',
        };
    }

    public static function values(): array
    {
        return array_map(fn(self $case) => $case->value, self::cases());
    }

    public static function labels(): array
    {
        return array_map(fn(self $case) => $case->label(), self::cases());
    }
}
