<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class BenchmarkHistoryTest extends TestCase
{
    public function testHistoryViewEscapesMachineNamesAndRendersRecordedPoints(): void
    {
        $history = [[
            'run_date' => '2026-10-01 12:00:00',
            'hostname' => '<script>alert(1)</script>',
            'machine_id' => 2,
            'run_id' => 10,
            'steps_per_second' => 4.25,
        ]];
        $datasets = [];
        $models = [];
        $machines = [['machine_id' => 2, 'hostname' => '<script>alert(1)</script>']];
        $datasetId = null;
        $modelId = null;
        $machineId = null;
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', '');
        }
        ob_start();
        require __DIR__ . '/../app/Views/history.php';
        $html = (string) ob_get_clean();

        self::assertStringContainsString('Benchmark History', $html);
        self::assertStringContainsString('4.250 steps/sec', $html);
        self::assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
        self::assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function testHistoryViewShowsEmptyState(): void
    {
        $history = [];
        $datasets = [];
        $models = [];
        $machines = [];
        $datasetId = null;
        $modelId = null;
        $machineId = null;
        if (!defined('BASE_PATH')) {
            define('BASE_PATH', '');
        }
        ob_start();
        require __DIR__ . '/../app/Views/history.php';
        $html = (string) ob_get_clean();

        self::assertStringContainsString('No valid benchmark throughput measurements', $html);
    }
}
