<?php

declare(strict_types=1);

namespace Modules\Capstone\Themes;

use DistortedFusion\BladeComponents\Contracts\ThemeContract;
use DistortedFusion\BladeComponents\Enums\ThemeVariable;
use DistortedFusion\BladeComponents\Enums\ThemeVariant;
use DistortedFusion\BladeComponents\Themes\DefaultTheme;

/**
 * CapstoneTheme maps SITKOM tokens (Modules/Capstone theme.css) onto
 * blade-components CSS variables. Light/dark values mirror theme.css
 * :root and .dark blocks so x-df-* renders identically to mahasiswa reference.
 */
class CapstoneTheme implements ThemeContract
{
    public static function definitions(ThemeVariant $variant): array
    {
        return match ($variant) {
            ThemeVariant::DARK => static::darkColors(),
            ThemeVariant::LIGHT => static::lightColors(),
            default => [],
        };
    }

    private static function lightColors(): array
    {
        return [
            ...DefaultTheme::definitions(ThemeVariant::LIGHT),

            ThemeVariable::BACKGROUND->value => '#F6F8FA',
            ThemeVariable::FOREGROUND->value => '#0D0D12',

            ThemeVariable::SIDEBAR->value => '#FFFFFF',
            ThemeVariable::HEADER->value => '#FFFFFF',

            ThemeVariable::PRIMARY->value => '#0B266E',
            ThemeVariable::PRIMARY_FOREGROUND->value => '#FFFFFF',

            ThemeVariable::SECONDARY->value => '#F6F8FA',
            ThemeVariable::SECONDARY_FOREGROUND->value => '#353849',

            ThemeVariable::ACCENT->value => '#E7E8F0',
            ThemeVariable::ACCENT_FOREGROUND->value => '#0D0D12',

            ThemeVariable::MUTED->value => '#F6F8FA',
            ThemeVariable::MUTED_FOREGROUND->value => '#666D80',

            ThemeVariable::CARD->value => '#FFFFFF',
            ThemeVariable::CARD_FOREGROUND->value => '#0D0D12',

            ThemeVariable::BORDER->value => '#DFE1E7',
            ThemeVariable::INPUT->value => '#DFE1E7',
            ThemeVariable::RING->value => '#5C78B8',

            ThemeVariable::SUCCESS->value => '#287F6E',
            ThemeVariable::SUCCESS_FOREGROUND->value => '#FFFFFF',
            ThemeVariable::INFO->value => '#0C4D6E',
            ThemeVariable::INFO_FOREGROUND->value => '#FFFFFF',
            ThemeVariable::WARNING->value => '#956321',
            ThemeVariable::WARNING_FOREGROUND->value => '#FFFFFF',
            ThemeVariable::DANGER->value => '#DF1C41',
            ThemeVariable::DANGER_FOREGROUND->value => '#FFFFFF',

            ThemeVariable::RADIUS->value => '0.625rem',
            ThemeVariable::RADIUS_INNER->value => '0.5rem',

            ThemeVariable::HEADER_HEIGHT->value => '3.75rem',
            ThemeVariable::SIDEBAR_WIDTH->value => '15rem',
        ];
    }

    private static function darkColors(): array
    {
        return [
            ...DefaultTheme::definitions(ThemeVariant::DARK),

            ThemeVariable::BACKGROUND->value => '#0D0D12',
            ThemeVariable::FOREGROUND->value => '#FFFFFF',

            ThemeVariable::SIDEBAR->value => '#1A1B25',
            ThemeVariable::HEADER->value => '#1A1B25',

            ThemeVariable::PRIMARY->value => '#6F7DA4',
            ThemeVariable::PRIMARY_FOREGROUND->value => '#FFFFFF',

            ThemeVariable::SECONDARY->value => '#272835',
            ThemeVariable::SECONDARY_FOREGROUND->value => '#F8F9FB',

            ThemeVariable::ACCENT->value => '#415086',
            ThemeVariable::ACCENT_FOREGROUND->value => '#F8F9FB',

            ThemeVariable::MUTED->value => '#353849',
            ThemeVariable::MUTED_FOREGROUND->value => '#A4ABB8',

            ThemeVariable::CARD->value => '#1A1B25',
            ThemeVariable::CARD_FOREGROUND->value => '#FFFFFF',

            ThemeVariable::BORDER->value => '#272835',
            ThemeVariable::INPUT->value => '#272835',
            ThemeVariable::RING->value => '#6F7DA4',

            ThemeVariable::SUCCESS->value => '#40C4AA',
            ThemeVariable::SUCCESS_FOREGROUND->value => '#0D0D12',
            ThemeVariable::INFO->value => '#33CFFF',
            ThemeVariable::INFO_FOREGROUND->value => '#0D0D12',
            ThemeVariable::WARNING->value => '#FFBD4C',
            ThemeVariable::WARNING_FOREGROUND->value => '#0D0D12',
            ThemeVariable::DANGER->value => '#ED8296',
            ThemeVariable::DANGER_FOREGROUND->value => '#0D0D12',
        ];
    }
}
