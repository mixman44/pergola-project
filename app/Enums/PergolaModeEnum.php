<?php

namespace App\Enums;

enum PergolaModeEnum: string
{
    case DEFAULT = 'default';
    case CUSTOM = 'custom';
    case TWO_IMAGE = '2image';

    /**
     * Retourne le texte lisible associé à chaque mode.
     */
    public function label(): string
    {
        return match($this) {
            self::DEFAULT => 'Description par défaut',
            self::CUSTOM => 'Description personnalisée',
            self::TWO_IMAGE => '2 images',
        };
    }

    /**
     * Retourne les options prêtes pour un select HTML.
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn($case) => [
                $case->value => $case->label()
            ])
            ->toArray();
    }

    /**
     * Retourne uniquement les valeurs brutes de l'enum.
     */
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, self::cases());
    }
}
