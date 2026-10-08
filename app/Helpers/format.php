<?php
declare(strict_types=1);

function runtime(float $seconds): string
{
    $minutes = floor($seconds / 60);
    $remaining = $seconds - ($minutes * 60);

    return sprintf('%02d:%06.3f', $minutes, $remaining);
}
