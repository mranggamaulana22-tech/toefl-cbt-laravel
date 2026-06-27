<?php

namespace App\Enums;

enum AiStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case DONE = 'done';
    case FAILED = 'failed';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pending',
            self::PROCESSING => 'Processing',
            self::DONE => 'Done',
            self::FAILED => 'Failed',
        };
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::DONE, self::FAILED]);
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
