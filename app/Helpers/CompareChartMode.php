<?php
declare(strict_types=1);

namespace App\Helpers;

final class CompareChartMode
{
    public static function normalize(mixed $value): string
    {
        return in_array($value, ['average', 'runs'], true) ? $value : 'average';
    }
}
