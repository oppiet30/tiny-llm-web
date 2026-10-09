<?php
declare(strict_types=1);

use App\Helpers\BenchmarkStatistics;
use PHPUnit\Framework\TestCase;

final class BenchmarkStatisticsTest extends TestCase
{
    public function testGroupsRunsAndCalculatesSummaryStatistics(): void
    {
        $stats = BenchmarkStatistics::byMachine([
            ['machine_id' => 1, 'hostname' => 'optiplex', 'steps_per_second' => 6.2],
            ['machine_id' => 1, 'hostname' => 'optiplex', 'steps_per_second' => 6.4],
            ['machine_id' => 2, 'hostname' => 'dell', 'steps_per_second' => 3.0],
            ['machine_id' => 2, 'hostname' => 'dell', 'steps_per_second' => 3.2],
        ]);

        self::assertCount(2, $stats);
        self::assertSame(1, $stats[0]['machine_id']);
        self::assertSame(2, $stats[0]['count']);
        self::assertEqualsWithDelta(6.3, $stats[0]['mean'], 1e-9);
        self::assertEqualsWithDelta(6.2, $stats[0]['min'], 1e-9);
        self::assertEqualsWithDelta(6.4, $stats[0]['max'], 1e-9);
        self::assertEqualsWithDelta(sqrt(0.02), $stats[0]['stddev'], 1e-9);
        self::assertEqualsWithDelta(3.1, $stats[1]['mean'], 1e-9);
    }

    public function testExcludesNullZeroAndNonNumericMeasurements(): void
    {
        $stats = BenchmarkStatistics::byMachine([
            ['machine_id' => 1, 'hostname' => 'one', 'steps_per_second' => null],
            ['machine_id' => 1, 'hostname' => 'one', 'steps_per_second' => 0],
            ['machine_id' => 1, 'hostname' => 'one', 'steps_per_second' => 'not-a-number'],
        ]);

        self::assertSame([], $stats);
    }

    public function testSingleRunHasNoSampleStandardDeviation(): void
    {
        $stats = BenchmarkStatistics::byMachine([
            ['machine_id' => 7, 'hostname' => 'Mini', 'steps_per_second' => 1.4],
        ]);

        self::assertCount(1, $stats);
        self::assertSame(1, $stats[0]['count']);
        self::assertNull($stats[0]['stddev']);
    }

    public function testEmptyInputReturnsNoStatistics(): void
    {
        self::assertSame([], BenchmarkStatistics::byMachine([]));
    }
}
