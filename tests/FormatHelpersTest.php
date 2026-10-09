<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FormatHelpersTest extends TestCase
{
    public function testRuntimeFormatsMinutesAndSeconds(): void
    {
        self::assertSame('00:31.500', runtime(31.5));
        self::assertSame('01:00.000', runtime(60.0));
        self::assertSame('60:00.000', runtime(3600.0));
    }

    public function testFormatDurationHandlesSecondsMinutesAndHours(): void
    {
        self::assertSame('12.50s', formatDuration(12.5));
        self::assertSame('2m 05.00s', formatDuration(125.0));
        self::assertSame('1h 01m 01.25s', formatDuration(3661.25));
    }
}
