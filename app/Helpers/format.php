<?php
declare(strict_types=1);

function runtime(float $seconds): string
{
    $minutes = floor($seconds / 60);
    $remaining = $seconds - ($minutes * 60);

    return sprintf('%02d:%06.3f', $minutes, $remaining);
}
function formatDuration(float $seconds): string
{
    $hours = (int) floor($seconds / 3600);
    $minutes = (int) floor(fmod($seconds, 3600) / 60);
    $remainingSeconds = fmod($seconds, 60);

    if ($hours > 0) {
        return sprintf(
            '%dh %02dm %05.2fs',
            $hours,
            $minutes,
            $remainingSeconds
        );
    }

    if ($minutes > 0) {
        return sprintf(
            '%dm %05.2fs',
            $minutes,
            $remainingSeconds
        );
    }

    return sprintf('%.2fs', $remainingSeconds);
}
