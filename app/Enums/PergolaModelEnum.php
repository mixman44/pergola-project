<?php

namespace App\Enums;

enum PergolaModelEnum: string
{
    case GEMINI_FLASH = 'gemini-2.5-flash-image';
    case GEMINI_PRO = 'gemini-3-pro-image-preview';

    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }

}
